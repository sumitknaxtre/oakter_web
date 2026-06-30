<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Services\Checkout\CheckoutPaymentCompletionService;
use Illuminate\Console\Command;
use Razorpay\Api\Api;

class ReconcilePendingPaymentsCommand extends Command
{
    protected $signature = 'orders:reconcile-payments
                            {--order= : Reconcile a single order by database ID}
                            {--hours=72 : Only check pending orders created within this many hours}
                            {--dry-run : List candidates without updating}';

    protected $description = 'Mark pending orders as paid when Razorpay shows a captured payment';

    public function handle(CheckoutPaymentCompletionService $paymentCompletionService): int
    {
        if (! config('razorpay.key_id') || ! config('razorpay.key_secret')) {
            $this->error('Razorpay is not configured.');

            return self::FAILURE;
        }

        $query = Order::query()
            ->pendingPayment()
            ->whereNotNull('razorpay_order_id')
            ->latest('id');

        if ($orderId = $this->option('order')) {
            $query->whereKey($orderId);
        } else {
            $hours = max(1, (int) $this->option('hours'));
            $query->where('created_at', '>=', now()->subHours($hours));
        }

        $orders = $query->get();

        if ($orders->isEmpty()) {
            $this->info('No pending orders to reconcile.');

            return self::SUCCESS;
        }

        $api = new Api(config('razorpay.key_id'), config('razorpay.key_secret'));
        $reconciled = 0;

        foreach ($orders as $order) {
            $paymentId = $this->findCapturedPaymentId($api, (string) $order->razorpay_order_id);

            if ($paymentId === null) {
                $this->line("Order #{$order->id}: no captured payment on Razorpay.");

                continue;
            }

            if ($this->option('dry-run')) {
                $this->info("Order #{$order->id}: would reconcile with payment {$paymentId}.");

                continue;
            }

            $result = $paymentCompletionService->completeFromRazorpayPayment(
                (string) $order->razorpay_order_id,
                $paymentId,
            );

            if ($result->success) {
                $reconciled++;
                $this->info("Order #{$order->id}: marked paid (payment {$paymentId}).");
            } else {
                $this->warn("Order #{$order->id}: {$result->message}");
            }
        }

        if (! $this->option('dry-run')) {
            $this->info("Reconciled {$reconciled} order(s).");
        }

        return self::SUCCESS;
    }

    private function findCapturedPaymentId(Api $api, string $razorpayOrderId): ?string
    {
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
}
