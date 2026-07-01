<?php

namespace App\Console\Commands;

use App\Services\Shiprocket\ShiprocketClient;
use Illuminate\Console\Command;

class TestShiprocketConnectionCommand extends Command
{
    protected $signature = 'shiprocket:test-connection';

    protected $description = 'Verify Shiprocket API credentials';

    public function handle(ShiprocketClient $client): int
    {
        if (! config('shiprocket.enabled')) {
            $this->warn('Shiprocket is disabled. Set SHIPROCKET_ENABLED=true to enable sync.');

            return self::SUCCESS;
        }

        try {
            $result = $client->testConnection();
        } catch (\Throwable $exception) {
            $this->error($exception->getMessage());

            return self::FAILURE;
        }

        $this->info($result['message'] ?? 'Shiprocket connection successful.');

        return self::SUCCESS;
    }
}
