<?php

namespace Tests\Unit;

use App\Exports\OrderPaymentIdsExport;
use App\Models\Order;
use App\Models\User;
use Tests\TestCase;

class OrderPaymentIdsExportTest extends TestCase
{
    public function test_it_exports_only_payment_matching_columns(): void
    {
        config(['unicommerce.display_order_code_prefix' => 'NEW']);

        $order = new Order([
            'shipping_snapshot' => [
                'first_name' => 'Rahul',
                'last_name' => 'Sharma',
                'phone' => '9876543210',
            ],
            'razorpay_payment_id' => 'pay_123',
        ]);
        $order->id = 1039;
        $order->setRelation('user', new User(['email' => 'rahul@example.com']));

        $this->assertSame([
            'Order ID',
            'Customer name',
            'Email',
            'Phone',
            'Razorpay payment ID',
        ], OrderPaymentIdsExport::headers());

        $this->assertSame([
            'NEW1039',
            'Rahul Sharma',
            'rahul@example.com',
            '9876543210',
            'pay_123',
        ], OrderPaymentIdsExport::row($order));
    }
}
