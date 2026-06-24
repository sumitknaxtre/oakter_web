<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCheckoutRequest;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\CheckoutAddressService;
use App\Services\CheckoutLookupService;
use App\Services\CheckoutUserService;
use App\Services\CouponService;
use App\Services\PendingCheckoutService;
use App\Support\ProductCatalog;
use App\Support\OrderFulfillmentStatus;
use App\Support\OrderPaymentStatus;
use App\Support\RazorpayPaymentMethod;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use InvalidArgumentException;
use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;

class CheckoutController extends Controller
{
    public function __construct(
        private readonly CheckoutUserService $checkoutUserService,
        private readonly CheckoutAddressService $checkoutAddressService,
        private readonly CheckoutLookupService $checkoutLookupService,
        private readonly CouponService $couponService,
        private readonly PendingCheckoutService $pendingCheckoutService,
    ) {}

    public function show(string $product): View
    {
        try {
            $product = ProductCatalog::get($product);
        } catch (InvalidArgumentException) {
            abort(404);
        }

        $dbProduct = Product::query()->findOrFail($product['id']);

        if (! $dbProduct->isInStock()) {
            return redirect()
                ->to(ProductCatalog::buyRoute($product['slug']))
                ->with('product_unavailable', $dbProduct->name);
        }

        abort_unless(config('razorpay.key_id') && config('razorpay.key_secret'), 503, 'Payment gateway is not configured.');

        return view('website.checkout', [
            'product' => $product,
            'states' => config('india.states'),
            'razorpayKey' => config('razorpay.key_id'),
        ]);
    }

    public function lookup(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255'],
        ]);

        return response()->json(
            $this->checkoutLookupService->lookupByEmail($validated['email'])
        );
    }

    public function applyCoupon(string $product, Request $request): JsonResponse
    {
        $validated = $request->validate([
            'coupon_code' => ['required', 'string', 'max:50'],
        ]);

        try {
            $productData = ProductCatalog::get($product);
        } catch (InvalidArgumentException) {
            abort(404);
        }

        $dbProduct = Product::query()->findOrFail($productData['id']);
        $dbProduct->ensureInStock();

        try {
            $coupon = $this->couponService->resolveForProduct($validated['coupon_code'], $dbProduct);
        } catch (ValidationException $exception) {
            return response()->json([
                'message' => $exception->errors()['coupon_code'][0] ?? 'This discount code is not valid.',
            ], 422);
        }

        $pricing = $this->couponService->calculatePricing($dbProduct, $coupon);

        return response()->json($this->couponPayload($coupon, $pricing));
    }

    public function createOrder(string $product, StoreCheckoutRequest $request): JsonResponse
    {
        abort_unless(config('razorpay.key_id') && config('razorpay.key_secret'), 503, 'Payment gateway is not configured.');

        try {
            $productData = ProductCatalog::get($product);
        } catch (InvalidArgumentException) {
            abort(404);
        }

        $dbProduct = Product::query()->findOrFail($productData['id']);
        $dbProduct->ensureInStock();
        $validated = $request->validated();

        $coupon = null;
        if (! empty($validated['coupon_code'])) {
            $coupon = $this->couponService->resolveForProduct($validated['coupon_code'], $dbProduct);
        }

        $pricing = $this->couponService->calculatePricing($dbProduct, $coupon);

        $user = $this->checkoutUserService->resolveFromCheckout($validated);
        $addressData = $this->checkoutAddressService->persistForCheckout($user, $validated);

        $api = new Api(config('razorpay.key_id'), config('razorpay.key_secret'));

        try {
            $razorpayOrder = $api->order->create([
                'receipt' => 'oakter_'.Str::lower(Str::ulid()),
                'amount' => $pricing['amount_paise'],
                'currency' => $dbProduct->currency,
                'notes' => [
                    'product_key' => $dbProduct->config_key,
                ],
            ]);
        } catch (\Throwable $exception) {
            report($exception);

            return response()->json([
                'message' => 'Unable to create Razorpay order. Please try again.',
            ], 500);
        }

        $this->pendingCheckoutService->store($razorpayOrder['id'], [
            'user_id' => $user->id,
            'product_id' => $dbProduct->id,
            'product_snapshot' => $dbProduct->toSnapshot(),
            'subtotal_paise' => $pricing['subtotal_paise'],
            'discount_paise' => $pricing['discount_paise'],
            'amount_paise' => $pricing['amount_paise'],
            'coupon_id' => $coupon?->id,
            'coupon_snapshot' => $coupon?->toSnapshot(),
            'currency' => $dbProduct->currency,
            ...$addressData,
        ]);

        return response()->json([
            'razorpay_order_id' => $razorpayOrder['id'],
            'amount' => $pricing['amount_paise'],
            'currency' => $dbProduct->currency,
            'key' => config('razorpay.key_id'),
            'name' => config('razorpay.company_name'),
            'description' => $dbProduct->name,
            'prefill' => [
                'name' => trim($validated['first_name'].' '.$validated['last_name']),
                'email' => $validated['email'],
                'contact' => $validated['phone'],
            ],
            'notes' => [
                'product' => $dbProduct->config_key,
            ],
            'theme' => [
                'color' => '#171717',
            ],
            'verify_url' => route('website.checkout.verify'),
        ]);
    }

    public function verify(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'razorpay_order_id' => ['required', 'string'],
            'razorpay_payment_id' => ['required', 'string'],
            'razorpay_signature' => ['required', 'string'],
        ]);

        $existingOrder = Order::query()
            ->where('razorpay_order_id', $validated['razorpay_order_id'])
            ->first();

        if ($existingOrder !== null) {
            return response()->json([
                'redirect' => route('website.checkout.success', $existingOrder),
            ]);
        }

        $api = new Api(config('razorpay.key_id'), config('razorpay.key_secret'));

        try {
            $api->utility->verifyPaymentSignature([
                'razorpay_order_id' => $validated['razorpay_order_id'],
                'razorpay_payment_id' => $validated['razorpay_payment_id'],
                'razorpay_signature' => $validated['razorpay_signature'],
            ]);
        } catch (SignatureVerificationError) {
            return response()->json(['message' => 'Payment verification failed.'], 422);
        }

        $pending = $this->pendingCheckoutService->pull($validated['razorpay_order_id']);

        if ($pending === null) {
            return response()->json([
                'message' => 'Checkout session expired. Please try again.',
            ], 422);
        }

        $dbProduct = Product::query()->findOrFail($pending['product_id']);
        $dbProduct->ensureInStock();

        $coupon = null;
        if (! empty($pending['coupon_id'])) {
            $coupon = Coupon::query()->with('products')->find($pending['coupon_id']);

            if ($coupon !== null) {
                try {
                    $this->couponService->assertApplicable($coupon, $dbProduct);
                } catch (ValidationException) {
                    return response()->json([
                        'message' => 'The applied coupon is no longer valid. Please checkout again.',
                    ], 422);
                }
            }
        }

        $pricing = $this->couponService->calculatePricing($dbProduct, $coupon);

        if ($pricing['amount_paise'] !== $pending['amount_paise']) {
            return response()->json([
                'message' => 'Order amount mismatch. Please checkout again.',
            ], 422);
        }

        $paymentMethod = $this->resolvePaymentMethod($api, $validated['razorpay_payment_id']);

        $order = DB::transaction(function () use ($pending, $pricing, $coupon, $validated, $paymentMethod) {
            $order = Order::query()->create([
                'user_id' => $pending['user_id'],
                'product_id' => $pending['product_id'],
                'product_snapshot' => $pending['product_snapshot'],
                'shipping_address_id' => $pending['shipping_address_id'],
                'shipping_snapshot' => $pending['shipping_snapshot'],
                'billing_address_id' => $pending['billing_address_id'],
                'billing_snapshot' => $pending['billing_snapshot'],
                'billing_same_as_shipping' => $pending['billing_same_as_shipping'],
                'subtotal_paise' => $pending['subtotal_paise'],
                'discount_paise' => $pending['discount_paise'],
                'amount_paise' => $pending['amount_paise'],
                'shipping_charges' => 0,
                'tax_amount' => Order::calculateInclusiveTaxPaise($pricing['amount_paise']),
                'coupon_id' => $coupon?->id,
                'coupon_snapshot' => $pending['coupon_snapshot'],
                'currency' => $pending['currency'],
                'status' => 'paid',
                'payment_status' => OrderPaymentStatus::Paid,
                'payment_method' => $paymentMethod,
                'fulfillment_status' => OrderFulfillmentStatus::Pending,
                'razorpay_order_id' => $validated['razorpay_order_id'],
                'razorpay_payment_id' => $validated['razorpay_payment_id'],
                'razorpay_signature' => $validated['razorpay_signature'],
                'paid_at' => now(),
            ]);

            User::query()
                ->whereKey($pending['user_id'])
                ->firstOrFail()
                ->recordSuccessfulOrder($pending['amount_paise']);

            if ($coupon !== null) {
                $this->couponService->markUsed($coupon);
            }

            return $order;
        });

        return response()->json([
            'redirect' => route('website.checkout.success', $order),
        ]);
    }

    public function success(Order $order): View
    {
        abort_unless($order->status === 'paid', 404);

        $order->load('user');

        return view('website.checkout_success', [
            'order' => $order,
        ]);
    }

    /**
     * @param  array{subtotal_paise: int, discount_paise: int, amount_paise: int}  $pricing
     */
    private function couponPayload(Coupon $coupon, array $pricing): array
    {
        return [
            'applied' => true,
            'code' => $coupon->code,
            'subtotal_paise' => $pricing['subtotal_paise'],
            'discount_paise' => $pricing['discount_paise'],
            'amount_paise' => $pricing['amount_paise'],
            'subtotal' => $this->formatPaise($pricing['subtotal_paise']),
            'discount' => $this->formatPaise($pricing['discount_paise']),
            'total' => $this->formatPaise($pricing['amount_paise']),
            'tax' => $this->formatTax($pricing['amount_paise']),
        ];
    }

    private function formatPaise(int $paise): string
    {
        return number_format($paise / 100, 2);
    }

    private function formatTax(int $amountPaise): string
    {
        $amount = $amountPaise / 100;
        $tax = $amount - ($amount / 1.18);

        return number_format($tax, 2);
    }

    private function resolvePaymentMethod(Api $api, string $paymentId): string
    {
        try {
            $payment = $api->payment->fetch($paymentId);

            return RazorpayPaymentMethod::labelFromPayment($payment->toArray());
        } catch (\Throwable $exception) {
            report($exception);

            return 'Razorpay';
        }
    }
}
