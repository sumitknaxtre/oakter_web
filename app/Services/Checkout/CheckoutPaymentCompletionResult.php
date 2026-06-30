<?php

namespace App\Services\Checkout;

use App\Models\Order;

final class CheckoutPaymentCompletionResult
{
    private function __construct(
        public readonly bool $success,
        public readonly ?Order $order = null,
        public readonly ?string $message = null,
        public readonly bool $alreadyPaid = false,
    ) {}

    public static function paid(Order $order): self
    {
        return new self(success: true, order: $order);
    }

    public static function alreadyPaid(Order $order): self
    {
        return new self(success: true, order: $order, alreadyPaid: true);
    }

    public static function failed(string $message): self
    {
        return new self(success: false, message: $message);
    }
}
