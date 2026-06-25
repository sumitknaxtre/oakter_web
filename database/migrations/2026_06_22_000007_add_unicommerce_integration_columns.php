<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('sku', 100)
                ->nullable()
                ->unique()
                ->after('config_key');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->string('unicommerce_sale_order_code', 50)
                ->nullable()
                ->unique()
                ->after('fulfillment_status');

            $table->string('unicommerce_sync_status', 20)
                ->default('pending')
                ->after('unicommerce_sale_order_code');

            $table->timestamp('unicommerce_synced_at')
                ->nullable()
                ->after('unicommerce_sync_status');

            $table->text('unicommerce_last_error')
                ->nullable()
                ->after('unicommerce_synced_at');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'unicommerce_sale_order_code',
                'unicommerce_sync_status',
                'unicommerce_synced_at',
                'unicommerce_last_error',
            ]);
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn('sku');
        });
    }
};
