<?php

use App\Models\User;
use Illuminate\Support\Facades\Validator;

it('uses Spanish as locale and fallback locale', function () {
    expect(app()->getLocale())->toBe('es')
        ->and(config('app.fallback_locale'))->toBe('es');
});

it('shows validation messages in Spanish', function () {
    $errors = Validator::make(['email' => ''], ['email' => 'required'])->errors();

    expect($errors->first('email'))->toBe('El campo correo electrónico es obligatorio.');
});

it('renders the login screen in Spanish', function () {
    $this->get(route('login'))
        ->assertOk()
        ->assertSee('<html lang="es"', false)
        ->assertSee('Inicie sesión en su cuenta')
        ->assertDontSee('Log in to your account');
});

it('translates Livewire page titles', function () {
    $this->actingAs(User::factory()->create())
        ->get(route('profile.edit'))
        ->assertOk()
        ->assertSee('Ajustes de perfil')
        ->assertDontSee('Profile settings');
});

it('rejects invalid credentials with a Spanish message', function () {
    $user = User::factory()->create();

    $this->post(route('login.store'), ['email' => $user->email, 'password' => 'wrong-password'])
        ->assertSessionHasErrors(['email' => 'Estas credenciales no coinciden con nuestros registros.']);
});
