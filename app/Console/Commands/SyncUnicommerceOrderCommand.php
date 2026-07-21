<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Services\Unicommerce\UnicommerceClient;
use App\Services\Unicommerce\UnicommerceOrderMapper;
use App\Support\UnicommerceSyncStatus;
use Illuminate\Console\Command;

class SyncUnicommerceOrderCommand extends Command
{
    protected $signature = 'unicommerce:sync-order
                            {order : The Oakter order ID}
                            {--dry-run : Build and print the payload without calling Uniware}
                            {--force : Retry even if the order is already marked synced}';

    protected $description = 'Sync a single paid order to Uniware (debug + retry)';

    public function handle(
        UnicommerceOrderMapper $mapper,
        UnicommerceClient $client,
    ): int {
        if (! config('unicommerce.enabled')) {
            $this->error('Unicommerce is disabled. Set UNICOMMERCE_ENABLED=true in .env.');

            return self::FAILURE;
        }

        $order = Order::query()->with('product')->find($this->argument('order'));

        if ($order === null) {
            $this->error('Order not found.');

            return self::FAILURE;
        }

        if (! $order->isPaid()) {
            $this->error('Order #'.$order->id.' is not paid.');

            return self::FAILURE;
        }

        $this->line('Channel: '.config('unicommerce.channel'));
        $this->line('Product SKU: '.($order->product?->sku ?? '(missing)'));
        $this->line('Sync status: '.$order->unicommerce_sync_status);
        $this->line('Uniware code: '.($order->unicommerce_sale_order_code ?? '(none)'));

        try {
            $payload = $mapper->toCreateSaleOrderPayload($order);
        } catch (\Throwable $exception) {
            $this->error('Payload build failed: '.$exception->getMessage());

            return self::FAILURE;
        }

        $saleOrder = $payload['saleOrder'];
        $item = $saleOrder['saleOrderItems'][0] ?? [];
        $hasOrderLevelDiscount = array_key_exists('totalDiscount', $saleOrder);
        $hasOrderLevelPrepaid = array_key_exists('totalPrepaidAmount', $saleOrder);

        $this->line('Order-level totalDiscount: '.($hasOrderLevelDiscount ? 'YES (bad)' : 'no'));
        $this->line('Order-level totalPrepaidAmount: '.($hasOrderLevelPrepaid ? 'YES (bad)' : 'no'));
        $this->newLine();
        $this->line('Invoice pricing check (taxable base = sellingPrice):');
        $this->line('  sellingPrice: '.($item['sellingPrice'] ?? '(missing)'));
        $this->line('  totalPrice: '.($item['totalPrice'] ?? '(missing)'));
        $this->line('  prepaidAmount: '.($item['prepaidAmount'] ?? '(missing)'));
        $this->line('  discount: '.($item['discount'] ?? '(missing)').' (must be 0; coupon goes in additionalInfo)');
        $this->line('  additionalInfo: '.($saleOrder['additionalInfo'] ?? '(none)'));
        $this->line('  expected taxable (post-coupon): '.$this->formatRupees($order->amount_paise));
        if ($order->discount_paise > 0) {
            $this->line('  coupon discount (info only): '.$this->formatRupees($order->discount_paise));
        }

        $this->newLine();
        $this->line('Payload JSON:');
        $this->line(json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        if ($this->option('dry-run')) {
            $this->info('Dry run complete. No API call made.');
            $this->comment('Verify: sellingPrice == amount paid; then sync for real and confirm Uniware invoice taxable value matches.');
            $this->comment('Note: already-synced Uniware orders keep old prices; only new creates get this pricing.');

            return self::SUCCESS;
        }

        $this->newLine();
        $this->line('Calling Uniware create sale order API...');

        try {
            $response = $client->createSaleOrder($payload);
        } catch (\Throwable $exception) {
            $this->error('API request failed: '.$exception->getMessage());

            return self::FAILURE;
        }

        $this->newLine();
        $this->line('API response:');
        $this->line(json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        if (! ($response['successful'] ?? false)) {
            $this->error('Uniware rejected the order.');

            return self::FAILURE;
        }

        if ($this->option('force') || $order->unicommerce_sync_status !== UnicommerceSyncStatus::Synced) {
            $order->update([
                'unicommerce_sale_order_code' => $response['saleOrderDetailDTO']['code'] ?? $mapper->saleOrderCode($order),
                'unicommerce_sync_status' => UnicommerceSyncStatus::Synced,
                'unicommerce_synced_at' => now(),
                'unicommerce_last_error' => null,
            ]);
        }

        $this->info('Order #'.$order->id.' synced to Uniware successfully.');
        $this->comment('Next: print Uniware invoice and confirm taxable value equals sellingPrice (post-coupon amount).');

        return self::SUCCESS;
    }

    private function formatRupees(int $paise): string
    {
        return '₹'.number_format($paise / 100, 2);
    }
}
