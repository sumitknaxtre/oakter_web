<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Services\Shiprocket\ShiprocketOrderMapper;
use App\Services\Shiprocket\ShiprocketOrderSyncService;
use App\Support\ShiprocketSyncStatus;
use Illuminate\Console\Command;

class SyncShiprocketOrderCommand extends Command
{
    protected $signature = 'shiprocket:sync-order
                            {order : The Oakter order ID}
                            {--dry-run : Build and print the payload without calling Shiprocket}
                            {--force : Retry even if the order is already marked synced}';

    protected $description = 'Sync a single paid order to Shiprocket (debug + retry)';

    public function handle(
        ShiprocketOrderMapper $mapper,
        ShiprocketOrderSyncService $syncService,
    ): int {
        if (! config('shiprocket.enabled')) {
            $this->error('Shiprocket is disabled. Set SHIPROCKET_ENABLED=true in .env.');

            return self::FAILURE;
        }

        $order = Order::query()->with('user')->find($this->argument('order'));

        if ($order === null) {
            $this->error('Order not found.');

            return self::FAILURE;
        }

        if (! $order->isPaid()) {
            $this->error('Order #'.$order->id.' is not paid.');

            return self::FAILURE;
        }

        $this->line('Pickup location: '.config('shiprocket.pickup_location'));
        $this->line('Reference order ID: '.$mapper->referenceOrderId($order));
        $this->line('Sync status: '.$order->shiprocket_sync_status);
        $this->line('Shiprocket order ID: '.($order->shiprocket_order_id ?? '(none)'));

        try {
            $payload = $mapper->toCreateAdhocOrderPayload($order);
        } catch (\Throwable $exception) {
            $this->error('Payload build failed: '.$exception->getMessage());

            return self::FAILURE;
        }

        $this->newLine();
        $this->line('Payload JSON:');
        $this->line(json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        if ($this->option('dry-run')) {
            $this->info('Dry run complete. No API call made.');

            return self::SUCCESS;
        }

        if ($order->shiprocket_sync_status === ShiprocketSyncStatus::Synced && ! $this->option('force')) {
            $this->warn('Order is already synced. Use --force to create again with a new reference.');

            return self::SUCCESS;
        }

        try {
            $syncService->sync($order->fresh());
        } catch (\Throwable $exception) {
            $this->error('Shiprocket sync failed: '.$exception->getMessage());

            return self::FAILURE;
        }

        $this->info('Order #'.$order->id.' synced to Shiprocket successfully.');

        return self::SUCCESS;
    }
}
