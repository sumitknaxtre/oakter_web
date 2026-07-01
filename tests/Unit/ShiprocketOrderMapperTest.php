<?php

namespace Tests\Unit;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\Shiprocket\ShiprocketOrderMapper;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class ShiprocketOrderMapperTest extends TestCase
{
    public function test_maps_paid_order_to_shiprocket_payload(): void
    {
        config([
            'shiprocket.pickup_location' => 'Delhi Warehouse',
            'shiprocket.order_id_prefix' => 'OAKTER',
        ]);

        $order = new Order([
            'product_snapshot' => [
                'name' => 'Mini UPS',
                'sku' => '27704',
                'package_weight_kg' => 0.2,
                'package_length_cm' => 16.5,
                'package_breadth_cm' => 11,
                'package_height_cm' => 4,
            ],
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
            'subtotal_paise' => 499900,
            'discount_paise' => 0,
            'amount_paise' => 499900,
            'shipping_charges' => 0,
            'payment_method' => 'UPI',
            'paid_at' => Carbon::parse('2026-06-24 10:30:00'),
        ]);
        $order->id = 42;
        $order->product_id = 7;

        $order->setRelation('user', new User(['email' => 'buyer@example.com']));
        $order->setRelation('product', new Product([
            'sku' => '27704',
            'name' => 'Mini UPS',
            'package_weight_kg' => 0.2,
            'package_length_cm' => 16.5,
            'package_breadth_cm' => 11,
            'package_height_cm' => 4,
        ]));

        $payload = (new ShiprocketOrderMapper)->toCreateAdhocOrderPayload($order);

        $this->assertSame('OAKTER-42', $payload['order_id']);
        $this->assertSame('Delhi Warehouse', $payload['pickup_location']);
        $this->assertSame('Prepaid', $payload['payment_method']);
        $this->assertSame(4999, $payload['sub_total']);
        $this->assertSame(0.2, $payload['weight']);
        $this->assertSame(16.5, $payload['length']);
        $this->assertSame(11.0, $payload['breadth']);
        $this->assertSame(4.0, $payload['height']);
        $this->assertSame('27704', $payload['order_items'][0]['sku']);
    }

    public function test_requires_package_details(): void
    {
        config(['shiprocket.pickup_location' => 'Delhi Warehouse']);

        $order = new Order([
            'product_snapshot' => ['name' => 'Mini UPS', 'sku' => '27704'],
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
            'subtotal_paise' => 499900,
            'amount_paise' => 499900,
            'paid_at' => now(),
        ]);
        $order->id = 1;
        $order->setRelation('user', new User(['email' => 'buyer@example.com']));
        $order->setRelation('product', new Product(['sku' => '27704', 'name' => 'Mini UPS']));

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Package weight and dimensions are not configured');

        (new ShiprocketOrderMapper)->toCreateAdhocOrderPayload($order);
    }
}
