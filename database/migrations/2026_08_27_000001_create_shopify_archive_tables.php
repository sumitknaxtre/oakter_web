<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shopify_customers', function (Blueprint $table) {
            $table->id();
            $table->string('shopify_id')->unique();
            $table->string('name')->nullable();
            $table->string('email')->nullable()->index();
            $table->text('address')->nullable();
            $table->string('phones')->nullable();
            $table->decimal('total_spent', 12, 2)->default(0);
            $table->unsignedInteger('total_orders')->default(0);
            $table->timestamps();
        });

        Schema::create('shopify_orders', function (Blueprint $table) {
            $table->id();
            $table->string('shopify_id')->nullable()->index();
            $table->string('order_number')->nullable()->index();
            $table->foreignId('shopify_customer_id')->nullable()->constrained('shopify_customers')->nullOnDelete();
            $table->string('email')->nullable()->index();
            $table->string('financial_status')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->string('fulfillment_status')->nullable();
            $table->decimal('subtotal', 12, 2)->nullable();
            $table->decimal('taxes', 12, 2)->nullable();
            $table->decimal('total', 12, 2)->nullable();
            $table->string('discount_code')->nullable();
            $table->decimal('discount_amount', 12, 2)->nullable();
            $table->timestamp('shopify_created_at')->nullable()->index();
            $table->string('lineitem_name')->nullable();
            $table->unsignedInteger('lineitem_qty')->nullable();
            $table->decimal('lineitem_price', 12, 2)->nullable();
            $table->decimal('lineitem_compare_at_price', 12, 2)->nullable();
            $table->text('shipping_address')->nullable();
            $table->text('billing_address')->nullable();
            $table->longText('note_attributes')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('payment_reference')->nullable();
            $table->decimal('refunded_amount', 12, 2)->nullable();
            $table->decimal('tax_value', 12, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shopify_orders');
        Schema::dropIfExists('shopify_customers');
    }
};
