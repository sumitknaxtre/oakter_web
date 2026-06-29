<?php

namespace App\Support;

final class OrderFulfillmentStatus
{
    public const Pending = 'pending';

    public const Fulfilled = 'fulfilled';

    public const Cancelled = 'cancelled';

    public static function label(string $status): string
    {
        return match ($status) {
            self::Fulfilled => 'Fulfilled',
            self::Cancelled => 'Cancelled',
            self::Pending => 'Pending',
            default => ucfirst($status),
        };
    }
}
