<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->restrictOnDelete();
            $table->foreignId('product_id')->constrained()->restrictOnDelete();
            $table->json('product_snapshot');
            $table->foreignId('shipping_address_id')->nullable()->constrained('user_addresses')->nullOnDelete();
            $table->json('shipping_snapshot');
            $table->foreignId('billing_address_id')->nullable()->constrained('user_addresses')->nullOnDelete();
            $table->json('billing_snapshot');
            $table->boolean('billing_same_as_shipping')->default(true);
            $table->unsignedInteger('subtotal_paise');
            $table->unsignedInteger('discount_paise')->default(0);
            $table->unsignedInteger('amount_paise');
            $table->foreignId('coupon_id')->nullable()->constrained()->nullOnDelete();
            $table->json('coupon_snapshot')->nullable();
            $table->string('currency', 3)->default('INR');
            $table->string('status')->default('paid');
            $table->string('razorpay_order_id')->nullable()->unique();
            $table->string('razorpay_payment_id')->nullable()->unique();
            $table->string('razorpay_signature')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
