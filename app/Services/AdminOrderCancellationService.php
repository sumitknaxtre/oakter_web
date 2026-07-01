<?php

namespace App\Services;

use App\Jobs\CancelOrderInShiprocketJob;
use App\Models\Order;
use App\Support\OrderFulfillmentStatus;
use App\Support\OrderPaymentStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Razorpay\Api\Api;
use RuntimeException;

class AdminOrderCancellationService
{
    public function cancel(Order $order): void
    {
        if (! $order->canBeCancelled()) {
            throw ValidationException::withMessages([
                'cancel' => ['This order cannot be cancelled.'],
            ]);
        }

        if (! is_string($order->razorpay_payment_id) || $order->razorpay_payment_id === '') {
            throw ValidationException::withMessages([
                'cancel' => ['This order has no Razorpay payment to refund.'],
            ]);
        }

        abort_unless(config('razorpay.key_id') && config('razorpay.key_secret'), 503, 'Payment gateway is not configured.');

        $api = new Api(config('razorpay.key_id'), config('razorpay.key_secret'));

        try {
            $refund = $api->payment
                ->fetch($order->razorpay_payment_id)
                ->refund([
                    'amount' => $order->amount_paise,
                    'notes' => [
                        'order_id' => (string) $order->id,
                    ],
                ]);
        } catch (\Throwable $exception) {
            report($exception);

            throw ValidationException::withMessages([
                'cancel' => [$this->refundErrorMessage($exception)],
            ]);
        }

        $refundId = $refund['id'] ?? null;

        if (! is_string($refundId) || $refundId === '') {
            throw new RuntimeException('Razorpay refund did not return a refund ID.');
        }

        DB::transaction(function () use ($order, $refundId) {
            $now = now();

            $order->update([
                'status' => 'cancelled',
                'payment_status' => OrderPaymentStatus::Refunded,
                'fulfillment_status' => OrderFulfillmentStatus::Cancelled,
                'razorpay_refund_id' => $refundId,
                'cancelled_at' => $now,
                'refunded_at' => $now,
            ]);
        });

        if (config('shiprocket.enabled')) {
            CancelOrderInShiprocketJob::dispatch($order->id);
        }
    }

    private function refundErrorMessage(\Throwable $exception): string
    {
        $message = $exception->getMessage();

        if (str_contains(strtolower($message), 'already been refunded')) {
            return 'This payment has already been refunded in Razorpay.';
        }

        return 'Razorpay refund failed. Please try again or process the refund manually in the Razorpay dashboard.';
    }
}
