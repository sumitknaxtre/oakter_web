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
        $this->line('Enabled: '.(config('shiprocket.enabled') ? 'yes' : 'no'));
        $this->line('Pickup location: '.(config('shiprocket.pickup_location') ?: '(not set)'));
        $this->line('API email: '.(config('shiprocket.email') ?: '(not set)'));

        if (! config('shiprocket.enabled')) {
            $this->warn('Shiprocket is disabled. Set SHIPROCKET_ENABLED=true in .env, then run config:cache and queue:restart.');

            return self::FAILURE;
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
