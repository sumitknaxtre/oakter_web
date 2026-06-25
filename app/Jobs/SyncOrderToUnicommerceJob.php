<?php

namespace App\Jobs;

use App\Models\Order;
use App\Services\Unicommerce\UnicommerceOrderSyncService;
use App\Support\UnicommerceSyncStatus;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Throwable;

class SyncOrderToUnicommerceJob implements ShouldQueue
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

    public function handle(UnicommerceOrderSyncService $syncService): void
    {
        if (! config('unicommerce.enabled')) {
            return;
        }

        $order = Order::query()->find($this->orderId);

        if ($order === null || ! $order->isPaid()) {
            return;
        }

        if ($order->unicommerce_sync_status === UnicommerceSyncStatus::Synced) {
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

        if ($order->unicommerce_sync_status === UnicommerceSyncStatus::Synced) {
            return;
        }

        $order->update([
            'unicommerce_sync_status' => UnicommerceSyncStatus::Failed,
            'unicommerce_last_error' => $exception?->getMessage() ?? 'Unicommerce sync failed.',
        ]);

        Log::error('Unicommerce order sync failed.', [
            'order_id' => $this->orderId,
            'message' => $exception?->getMessage(),
        ]);
    }
}
