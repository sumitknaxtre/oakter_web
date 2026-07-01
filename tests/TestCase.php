<?php

namespace Tests;

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use RuntimeException;

abstract class TestCase extends BaseTestCase
{
    public function createApplication()
    {
        $app = parent::createApplication();

        $this->guardAgainstUnsafeDatabaseConnection($app);

        return $app;
    }

    /**
     * Prevent tests from touching the local MySQL database when config is cached.
     */
    protected function guardAgainstUnsafeDatabaseConnection(Application $app): void
    {
        $connection = config('database.default');
        $driver = config("database.connections.{$connection}.driver");

        if ($driver === 'sqlite') {
            return;
        }

        $database = config("database.connections.{$connection}.database");

        throw new RuntimeException(sprintf(
            'Tests must use an isolated sqlite database, not %s/%s. '
            .'Run `php artisan config:clear` (or `composer test`) before running tests.',
            $driver,
            $database,
        ));
    }
}
