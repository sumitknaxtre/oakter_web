<?php

namespace Tests\Unit;

use App\Models\AdminSetting;
use App\Support\AdminSettingKeys;
use App\Support\AdminSettings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class AdminSettingsTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_returns_default_when_setting_is_missing(): void
    {
        $this->assertSame('10 to 14', AdminSettings::shippingDaysEstimate());
    }

    public function test_it_reads_and_updates_settings_with_cache_invalidation(): void
    {
        AdminSetting::query()
            ->where('key', AdminSettingKeys::SHIPPING_DAYS_ESTIMATE)
            ->update(['value' => '7 to 10']);

        Cache::forget('admin_setting.'.AdminSettingKeys::SHIPPING_DAYS_ESTIMATE);

        $this->assertSame('7 to 10', AdminSettings::shippingDaysEstimate());

        AdminSettings::set(AdminSettingKeys::SHIPPING_DAYS_ESTIMATE, '5 to 7');

        $this->assertSame('5 to 7', AdminSettings::shippingDaysEstimate());
        $this->assertDatabaseHas('admin_settings', [
            'key' => AdminSettingKeys::SHIPPING_DAYS_ESTIMATE,
            'value' => '5 to 7',
        ]);
    }
}
