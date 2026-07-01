<?php

namespace App\Support;

final class ShiprocketSyncStatus
{
    public const Pending = 'pending';

    public const Synced = 'synced';

    public const Failed = 'failed';

    public const Cancelled = 'cancelled';

    public static function label(string $status): string
    {
        return match ($status) {
            self::Synced => 'Synced',
            self::Failed => 'Failed',
            self::Cancelled => 'Cancelled',
            self::Pending => 'Pending',
            default => ucfirst($status),
        };
    }
}
