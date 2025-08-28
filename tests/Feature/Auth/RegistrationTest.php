<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_requires_authentication(): void
    {
        $response = $this->get('/register');

        $response->assertRedirect(route('login'));
    }

    public function test_registration_screen_requires_permission(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/register');

        $response->assertStatus(403);
    }

    public function test_registration_screen_can_be_rendered_with_permission(): void
    {
        $user = User::factory()->create();
        $permission = Permission::create([
            'name' => 'Crear Usuarios',
            'module' => 'users',
            'action' => 'create',
            'description' => 'Test permission'
        ]);
        $user->permissions()->attach($permission);

        $response = $this->actingAs($user)->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_be_registered_by_authorized_user(): void
    {
        $user = User::factory()->create();
        $permission = Permission::create([
            'name' => 'Crear Usuarios',
            'module' => 'users',
            'action' => 'create',
            'description' => 'Test permission'
        ]);
        $user->permissions()->attach($permission);

        $response = $this->actingAs($user)->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect(route('dashboard', absolute: false));
        $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
    }

    public function test_registration_requires_permission(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('users', ['email' => 'test@example.com']);
    }
}
