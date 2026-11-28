<?php

namespace Tests;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\DB;
use Laravel\Fortify\Features;
use RuntimeException;

abstract class TestCase extends BaseTestCase
{
    /*
     * Tests run as the application database user, which cannot run DDL.
     * The schema is migrated beforehand with the owner connection
     * (php artisan migrate --database=mysql_owner --env=testing) and every
     * test runs inside a transaction that is rolled back afterwards.
     */
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        $this->ensureTestDatabase();
    }

    /**
     * Abort before touching data if the suite is not pointed at a test database
     * (for example, when .env.testing is missing and .env is used instead).
     */
    private function ensureTestDatabase(): void
    {
        $database = DB::connection()->getDatabaseName();

        if (DB::getDefaultConnection() !== 'mysql' || preg_match('/_test(_hook)?$/', $database) !== 1) {
            throw new RuntimeException(
                "Tests must run on the 'mysql' connection against a *_test or *_test_hook database; got [".DB::getDefaultConnection()."] {$database}."
            );
        }
    }

    protected function skipUnlessFortifyHas(string $feature, ?string $message = null): void
    {
        if (! Features::enabled($feature)) {
            $this->markTestSkipped($message ?? "Fortify feature [{$feature}] is not enabled.");
        }
    }
}
