<?php

namespace Tests\Unit;

use App\Support\OrderAttribution;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class OrderAttributionTest extends TestCase
{
    #[Test]
    public function test_it_labels_meta_ad_clicks_from_fbc(): void
    {
        $this->assertSame('Meta ad click', OrderAttribution::label([
            'fbc' => 'fb.1.123.abc',
        ]));
    }

    #[Test]
    public function test_it_labels_instagram_utm_traffic(): void
    {
        $this->assertSame('Instagram (UTM)', OrderAttribution::label([
            'utm_source' => 'instagram',
            'utm_medium' => 'story',
        ]));
    }

    #[Test]
    public function test_it_labels_direct_traffic_when_no_signals_exist(): void
    {
        $this->assertSame('Direct / unknown', OrderAttribution::label([
            'landing_url' => 'https://oakter.com/',
        ]));
    }
}
