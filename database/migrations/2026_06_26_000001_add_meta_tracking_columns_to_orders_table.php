<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Shared event_id for browser Pixel + CAPI deduplication.
            $table->string('meta_event_id', 36)
                ->nullable()
                ->after('paid_at');

            // Prevents duplicate CAPI Purchase on payment-verify retries.
            $table->timestamp('meta_purchase_sent_at')
                ->nullable()
                ->after('meta_event_id');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'meta_event_id',
                'meta_purchase_sent_at',
            ]);
        });
    }
};
