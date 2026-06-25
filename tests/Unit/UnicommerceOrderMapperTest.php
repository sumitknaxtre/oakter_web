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
    public function test_it_maps_a_prepaid_order_to_a_create_sale_order_payload(): void
    {
        config([
            'unicommerce.channel' => 'Oakter Website',
            'unicommerce.shipping_method' => 'STD',
            'unicommerce.order_code_prefix' => 'OAKTER',
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
            'discount_paise' => 9999,
            'amount_paise' => 1689901,
            'shipping_charges' => 0,
            'currency' => 'INR',
            'payment_method' => 'UPI',
            'paid_at' => Carbon::parse('2026-06-24 10:30:00'),
        ]);
        $order->id = 15;

        $order->setRelation('user', new User(['email' => 'rahul@example.com']));
        $order->setRelation('product', new Product(['sku' => 'STUDIO-AC-5000']));

        $payload = (new UnicommerceOrderMapper)->toCreateSaleOrderPayload($order);

        $this->assertSame('OAKTER-15', $payload['saleOrder']['code']);
        $this->assertSame('#15', $payload['saleOrder']['displayOrderCode']);
        $this->assertFalse($payload['saleOrder']['cashOnDelivery']);
        $this->assertSame('WALLET', $payload['saleOrder']['paymentInstrument']);
        $this->assertSame('STUDIO-AC-5000', $payload['saleOrder']['saleOrderItems'][0]['itemSku']);
        $this->assertSame(16899.01, $payload['saleOrder']['saleOrderItems'][0]['prepaidAmount']);
        $this->assertSame('shipping', $payload['saleOrder']['shippingAddress']['referenceId']);
    }
}
