<?php

namespace Tests\Unit;

use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DatabaseConnectionTest extends TestCase
{
    public function test_uses_isolated_sqlite_database(): void
    {
        $this->assertSame('sqlite', DB::connection()->getDriverName());
        $this->assertSame(':memory:', DB::connection()->getDatabaseName());
    }
}
