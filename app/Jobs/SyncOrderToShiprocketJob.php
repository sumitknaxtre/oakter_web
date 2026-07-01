<?php

namespace App\Jobs;

use App\Models\Order;
use App\Services\Shiprocket\ShiprocketOrderSyncService;
use App\Support\ShiprocketSyncStatus;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Contracts\Queue\ShouldQueueAfterCommit;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;
use Throwable;

class SyncOrderToShiprocketJob implements ShouldQueue, ShouldQueueAfterCommit
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
        public readonly ?string $pickupLocation = null,
    ) {}

    public function handle(ShiprocketOrderSyncService $syncService): void
    {
        $pickupLocation = $this->pickupLocation ?? config('shiprocket.pickup_location');

        if (is_string($pickupLocation) && $pickupLocation !== '') {
            config(['shiprocket.pickup_location' => $pickupLocation]);
        }

        if (! config('shiprocket.enabled')) {
            Log::warning('Shiprocket sync skipped because integration is disabled in the queue worker.', [
                'order_id' => $this->orderId,
                'hint' => 'Set SHIPROCKET_ENABLED=true, then run: php artisan config:cache && php artisan queue:restart',
            ]);

            return;
        }

        $order = Order::query()->find($this->orderId);

        if ($order === null) {
            Log::warning('Shiprocket sync skipped because order was not found.', [
                'order_id' => $this->orderId,
            ]);

            return;
        }

        if (! $order->isPaid() || $order->isCancelled()) {
            Log::warning('Shiprocket sync skipped because order is not eligible.', [
                'order_id' => $this->orderId,
                'payment_status' => $order->payment_status,
                'fulfillment_status' => $order->fulfillment_status,
            ]);

            return;
        }

        if ($order->shiprocket_sync_status === ShiprocketSyncStatus::Synced) {
            return;
        }

        $syncService->sync($order);

        Log::info('Shiprocket order sync completed.', [
            'order_id' => $order->id,
            'pickup_location' => config('shiprocket.pickup_location'),
            'shiprocket_order_id' => $order->fresh()->shiprocket_order_id,
            'shiprocket_reference' => $order->shiprocket_reference,
        ]);
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
            'pickup_location' => $this->pickupLocation ?? config('shiprocket.pickup_location'),
            'message' => $exception?->getMessage(),
        ]);
    }
}
