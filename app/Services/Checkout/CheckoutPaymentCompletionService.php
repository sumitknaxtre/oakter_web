<?php

namespace App\Services\Checkout;

use App\Jobs\SyncOrderToUnicommerceJob;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\CouponService;
use App\Services\Meta\MetaPurchaseEventService;
use App\Support\OrderAttribution;
use App\Support\OrderFulfillmentStatus;
use App\Support\OrderPaymentStatus;
use App\Support\RazorpayPaymentMethod;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;

class CheckoutPaymentCompletionService
{
    public function __construct(
        private readonly CouponService $couponService,
        private readonly MetaPurchaseEventService $metaPurchaseEventService,
    ) {}

    public function completeFromSignature(
        string $razorpayOrderId,
        string $razorpayPaymentId,
        string $razorpaySignature,
        ?string $fbp = null,
        ?string $fbc = null,
        ?string $clientIp = null,
        ?string $userAgent = null,
        ?string $eventSourceUrl = null,
    ): CheckoutPaymentCompletionResult {
        $order = Order::query()
            ->where('razorpay_order_id', $razorpayOrderId)
            ->first();

        if ($order === null) {
            return CheckoutPaymentCompletionResult::failed('Checkout session expired. Please try again.');
        }

        if ($order->isPaid()) {
            return CheckoutPaymentCompletionResult::alreadyPaid($order);
        }

        $api = $this->api();

        try {
            $api->utility->verifyPaymentSignature([
                'razorpay_order_id' => $razorpayOrderId,
                'razorpay_payment_id' => $razorpayPaymentId,
                'razorpay_signature' => $razorpaySignature,
            ]);
        } catch (SignatureVerificationError) {
            return CheckoutPaymentCompletionResult::failed('Payment verification failed.');
        }

        return $this->completeCapturedPayment(
            $order,
            $razorpayPaymentId,
            $razorpaySignature,
            $api,
            $fbp,
            $fbc,
            $clientIp,
            $userAgent,
            $eventSourceUrl,
        );
    }

    public function completeFromRazorpayPayment(
        string $razorpayOrderId,
        string $razorpayPaymentId,
        ?string $clientIp = null,
        ?string $userAgent = null,
        ?string $eventSourceUrl = null,
    ): CheckoutPaymentCompletionResult {
        $order = Order::query()
            ->where('razorpay_order_id', $razorpayOrderId)
            ->first();

        if ($order === null) {
            return CheckoutPaymentCompletionResult::failed('Order not found for Razorpay order.');
        }

        if ($order->isPaid()) {
            return CheckoutPaymentCompletionResult::alreadyPaid($order);
        }

        $api = $this->api();

        try {
            $payment = $api->payment->fetch($razorpayPaymentId)->toArray();
        } catch (\Throwable $exception) {
            report($exception);

            return CheckoutPaymentCompletionResult::failed('Unable to verify payment with Razorpay.');
        }

        if (($payment['order_id'] ?? null) !== $razorpayOrderId) {
            return CheckoutPaymentCompletionResult::failed('Payment does not belong to this order.');
        }

        if (! in_array($payment['status'] ?? '', ['captured', 'authorized'], true)) {
            return CheckoutPaymentCompletionResult::failed('Payment is not captured yet.');
        }

        if ((int) ($payment['amount'] ?? 0) !== (int) $order->amount_paise) {
            return CheckoutPaymentCompletionResult::failed('Order amount mismatch.');
        }

        return $this->completeCapturedPayment(
            $order,
            $razorpayPaymentId,
            null,
            $api,
            null,
            null,
            $clientIp,
            $userAgent,
            $eventSourceUrl,
        );
    }

    private function completeCapturedPayment(
        Order $order,
        string $razorpayPaymentId,
        ?string $razorpaySignature,
        Api $api,
        ?string $fbp,
        ?string $fbc,
        ?string $clientIp,
        ?string $userAgent,
        ?string $eventSourceUrl,
    ): CheckoutPaymentCompletionResult {
        $order->refresh();

        if ($order->isPaid()) {
            return CheckoutPaymentCompletionResult::alreadyPaid($order);
        }

        $dbProduct = Product::query()->find($order->product_id);

        if ($dbProduct === null) {
            return CheckoutPaymentCompletionResult::failed('Product for this order is no longer available.');
        }

        try {
            $dbProduct->ensureInStock();
        } catch (ValidationException $exception) {
            return CheckoutPaymentCompletionResult::failed(
                $exception->errors()['product'][0] ?? 'Product is out of stock.',
            );
        }

        $coupon = $order->coupon_id
            ? Coupon::query()->with('products')->find($order->coupon_id)
            : null;

        if ($coupon !== null) {
            try {
                $this->couponService->assertApplicable($coupon, $dbProduct);
            } catch (ValidationException) {
                return CheckoutPaymentCompletionResult::failed(
                    'The applied coupon is no longer valid. Please contact support.',
                );
            }
        }

        $pricing = $this->couponService->calculatePricing($dbProduct, $coupon);

        if ($pricing['amount_paise'] !== $order->amount_paise) {
            return CheckoutPaymentCompletionResult::failed('Order amount mismatch. Please contact support.');
        }

        $paymentMethod = $this->resolvePaymentMethod($api, $razorpayPaymentId);
        $metaEventId = is_string($order->meta_event_id) && $order->meta_event_id !== ''
            ? $order->meta_event_id
            : (string) Str::uuid();

        DB::transaction(function () use ($order, $pricing, $coupon, $razorpayPaymentId, $razorpaySignature, $paymentMethod, $metaEventId, $fbp, $fbc) {
            $order->refresh();

            if ($order->isPaid()) {
                return;
            }

            $order->update([
                'tax_amount' => Order::calculateInclusiveTaxPaise($pricing['amount_paise']),
                'coupon_id' => $coupon?->id,
                'status' => 'paid',
                'payment_status' => OrderPaymentStatus::Paid,
                'payment_method' => $paymentMethod,
                'razorpay_payment_id' => $razorpayPaymentId,
                'razorpay_signature' => $razorpaySignature,
                'paid_at' => now(),
                'meta_event_id' => $metaEventId,
                'attribution' => $this->mergeOrderAttribution(
                    $order->attribution ?? [],
                    $fbp,
                    $fbc,
                ),
            ]);

            User::query()
                ->whereKey($order->user_id)
                ->firstOrFail()
                ->recordSuccessfulOrder($order->amount_paise);

            if ($coupon !== null) {
                $this->couponService->markUsed($coupon);
            }
        });

        $order->refresh();

        if (! $order->isPaid()) {
            return CheckoutPaymentCompletionResult::alreadyPaid($order);
        }

        if (config('unicommerce.enabled')) {
            SyncOrderToUnicommerceJob::dispatch($order->id);
        }

        $this->metaPurchaseEventService->dispatchPurchase(
            $order,
            $clientIp,
            $userAgent,
            $fbp,
            $fbc,
            $eventSourceUrl ?? route('website.checkout.success', $order),
        );

        return CheckoutPaymentCompletionResult::paid($order);
    }

    private function api(): Api
    {
        return new Api(config('razorpay.key_id'), config('razorpay.key_secret'));
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

    /**
     * @param  array<string, mixed>  $attribution
     * @return array<string, mixed>|null
     */
    private function mergeOrderAttribution(array $attribution, ?string $fbp, ?string $fbc): ?array
    {
        $merged = OrderAttribution::withMetaCookies($attribution, $fbp, $fbc);

        return $merged === [] ? null : $merged;
    }
}
