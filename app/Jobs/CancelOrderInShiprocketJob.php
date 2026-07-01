<?php

namespace App\Jobs;

use App\Models\Order;
use App\Services\Shiprocket\ShiprocketOrderCancellationService;
use App\Support\ShiprocketSyncStatus;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Throwable;

class CancelOrderInShiprocketJob implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;

    /**
     * @return list<int>
     */
    public function backoff(): array
    {
        return [30, 120, 300];
    }

    public function __construct(
        public readonly int $orderId,
    ) {}

    public function handle(ShiprocketOrderCancellationService $cancellationService): void
    {
        if (! config('shiprocket.enabled')) {
            return;
        }

        $order = Order::query()->find($this->orderId);

        if ($order === null || $order->shiprocket_order_id === null) {
            return;
        }

        if ($order->shiprocket_sync_status === ShiprocketSyncStatus::Cancelled) {
            return;
        }

        $cancellationService->cancel($order);
    }

    public function failed(?Throwable $exception): void
    {
        Log::error('Shiprocket order cancellation failed.', [
            'order_id' => $this->orderId,
            'message' => $exception?->getMessage(),
        ]);
    }
}
