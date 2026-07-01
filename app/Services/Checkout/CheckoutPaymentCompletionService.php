<?php

namespace App\Services\Checkout;

use App\Jobs\SyncOrderToShiprocketJob;
use App\Jobs\SyncOrderToUnicommerceJob;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\User;
use App\Services\CouponService;
use App\Services\Meta\MetaPurchaseEventService;
use App\Support\OrderAttribution;
use App\Support\OrderPaymentStatus;
use App\Support\RazorpayPaymentMethod;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
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
        $order = $this->findPendingOrder($razorpayOrderId);

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

        return $this->markOrderPaid(
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
        $order = $this->findPendingOrder($razorpayOrderId);

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

        return $this->markOrderPaid(
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

    public function reconcilePendingOrder(Order $order): CheckoutPaymentCompletionResult
    {
        if ($order->isPaid()) {
            return CheckoutPaymentCompletionResult::alreadyPaid($order);
        }

        if (! is_string($order->razorpay_order_id) || $order->razorpay_order_id === '') {
            return CheckoutPaymentCompletionResult::failed('This order has no Razorpay order ID.');
        }

        $paymentId = $this->findCapturedPaymentId($order->razorpay_order_id);

        if ($paymentId === null) {
            return CheckoutPaymentCompletionResult::failed('No captured payment found on Razorpay for this order.');
        }

        return $this->completeFromRazorpayPayment(
            $order->razorpay_order_id,
            $paymentId,
        );
    }

    public function findCapturedPaymentId(string $razorpayOrderId): ?string
    {
        $api = $this->api();

        try {
            $razorpayOrder = $api->order->fetch($razorpayOrderId)->toArray();
        } catch (\Throwable $exception) {
            report($exception);

            return null;
        }

        if (($razorpayOrder['status'] ?? '') !== 'paid') {
            return null;
        }

        try {
            $payments = $api->order->fetch($razorpayOrderId)->payments()->toArray();
        } catch (\Throwable $exception) {
            report($exception);

            return null;
        }

        foreach ($payments['items'] ?? [] as $payment) {
            if (! is_array($payment)) {
                continue;
            }

            if (in_array($payment['status'] ?? '', ['captured', 'authorized'], true)) {
                return $payment['id'] ?? null;
            }
        }

        return null;
    }

    private function markOrderPaid(
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

        $coupon = $order->coupon_id
            ? Coupon::query()->find($order->coupon_id)
            : null;

        $paymentMethod = $this->resolvePaymentMethod($api, $razorpayPaymentId);
        $metaEventId = is_string($order->meta_event_id) && $order->meta_event_id !== ''
            ? $order->meta_event_id
            : (string) Str::uuid();

        DB::transaction(function () use ($order, $coupon, $razorpayPaymentId, $razorpaySignature, $paymentMethod, $metaEventId, $fbp, $fbc) {
            $order->refresh();

            if ($order->isPaid()) {
                return;
            }

            $order->update([
                'tax_amount' => Order::calculateInclusiveTaxPaise($order->amount_paise),
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
            Log::error('Checkout payment completion failed after transaction.', [
                'order_id' => $order->id,
                'razorpay_payment_id' => $razorpayPaymentId,
            ]);

            return CheckoutPaymentCompletionResult::failed('Unable to mark order as paid.');
        }

        if (config('unicommerce.enabled')) {
            SyncOrderToUnicommerceJob::dispatch($order->id);
        }

        if (config('shiprocket.enabled')) {
            SyncOrderToShiprocketJob::dispatch($order->id);
        }

        $this->metaPurchaseEventService->dispatchPurchase(
            $order,
            $clientIp,
            $userAgent,
            $fbp,
            $fbc,
            $eventSourceUrl ?? route('website.checkout.success', $order),
        );

        Log::info('Checkout payment completed.', [
            'order_id' => $order->id,
            'razorpay_order_id' => $order->razorpay_order_id,
            'razorpay_payment_id' => $razorpayPaymentId,
        ]);

        return CheckoutPaymentCompletionResult::paid($order);
    }

    private function findPendingOrder(string $razorpayOrderId): ?Order
    {
        return Order::query()
            ->where('razorpay_order_id', $razorpayOrderId)
            ->first();
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
