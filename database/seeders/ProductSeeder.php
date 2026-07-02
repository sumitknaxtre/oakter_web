<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Support\ProductCatalog;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        foreach (ProductCatalog::slugs() as $slug => $configKey) {
            $config = config("products.{$configKey}");

            if ($config === null) {
                continue;
            }

            Product::query()->updateOrCreate(
                ['slug' => $slug],
                [
                    'config_key' => $configKey,
                    'name' => $config['order_name'],
                    'amount_paise' => $config['amount_paise'],
                    'currency' => 'INR',
                    'catalog' => $config,
                    'is_active' => true,
                    'is_in_stock' => true,
                    'hide_buy_button' => false,
                ],
            );
        }
    }
}
