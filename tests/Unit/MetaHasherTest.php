<?php

namespace Tests\Unit;

use App\Services\Meta\MetaHasher;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MetaHasherTest extends TestCase
{
    #[Test]
    public function test_it_hashes_email_in_lowercase(): void
    {
        $hash = MetaHasher::email('User@Example.COM');

        $this->assertSame(hash('sha256', 'user@example.com'), $hash);
    }

    #[Test]
    public function test_it_normalizes_indian_phone_numbers(): void
    {
        $hash = MetaHasher::phone('9876543210');

        $this->assertSame(hash('sha256', '919876543210'), $hash);
    }

    #[Test]
    public function test_it_hashes_country_as_iso_code(): void
    {
        $hash = MetaHasher::country('India');

        $this->assertSame(hash('sha256', 'in'), $hash);
    }
}
