<?php

namespace Tests\Feature;

use Tests\TestCase;

class CheckoutPaymentCallbackTest extends TestCase
{
    public function test_payment_callback_accepts_post_from_razorpay_redirect(): void
    {
        $response = $this->post('/checkout/payment/callback', [
            'razorpay_order_id' => 'order_test',
            'razorpay_payment_id' => 'pay_test',
            'razorpay_signature' => 'invalid-signature',
        ]);

        $this->assertNotSame(405, $response->getStatusCode());
        $this->assertNotSame(419, $response->getStatusCode());
    }
}
