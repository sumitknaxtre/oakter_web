<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->string('razorpay_refund_id', 50)
                ->nullable()
                ->unique()
                ->after('razorpay_signature');

            $table->timestamp('cancelled_at')
                ->nullable()
                ->after('paid_at');

            $table->timestamp('refunded_at')
                ->nullable()
                ->after('cancelled_at');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'razorpay_refund_id',
                'cancelled_at',
                'refunded_at',
            ]);
        });
    }
};
