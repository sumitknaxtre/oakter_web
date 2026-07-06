<?php

namespace App\Support;

use App\Models\AdminSetting;
use Illuminate\Support\Facades\Cache;

class AdminSettings
{
    public static function get(string $key, ?string $default = null): string
    {
        $default ??= AdminSettingKeys::defaults()[$key] ?? '';

        return Cache::rememberForever(self::cacheKey($key), function () use ($key, $default): string {
            $value = AdminSetting::query()
                ->where('key', $key)
                ->value('value');

            return $value ?? $default;
        });
    }

    public static function set(string $key, string $value): void
    {
        AdminSetting::query()->updateOrCreate(
            ['key' => $key],
            ['value' => $value],
        );

        Cache::forget(self::cacheKey($key));
    }

    public static function shippingDaysEstimate(): string
    {
        return self::get(AdminSettingKeys::SHIPPING_DAYS_ESTIMATE);
    }

    private static function cacheKey(string $key): string
    {
        return "admin_setting.{$key}";
    }
}
