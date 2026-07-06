<?php

namespace App\Support;

class AdminSettingKeys
{
    public const SHIPPING_DAYS_ESTIMATE = 'shipping_days_estimate';

    /**
     * @return array<string, string>
     */
    public static function defaults(): array
    {
        return [
            self::SHIPPING_DAYS_ESTIMATE => '10 to 14',
        ];
    }
}
