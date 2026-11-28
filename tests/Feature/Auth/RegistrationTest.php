<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Laravel\Fortify\Features;
use Tests\TestCase;

/**
 * Public registration is disabled: users are created by an administrator
 * (php artisan users:create).
 */
class RegistrationTest extends TestCase
{
    public function test_registration_feature_is_disabled(): void
    {
        $this->assertFalse(Features::enabled(Features::registration()));
    }

    public function test_registration_screen_is_not_available(): void
    {
        $this->get('/register')->assertNotFound();
    }

    public function test_registration_cannot_be_submitted(): void
    {
        $this->post('/register', [
            'name' => 'John Doe',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ])->assertNotFound();

        $this->assertGuest();
        $this->assertDatabaseMissing(User::class, ['email' => 'test@example.com']);
    }

    public function test_login_screen_does_not_offer_sign_up(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertDontSee(__('Sign up'))
            ->assertDontSee('/register');
    }
}
