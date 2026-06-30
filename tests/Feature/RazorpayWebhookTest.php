<?php

namespace Tests\Feature;

use Tests\TestCase;

class RazorpayWebhookTest extends TestCase
{
    public function test_webhook_does_not_require_csrf_token(): void
    {
        $response = $this->postJson('/razorpay/webhook', [
            'event' => 'payment.captured',
        ], [
            'X-Razorpay-Signature' => 'invalid-signature',
        ]);

        $this->assertNotSame(419, $response->getStatusCode());
    }
}
