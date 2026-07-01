<?php

namespace App\Jobs;

use App\Models\Order;
use App\Services\Shiprocket\ShiprocketOrderSyncService;
use App\Support\ShiprocketSyncStatus;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Throwable;

class SyncOrderToShiprocketJob implements ShouldQueue
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

    public function handle(ShiprocketOrderSyncService $syncService): void
    {
        if (! config('shiprocket.enabled')) {
            return;
        }

        $order = Order::query()->find($this->orderId);

        if ($order === null || ! $order->isPaid() || $order->isCancelled()) {
            return;
        }

        if ($order->shiprocket_sync_status === ShiprocketSyncStatus::Synced) {
            return;
        }

        $syncService->sync($order);
    }

    public function failed(?Throwable $exception): void
    {
        $order = Order::query()->find($this->orderId);

        if ($order === null) {
            return;
        }

        if ($order->shiprocket_sync_status === ShiprocketSyncStatus::Synced) {
            return;
        }

        $order->update([
            'shiprocket_sync_status' => ShiprocketSyncStatus::Failed,
            'shiprocket_last_error' => $exception?->getMessage() ?? 'Shiprocket sync failed.',
        ]);

        Log::error('Shiprocket order sync failed.', [
            'order_id' => $this->orderId,
            'message' => $exception?->getMessage(),
        ]);
    }
}
