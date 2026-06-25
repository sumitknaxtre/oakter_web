<?php

namespace App\Console\Commands;

use App\Services\Unicommerce\UnicommerceClient;
use Illuminate\Console\Command;

class TestUnicommerceConnectionCommand extends Command
{
    protected $signature = 'unicommerce:test-connection';

    protected $description = 'Test OAuth connection to Unicommerce (Uniware)';

    public function handle(UnicommerceClient $client): int
    {
        if (! config('unicommerce.enabled')) {
            $this->error('Unicommerce is disabled. Set UNICOMMERCE_ENABLED=true in .env.');

            return self::FAILURE;
        }

        try {
            $result = $client->testConnection();
            $this->info($result['message'] ?? 'Connection successful.');

            return self::SUCCESS;
        } catch (\Throwable $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }
    }
}
