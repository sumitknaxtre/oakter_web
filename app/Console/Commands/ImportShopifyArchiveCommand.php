<?php

namespace App\Console\Commands;

use App\Models\ShopifyCustomer;
use App\Models\ShopifyOrder;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ImportShopifyArchiveCommand extends Command
{
    protected $signature = 'shopify:import-archive
                            {--customers=customers_export.csv : Path to Shopify customers CSV}
                            {--orders=orders_export_1.csv : Path to Shopify orders CSV}
                            {--fresh : Truncate archive tables before import}';

    protected $description = 'Import historical Shopify customers and orders from CSV exports into archive tables';

    public function handle(): int
    {
        $customersPath = $this->resolvePath((string) $this->option('customers'));
        $ordersPath = $this->resolvePath((string) $this->option('orders'));

        if (! is_file($customersPath)) {
            $this->error('Customers CSV not found: '.$customersPath);

            return self::FAILURE;
        }

        if (! is_file($ordersPath)) {
            $this->error('Orders CSV not found: '.$ordersPath);

            return self::FAILURE;
        }

        if ($this->option('fresh')) {
            $this->warn('Truncating shopify archive tables...');
            ShopifyOrder::query()->delete();
            ShopifyCustomer::query()->delete();
        }

        $customerCount = $this->importCustomers($customersPath);
        $orderCount = $this->importOrders($ordersPath);

        $this->info("Imported {$customerCount} Shopify customers and {$orderCount} Shopify orders.");

        return self::SUCCESS;
    }

    private function resolvePath(string $path): string
    {
        if ($this->isAbsolutePath($path)) {
            return $path;
        }

        return base_path($path);
    }

    private function isAbsolutePath(string $path): bool
    {
        return str_starts_with($path, '/')
            || (strlen($path) > 2 && ctype_alpha($path[0]) && $path[1] === ':' && ($path[2] === '\\' || $path[2] === '/'));
    }

    private function importCustomers(string $path): int
    {
        $handle = fopen($path, 'r');

        if ($handle === false) {
            throw new \RuntimeException('Unable to open customers CSV.');
        }

        $headers = fgetcsv($handle, null, ',', '"', '');
        if ($headers === false) {
            fclose($handle);

            throw new \RuntimeException('Customers CSV is empty.');
        }

        $map = $this->headerMap($headers);
        $count = 0;
        $batch = [];

        while (($row = fgetcsv($handle, null, ',', '"', '')) !== false) {
            $data = $this->rowAssoc($map, $row);
            $shopifyId = $this->cleanId($data['Customer ID'] ?? '');

            if ($shopifyId === '') {
                continue;
            }

            $email = $this->cleanEmail($data['Email'] ?? '');
            $name = $this->combineName($data['First Name'] ?? '', $data['Last Name'] ?? '');
            $phones = $this->combinePhones($data['Phone'] ?? '', $data['Default Address Phone'] ?? '');

            $batch[] = [
                'shopify_id' => $shopifyId,
                'name' => $name,
                'email' => $email,
                'address' => $this->combineAddress([
                    $data['Default Address Company'] ?? '',
                    $data['Default Address Address1'] ?? '',
                    $data['Default Address Address2'] ?? '',
                    $data['Default Address City'] ?? '',
                    $data['Default Address Province Code'] ?? '',
                    $data['Default Address Country Code'] ?? '',
                    $data['Default Address Zip'] ?? '',
                ]),
                'phones' => $phones,
                'total_spent' => $this->money($data['Total Spent'] ?? null) ?? 0,
                'total_orders' => (int) ($data['Total Orders'] ?? 0),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if (count($batch) >= 200) {
                $this->upsertCustomers($batch);
                $count += count($batch);
                $batch = [];
            }
        }

        if ($batch !== []) {
            $this->upsertCustomers($batch);
            $count += count($batch);
        }

        fclose($handle);

        return $count;
    }

    private function importOrders(string $path): int
    {
        $customersByEmail = ShopifyCustomer::query()
            ->whereNotNull('email')
            ->where('email', '!=', '')
            ->pluck('id', 'email')
            ->all();

        $handle = fopen($path, 'r');

        if ($handle === false) {
            throw new \RuntimeException('Unable to open orders CSV.');
        }

        $headers = fgetcsv($handle, null, ',', '"', '');
        if ($headers === false) {
            fclose($handle);

            throw new \RuntimeException('Orders CSV is empty.');
        }

        $map = $this->headerMap($headers);
        $count = 0;
        $batch = [];

        while (($row = fgetcsv($handle, null, ',', '"', '')) !== false) {
            $data = $this->rowAssoc($map, $row);
            $email = $this->cleanEmail($data['Email'] ?? '');
            $shopifyId = $this->cleanId($data['Id'] ?? '');
            $orderNumber = $this->nullableString($data['Name'] ?? '');

            if ($shopifyId === '' && $orderNumber === null && $email === null) {
                continue;
            }

            $batch[] = [
                'shopify_id' => $shopifyId !== '' ? $shopifyId : null,
                'order_number' => $orderNumber,
                'shopify_customer_id' => $email !== null ? ($customersByEmail[$email] ?? null) : null,
                'email' => $email,
                'financial_status' => $this->nullableString($data['Financial Status'] ?? ''),
                'paid_at' => $this->parseDate($data['Paid at'] ?? ''),
                'fulfillment_status' => $this->nullableString($data['Fulfillment Status'] ?? ''),
                'subtotal' => $this->money($data['Subtotal'] ?? null),
                'taxes' => $this->money($data['Taxes'] ?? null),
                'total' => $this->money($data['Total'] ?? null),
                'discount_code' => $this->nullableString($data['Discount Code'] ?? ''),
                'discount_amount' => $this->money($data['Discount Amount'] ?? null),
                'shopify_created_at' => $this->parseDate($data['Created at'] ?? ''),
                'lineitem_name' => $this->nullableString($data['Lineitem name'] ?? ''),
                'lineitem_qty' => $this->nullableInt($data['Lineitem quantity'] ?? ''),
                'lineitem_price' => $this->money($data['Lineitem price'] ?? null),
                'lineitem_compare_at_price' => $this->money($data['Lineitem compare at price'] ?? null),
                'shipping_address' => $this->combineAddress([
                    $data['Shipping Name'] ?? '',
                    $data['Shipping Company'] ?? '',
                    $data['Shipping Address1'] ?? '',
                    $data['Shipping Address2'] ?? '',
                    $data['Shipping City'] ?? '',
                    $data['Shipping Province'] ?? '',
                    $data['Shipping Province Name'] ?? '',
                    $data['Shipping Country'] ?? '',
                    $data['Shipping Zip'] ?? '',
                    $data['Shipping Phone'] ?? '',
                ]),
                'billing_address' => $this->combineAddress([
                    $data['Billing Name'] ?? '',
                    $data['Billing Company'] ?? '',
                    $data['Billing Address1'] ?? '',
                    $data['Billing Address2'] ?? '',
                    $data['Billing City'] ?? '',
                    $data['Billing Province'] ?? '',
                    $data['Billing Province Name'] ?? '',
                    $data['Billing Country'] ?? '',
                    $data['Billing Zip'] ?? '',
                    $data['Billing Phone'] ?? '',
                ]),
                'note_attributes' => $this->nullableString($data['Note Attributes'] ?? ''),
                'cancelled_at' => $this->parseDate($data['Cancelled at'] ?? ''),
                'payment_method' => $this->nullableString($data['Payment Method'] ?? ''),
                'payment_reference' => $this->nullableString($data['Payment Reference'] ?? ''),
                'refunded_amount' => $this->money($data['Refunded Amount'] ?? null),
                'tax_value' => $this->money($data['Tax 1 Value'] ?? null),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            if (count($batch) >= 100) {
                ShopifyOrder::query()->insert($batch);
                $count += count($batch);
                $batch = [];
            }
        }

        if ($batch !== []) {
            ShopifyOrder::query()->insert($batch);
            $count += count($batch);
        }

        fclose($handle);

        return $count;
    }

    /**
     * @param  list<array<string, mixed>>  $batch
     */
    private function upsertCustomers(array $batch): void
    {
        ShopifyCustomer::query()->upsert(
            $batch,
            ['shopify_id'],
            ['name', 'email', 'address', 'phones', 'total_spent', 'total_orders', 'updated_at']
        );
    }

    /**
     * @param  list<string|null>  $headers
     * @return array<string, int>
     */
    private function headerMap(array $headers): array
    {
        $map = [];

        foreach ($headers as $index => $header) {
            $key = trim((string) $header);
            if ($key !== '') {
                $map[$key] = $index;
            }
        }

        return $map;
    }

    /**
     * @param  array<string, int>  $map
     * @param  list<string|null>  $row
     * @return array<string, string>
     */
    private function rowAssoc(array $map, array $row): array
    {
        $assoc = [];

        foreach ($map as $header => $index) {
            $assoc[$header] = trim((string) ($row[$index] ?? ''));
        }

        return $assoc;
    }

    private function cleanId(string $value): string
    {
        return ltrim(trim($value), "'");
    }

    private function cleanEmail(string $value): ?string
    {
        $email = strtolower(trim($value));

        return $email !== '' ? $email : null;
    }

    private function combineName(string $first, string $last): ?string
    {
        $name = trim(trim($first).' '.trim($last));

        return $name !== '' ? $name : null;
    }

    private function combinePhones(string $phone, string $addressPhone): ?string
    {
        $phones = [];

        foreach ([$phone, $addressPhone] as $value) {
            $cleaned = $this->cleanId(trim($value));
            if ($cleaned !== '' && ! in_array($cleaned, $phones, true)) {
                $phones[] = $cleaned;
            }
        }

        return $phones === [] ? null : implode(', ', $phones);
    }

    /**
     * @param  list<string>  $parts
     */
    private function combineAddress(array $parts): ?string
    {
        $cleaned = [];

        foreach ($parts as $part) {
            $value = $this->cleanId(trim($part));
            if ($value !== '' && ! in_array($value, $cleaned, true)) {
                $cleaned[] = $value;
            }
        }

        return $cleaned === [] ? null : implode(', ', $cleaned);
    }

    private function nullableString(string $value): ?string
    {
        $value = trim($value);

        return $value !== '' ? $value : null;
    }

    private function nullableInt(string $value): ?int
    {
        $value = trim($value);

        return $value !== '' && is_numeric($value) ? (int) $value : null;
    }

    private function money(?string $value): ?float
    {
        if ($value === null) {
            return null;
        }

        $value = trim($value);

        if ($value === '' || ! is_numeric($value)) {
            return null;
        }

        return round((float) $value, 2);
    }

    private function parseDate(string $value): ?string
    {
        $value = trim($value);

        if ($value === '') {
            return null;
        }

        try {
            return Carbon::parse($value)->timezone(config('app.timezone'))->toDateTimeString();
        } catch (\Throwable) {
            return null;
        }
    }
}
