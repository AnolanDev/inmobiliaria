<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Models\Role;
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

    public function test_registration_screen_requires_admin_role(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/register');

        $response->assertStatus(403);
    }

    public function test_registration_screen_can_be_rendered_by_admin(): void
    {
        $user = User::factory()->create();
        $adminRole = Role::create([
            'name' => 'Administrador',
            'slug' => 'admin',
            'description' => 'Test admin role'
        ]);
        $user->roles()->attach($adminRole);

        $response = $this->actingAs($user)->get('/register');

        $response->assertStatus(200);
    }

    public function test_registration_screen_can_be_rendered_by_super_admin(): void
    {
        $user = User::factory()->create();
        $superAdminRole = Role::create([
            'name' => 'Super Administrador',
            'slug' => 'super-admin',
            'description' => 'Test super admin role'
        ]);
        $user->roles()->attach($superAdminRole);

        $response = $this->actingAs($user)->get('/register');

        $response->assertStatus(200);
    }

    public function test_new_users_can_be_registered_by_admin(): void
    {
        $user = User::factory()->create();
        $adminRole = Role::create([
            'name' => 'Administrador',
            'slug' => 'admin',
            'description' => 'Test admin role'
        ]);
        $user->roles()->attach($adminRole);

        $response = $this->actingAs($user)->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect(route('dashboard', absolute: false));
        $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
    }

    public function test_new_users_can_be_registered_by_super_admin(): void
    {
        $user = User::factory()->create();
        $superAdminRole = Role::create([
            'name' => 'Super Administrador',
            'slug' => 'super-admin',
            'description' => 'Test super admin role'
        ]);
        $user->roles()->attach($superAdminRole);

        $response = $this->actingAs($user)->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertRedirect(route('dashboard', absolute: false));
        $this->assertDatabaseHas('users', ['email' => 'test@example.com']);
    }

    public function test_registration_requires_admin_role(): void
    {
        $user = User::factory()->create();
        $agentRole = Role::create([
            'name' => 'Agente',
            'slug' => 'agent',
            'description' => 'Test agent role'
        ]);
        $user->roles()->attach($agentRole);

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
