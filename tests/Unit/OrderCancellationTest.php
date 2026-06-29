<?php

namespace Tests\Unit;

use App\Models\Order;
use App\Support\OrderFulfillmentStatus;
use App\Support\OrderPaymentStatus;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class OrderCancellationTest extends TestCase
{
    #[Test]
    public function test_paid_order_with_payment_id_can_be_cancelled(): void
    {
        $order = new Order([
            'status' => 'paid',
            'payment_status' => OrderPaymentStatus::Paid,
            'fulfillment_status' => OrderFulfillmentStatus::Pending,
            'razorpay_payment_id' => 'pay_test123',
        ]);

        $this->assertTrue($order->canBeCancelled());
    }

    #[Test]
    public function test_refunded_order_cannot_be_cancelled_again(): void
    {
        $order = new Order([
            'status' => 'cancelled',
            'payment_status' => OrderPaymentStatus::Refunded,
            'fulfillment_status' => OrderFulfillmentStatus::Cancelled,
            'razorpay_payment_id' => 'pay_test123',
        ]);

        $this->assertFalse($order->canBeCancelled());
    }
}
