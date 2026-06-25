<?php

namespace App\Support;

final class UnicommerceSyncStatus
{
    public const Pending = 'pending';

    public const Synced = 'synced';

    public const Failed = 'failed';

    public static function label(string $status): string
    {
        return match ($status) {
            self::Synced => 'Synced',
            self::Failed => 'Failed',
            self::Pending => 'Pending',
            default => ucfirst($status),
        };
    }
}
