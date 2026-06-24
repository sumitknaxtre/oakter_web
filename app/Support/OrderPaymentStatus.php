<?php

namespace App\Support;

final class OrderPaymentStatus
{
    public const Pending = 1;

    public const Paid = 2;

    public const Refunded = 3;

    public static function label(int $status): string
    {
        return match ($status) {
            self::Pending => 'Pending',
            self::Paid => 'Paid',
            self::Refunded => 'Refunded',
            default => 'Unknown',
        };
    }
}
