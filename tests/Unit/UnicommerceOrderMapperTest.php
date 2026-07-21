<?php

namespace Tests\Unit;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\Unicommerce\UnicommerceOrderMapper;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class UnicommerceOrderMapperTest extends TestCase
{
    public function test_it_maps_a_coupon_order_with_post_discount_selling_price(): void
    {
        config([
            'unicommerce.channel' => 'Oakter Website',
            'unicommerce.shipping_method' => 'STD',
            'unicommerce.order_code_prefix' => 'OAKTER',
            'unicommerce.display_order_code_prefix' => 'NEW',
        ]);

        $order = new Order([
            'shipping_snapshot' => [
                'first_name' => 'Rahul',
                'last_name' => 'Sharma',
                'phone' => '9876543210',
                'address_line1' => '12 MG Road',
                'address_line2' => 'Near Metro',
                'city' => 'Bengaluru',
                'state' => 'Karnataka',
                'pincode' => '560001',
                'country' => 'India',
            ],
            'billing_same_as_shipping' => true,
            'subtotal_paise' => 1699900,
            'discount_paise' => 150000,
            'amount_paise' => 1549900,
            'shipping_charges' => 0,
            'currency' => 'INR',
            'payment_method' => 'UPI',
            'coupon_snapshot' => [
                'code' => 'SAVE1500',
            ],
            'paid_at' => Carbon::parse('2026-06-24 10:30:00'),
        ]);
        $order->id = 15;

        $order->setRelation('user', new User(['email' => 'rahul@example.com']));
        $order->setRelation('product', new Product(['sku' => 'STUDIO-AC-5000']));

        $payload = (new UnicommerceOrderMapper)->toCreateSaleOrderPayload($order);
        $item = $payload['saleOrder']['saleOrderItems'][0];

        $this->assertSame('OAKTER-15', $payload['saleOrder']['code']);
        $this->assertSame('NEW15', $payload['saleOrder']['displayOrderCode']);
        $this->assertFalse($payload['saleOrder']['cashOnDelivery']);
        $this->assertSame('WALLET', $payload['saleOrder']['paymentInstrument']);
        $this->assertSame('OAKTER-15-1', $item['code']);
        $this->assertSame('STUDIO-AC-5000', $item['itemSku']);
        $this->assertSame(15499.0, $item['sellingPrice']);
        $this->assertSame(15499.0, $item['totalPrice']);
        $this->assertSame(15499.0, $item['prepaidAmount']);
        $this->assertSame(0, $item['discount']);
        $this->assertSame('Coupon SAVE1500: -₹1,500.00', $payload['saleOrder']['additionalInfo']);
        $this->assertArrayNotHasKey('totalDiscount', $payload['saleOrder']);
        $this->assertArrayNotHasKey('totalPrepaidAmount', $payload['saleOrder']);
        $this->assertArrayNotHasKey('totalShippingCharges', $payload['saleOrder']);
        $this->assertSame('shipping', $payload['saleOrder']['shippingAddress']['referenceId']);
    }

    public function test_it_maps_a_full_price_order_without_order_level_totals(): void
    {
        config([
            'unicommerce.channel' => 'CUSTOM_WEBSITE',
            'unicommerce.shipping_method' => 'STD',
            'unicommerce.order_code_prefix' => 'OAKTER',
            'unicommerce.display_order_code_prefix' => 'NEW',
        ]);

        $order = new Order([
            'shipping_snapshot' => [
                'first_name' => 'Rahul',
                'last_name' => 'Sharma',
                'phone' => '9876543210',
                'address_line1' => '12 MG Road',
                'city' => 'Bengaluru',
                'state' => 'Karnataka',
                'pincode' => '560001',
                'country' => 'India',
            ],
            'billing_same_as_shipping' => true,
            'subtotal_paise' => 1699900,
            'discount_paise' => 0,
            'amount_paise' => 1699900,
            'shipping_charges' => 0,
            'currency' => 'INR',
            'payment_method' => 'UPI',
            'paid_at' => Carbon::parse('2026-06-24 10:30:00'),
        ]);
        $order->id = 20;

        $order->setRelation('user', new User(['email' => 'rahul@example.com']));
        $order->setRelation('product', new Product(['sku' => '27704']));

        $payload = (new UnicommerceOrderMapper)->toCreateSaleOrderPayload($order);
        $item = $payload['saleOrder']['saleOrderItems'][0];

        $this->assertSame(16999.0, $item['sellingPrice']);
        $this->assertSame(16999.0, $item['totalPrice']);
        $this->assertSame(16999.0, $item['prepaidAmount']);
        $this->assertSame(0, $item['discount']);
        $this->assertArrayNotHasKey('additionalInfo', $payload['saleOrder']);
        $this->assertArrayNotHasKey('totalPrepaidAmount', $payload['saleOrder']);
    }
}
