<?php

use App\Actions\Fortify\CreateNewUser;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;
use Illuminate\Testing\PendingCommand;

const STRONG_PASSWORD = 'Lote-Prueba-2026-xyz';

function askForUser(string $email, string $password, ?string $confirmation = null): PendingCommand
{
    return test()->artisan('users:create')
        ->expectsQuestion(__('Name'), 'Usuaria de Prueba')
        ->expectsQuestion(__('Email'), $email)
        ->expectsQuestion(__('Password'), $password)
        ->expectsQuestion(__('Confirm password'), $confirmation ?? $password);
}

it('creates a verified user with a hashed password and never prints it', function () {
    askForUser('usuaria@example.com', STRONG_PASSWORD)
        ->expectsOutputToContain('Usuario creado: usuaria@example.com')
        ->doesntExpectOutputToContain(STRONG_PASSWORD)
        ->assertSuccessful();

    $user = User::query()->where('email', 'usuaria@example.com')->sole();

    expect($user->password)->not->toBe(STRONG_PASSWORD)
        ->and(Hash::check(STRONG_PASSWORD, $user->password))->toBeTrue()
        ->and($user->email_verified_at)->not->toBeNull();
});

it('rejects a password confirmation that does not match', function () {
    askForUser('usuaria@example.com', STRONG_PASSWORD, 'Otra-Clave-2026-abc')
        ->expectsOutputToContain('no coincide')
        ->doesntExpectOutputToContain(STRONG_PASSWORD)
        ->assertFailed();

    expect(User::query()->where('email', 'usuaria@example.com')->exists())->toBeFalse();
});

it('rejects a password that is too short', function () {
    askForUser('usuaria@example.com', 'corta')
        ->doesntExpectOutputToContain('corta')
        ->assertFailed();

    expect(User::query()->where('email', 'usuaria@example.com')->exists())->toBeFalse();
});

it('rejects an e-mail address that is already taken', function () {
    User::factory()->create(['email' => 'usuaria@example.com']);

    askForUser('usuaria@example.com', STRONG_PASSWORD)
        ->expectsOutputToContain('ya ha sido registrado')
        ->assertFailed();

    expect(User::query()->where('email', 'usuaria@example.com')->count())->toBe(1);
});

it('offers no way to pass the password on the command line', function () {
    $definition = Artisan::all()['users:create']->getDefinition();

    expect($definition->getOptions())->toBe([])
        ->and($definition->getArguments())->toBe([]);
});

it('redacts the user input from stack traces', function () {
    $parameter = (new ReflectionMethod(CreateNewUser::class, 'create'))->getParameters()[0];

    expect($parameter->getAttributes(SensitiveParameter::class))->toHaveCount(1);
});
