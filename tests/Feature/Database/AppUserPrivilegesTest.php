<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Symfony\Component\Finder\SplFileInfo;

/*
 * Guards for the database split: the suite must run as the application user,
 * which may only read and write rows. Any extra privilege fails here.
 */

it('runs as a user limited to SELECT, INSERT, UPDATE and DELETE', function () {
    $database = DB::connection()->getDatabaseName();

    $grants = collect(DB::select('SHOW GRANTS'))
        ->map(fn (object $row): string => (string) array_values((array) $row)[0]);

    $unexpected = $grants->reject(fn (string $grant): bool => str_starts_with($grant, 'GRANT USAGE ON *.* TO ')
        || preg_match('/^GRANT SELECT, INSERT, UPDATE, DELETE ON `[A-Za-z0-9_]+`\.\* TO /', $grant) === 1);

    expect($unexpected->all())->toBe([])
        ->and($grants->contains(fn (string $grant): bool => str_contains($grant, "SELECT, INSERT, UPDATE, DELETE ON `{$database}`.*")))->toBeTrue()
        ->and($grants->implode("\n"))->not->toContain('WITH GRANT OPTION');
});

it('cannot change the schema', function () {
    // A fresh connection keeps the DDL attempt outside the test transaction.
    config(['database.connections.privilege_probe' => config('database.connections.mysql')]);

    try {
        expect(fn () => DB::connection('privilege_probe')->statement('CREATE TABLE privilege_probe (id INT)'))
            ->toThrow(Exception::class, 'CREATE command denied');
    } finally {
        DB::purge('privilege_probe');
    }
});

it('never refreshes or migrates the database from tests', function () {
    // The app user cannot run DDL, so these traits would fail (or worse,
    // succeed with an owner connection and wipe a database).
    $forbidden = '/Testing\\\\(RefreshDatabase|LazilyRefreshDatabase|DatabaseMigrations|DatabaseTruncation)\b/';

    $offenders = collect(File::allFiles(base_path('tests')))
        ->reject(fn (SplFileInfo $file): bool => $file->getRealPath() === __FILE__)
        ->filter(fn (SplFileInfo $file): bool => preg_match($forbidden, $file->getContents()) === 1)
        ->map(fn (SplFileInfo $file): string => $file->getRelativePathname())
        ->values();

    expect($offenders->all())->toBe([]);
});
