<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCheckoutRequest;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use App\Services\Checkout\CheckoutPaymentCompletionService;
use App\Services\CheckoutAddressService;
use App\Services\CheckoutLookupService;
use App\Services\CheckoutUserService;
use App\Services\CouponService;
use App\Services\Meta\MetaProductPayload;
use App\Support\OrderAttribution;
use App\Support\ProductCatalog;
use App\Support\OrderFulfillmentStatus;
use App\Support\OrderPaymentStatus;
use App\Support\ShiprocketSyncStatus;
use App\Support\UnicommerceSyncStatus;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use InvalidArgumentException;
use Razorpay\Api\Api;

class CheckoutController extends Controller
{
    public function __construct(
        private readonly CheckoutUserService $checkoutUserService,
        private readonly CheckoutAddressService $checkoutAddressService,
        private readonly CheckoutLookupService $checkoutLookupService,
        private readonly CouponService $couponService,
        private readonly CheckoutPaymentCompletionService $paymentCompletionService,
    ) {}

    public function show(string $product): View
    {
        try {
            $product = ProductCatalog::get($product);
        } catch (InvalidArgumentException) {
            abort(404);
        }

        $dbProduct = Product::query()->findOrFail($product['id']);

        if ($dbProduct->isBuyButtonHidden()) {
            return redirect()->to(ProductCatalog::buyRoute($product['slug']));
        }

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
            'metaCheckoutProduct' => MetaProductPayload::fromCheckoutProduct($product),
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
        $dbProduct->ensurePurchasable();

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
        $dbProduct->ensurePurchasable();
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

        Order::query()->create([
            'user_id' => $user->id,
            'product_id' => $dbProduct->id,
            'product_snapshot' => $dbProduct->toSnapshot(),
            'shipping_address_id' => $addressData['shipping_address_id'],
            'shipping_snapshot' => $addressData['shipping_snapshot'],
            'billing_address_id' => $addressData['billing_address_id'],
            'billing_snapshot' => $addressData['billing_snapshot'],
            'billing_same_as_shipping' => $addressData['billing_same_as_shipping'],
            'subtotal_paise' => $pricing['subtotal_paise'],
            'discount_paise' => $pricing['discount_paise'],
            'amount_paise' => $pricing['amount_paise'],
            'shipping_charges' => 0,
            'tax_amount' => Order::calculateInclusiveTaxPaise($pricing['amount_paise']),
            'coupon_id' => $coupon?->id,
            'coupon_snapshot' => $coupon?->toSnapshot(),
            'currency' => $dbProduct->currency,
            'status' => 'pending',
            'payment_status' => OrderPaymentStatus::Pending,
            'fulfillment_status' => OrderFulfillmentStatus::Pending,
            'unicommerce_sync_status' => UnicommerceSyncStatus::Pending,
            'shiprocket_sync_status' => ShiprocketSyncStatus::Pending,
            'razorpay_order_id' => $razorpayOrder['id'],
            'attribution' => $this->buildOrderAttribution($request),
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
            'callback_url' => route('website.checkout.callback'),
        ]);
    }

    public function paymentCallback(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'razorpay_order_id' => ['required', 'string'],
            'razorpay_payment_id' => ['required', 'string'],
            'razorpay_signature' => ['required', 'string'],
        ]);

        $result = $this->paymentCompletionService->completeFromSignature(
            $validated['razorpay_order_id'],
            $validated['razorpay_payment_id'],
            $validated['razorpay_signature'],
            $request->string('fbp')->toString() ?: null,
            $request->string('fbc')->toString() ?: null,
            $request->ip(),
            $request->userAgent(),
        );

        if ($result->success && $result->order !== null) {
            return redirect()->route('website.checkout.success', $result->order);
        }

        $order = Order::query()
            ->where('razorpay_order_id', $validated['razorpay_order_id'])
            ->first();

        $productKey = $order?->product_snapshot['config_key'] ?? $order?->product?->config_key;

        if (is_string($productKey) && $productKey !== '') {
            return redirect()
                ->route('website.checkout.show', $productKey)
                ->with('checkout_error', $result->message ?? 'Payment could not be completed. Please contact support if you were charged.');
        }

        return redirect()
            ->route('website.home')
            ->with('checkout_error', $result->message ?? 'Payment could not be completed. Please contact support if you were charged.');
    }

    public function verify(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'razorpay_order_id' => ['required', 'string'],
            'razorpay_payment_id' => ['required', 'string'],
            'razorpay_signature' => ['required', 'string'],
            'fbp' => ['nullable', 'string', 'max:255'],
            'fbc' => ['nullable', 'string', 'max:255'],
        ]);

        $result = $this->paymentCompletionService->completeFromSignature(
            $validated['razorpay_order_id'],
            $validated['razorpay_payment_id'],
            $validated['razorpay_signature'],
            $validated['fbp'] ?? null,
            $validated['fbc'] ?? null,
            $request->ip(),
            $request->userAgent(),
        );

        if ($result->success && $result->order !== null) {
            return response()->json([
                'redirect' => route('website.checkout.success', $result->order),
            ]);
        }

        return response()->json([
            'message' => $result->message ?? 'Payment verification failed.',
        ], 422);
    }

    public function success(Order $order): View
    {
        abort_unless($order->isPaid(), 404);

        $order->load('user');

        return view('website.checkout_success', [
            'order' => $order,
            'metaPurchaseProduct' => MetaProductPayload::fromOrder($order),
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

    /**
     * @return array<string, mixed>|null
     */
    private function buildOrderAttribution(Request $request): ?array
    {
        $sessionAttribution = $request->session()->get(OrderAttribution::SESSION_KEY, []);
        $base = is_array($sessionAttribution) ? $sessionAttribution : [];

        $attribution = OrderAttribution::withMetaCookies(
            $base,
            $request->string('fbp')->toString() ?: null,
            $request->string('fbc')->toString() ?: null,
        );

        return $attribution === [] ? null : $attribution;
    }
}
