<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('shiprocket_reference', 50)
                ->nullable()
                ->after('unicommerce_last_error');

            $table->unsignedBigInteger('shiprocket_order_id')
                ->nullable()
                ->after('shiprocket_reference');

            $table->unsignedBigInteger('shiprocket_shipment_id')
                ->nullable()
                ->after('shiprocket_order_id');

            $table->string('shiprocket_sync_status', 20)
                ->default('pending')
                ->after('shiprocket_shipment_id');

            $table->timestamp('shiprocket_synced_at')
                ->nullable()
                ->after('shiprocket_sync_status');

            $table->timestamp('shiprocket_cancelled_at')
                ->nullable()
                ->after('shiprocket_synced_at');

            $table->text('shiprocket_last_error')
                ->nullable()
                ->after('shiprocket_cancelled_at');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'shiprocket_reference',
                'shiprocket_order_id',
                'shiprocket_shipment_id',
                'shiprocket_sync_status',
                'shiprocket_synced_at',
                'shiprocket_cancelled_at',
                'shiprocket_last_error',
            ]);
        });
    }
};
