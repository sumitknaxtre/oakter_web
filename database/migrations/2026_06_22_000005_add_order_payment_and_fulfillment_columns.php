<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->unsignedInteger('shipping_charges')
                ->default(0)
                ->after('amount_paise')
                ->comment('Amount in paise');

            $table->unsignedInteger('tax_amount')
                ->default(0)
                ->after('shipping_charges')
                ->comment('Inclusive GST amount in paise');

            $table->unsignedTinyInteger('payment_status')
                ->default(1)
                ->after('status')
                ->comment('1 = Pending, 2 = Paid, 3 = Refunded');

            $table->string('payment_method', 50)
                ->nullable()
                ->after('payment_status');

            $table->string('fulfillment_status', 20)
                ->default('pending')
                ->after('payment_method')
                ->comment('pending or fulfilled');
        });

        DB::table('orders')
            ->where('status', 'paid')
            ->update([
                'payment_status' => 2,
                'payment_method' => 'Razorpay',
            ]);
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'shipping_charges',
                'tax_amount',
                'payment_status',
                'payment_method',
                'fulfillment_status',
            ]);
        });
    }
};
