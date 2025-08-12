<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class UserManagementTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create test roles and permissions
        $this->createTestRolesAndPermissions();
    }

    public function test_admin_can_view_users_index()
    {
        $admin = User::factory()->create();
        $adminRole = Role::where('slug', 'super-admin')->first();
        $admin->assignRole($adminRole);

        $response = $this->actingAs($admin)
            ->get(route('users.index'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Users/Index')
            ->has('users')
        );
    }

    public function test_user_without_permission_cannot_view_users()
    {
        $user = User::factory()->create();
        $role = Role::factory()->create(['name' => 'Basic User']);
        $user->assignRole($role);

        $response = $this->actingAs($user)
            ->get(route('users.index'));

        $response->assertStatus(403);
    }

    public function test_admin_can_create_user()
    {
        Storage::fake('public');
        
        $admin = User::factory()->create();
        $adminRole = Role::where('slug', 'super-admin')->first();
        $admin->assignRole($adminRole);

        $userData = [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'phone' => $this->faker->phoneNumber,
            'position' => 'Test Position',
            'is_active' => true,
            'force_password_change' => false,
            'roles' => [Role::first()->id],
        ];

        $response = $this->actingAs($admin)
            ->post(route('users.store'), $userData);

        $response->assertRedirect(route('users.index'));
        $this->assertDatabaseHas('users', [
            'email' => $userData['email'],
            'name' => $userData['name']
        ]);
    }

    public function test_user_creation_validates_required_fields()
    {
        $admin = User::factory()->create();
        $adminRole = Role::where('slug', 'super-admin')->first();
        $admin->assignRole($adminRole);

        $response = $this->actingAs($admin)
            ->post(route('users.store'), []);

        $response->assertSessionHasErrors(['name', 'email', 'password']);
    }

    public function test_user_creation_validates_unique_email()
    {
        $admin = User::factory()->create();
        $adminRole = Role::where('slug', 'super-admin')->first();
        $admin->assignRole($adminRole);

        $existingUser = User::factory()->create();

        $userData = [
            'name' => $this->faker->name,
            'email' => $existingUser->email, // Duplicate email
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        $response = $this->actingAs($admin)
            ->post(route('users.store'), $userData);

        $response->assertSessionHasErrors(['email']);
    }

    public function test_admin_can_upload_user_avatar()
    {
        Storage::fake('public');
        
        $admin = User::factory()->create();
        $adminRole = Role::where('slug', 'super-admin')->first();
        $admin->assignRole($adminRole);

        $avatar = UploadedFile::fake()->image('avatar.jpg', 100, 100);

        $userData = [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'avatar' => $avatar,
            'is_active' => true,
            'roles' => [Role::first()->id],
        ];

        $response = $this->actingAs($admin)
            ->post(route('users.store'), $userData);

        $response->assertRedirect(route('users.index'));
        Storage::disk('public')->assertExists('avatars/' . $avatar->hashName());
    }

    public function test_admin_can_update_user()
    {
        $admin = User::factory()->create();
        $adminRole = Role::where('slug', 'super-admin')->first();
        $admin->assignRole($adminRole);

        $user = User::factory()->create();
        $newRole = Role::factory()->create();

        $updateData = [
            'name' => 'Updated Name',
            'email' => $user->email,
            'phone' => '+57 300 123 4567',
            'position' => 'Updated Position',
            'is_active' => false,
            'roles' => [$newRole->id],
        ];

        $response = $this->actingAs($admin)
            ->patch(route('users.update', $user), $updateData);

        $response->assertRedirect(route('users.index'));
        
        $user->refresh();
        $this->assertEquals('Updated Name', $user->name);
        $this->assertEquals('+57 300 123 4567', $user->phone);
        $this->assertFalse($user->is_active);
        $this->assertTrue($user->hasRole($newRole));
    }

    public function test_admin_can_delete_user()
    {
        $admin = User::factory()->create();
        $adminRole = Role::where('slug', 'super-admin')->first();
        $admin->assignRole($adminRole);

        $user = User::factory()->create();

        $response = $this->actingAs($admin)
            ->delete(route('users.destroy', $user));

        $response->assertRedirect(route('users.index'));
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    public function test_user_search_functionality()
    {
        $admin = User::factory()->create();
        $adminRole = Role::where('slug', 'super-admin')->first();
        $admin->assignRole($adminRole);

        $user1 = User::factory()->create(['name' => 'John Doe']);
        $user2 = User::factory()->create(['name' => 'Jane Smith']);

        $response = $this->actingAs($admin)
            ->get(route('users.index', ['search' => 'John']));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->has('users.data', 1)
            ->where('users.data.0.name', 'John Doe')
        );
    }

    private function createTestRolesAndPermissions()
    {
        // Create permissions
        $permissions = [
            ['name' => 'Ver usuarios', 'slug' => 'users-view', 'module' => 'users', 'action' => 'view'],
            ['name' => 'Crear usuarios', 'slug' => 'users-create', 'module' => 'users', 'action' => 'create'],
            ['name' => 'Editar usuarios', 'slug' => 'users-edit', 'module' => 'users', 'action' => 'edit'],
            ['name' => 'Eliminar usuarios', 'slug' => 'users-delete', 'module' => 'users', 'action' => 'delete'],
        ];

        foreach ($permissions as $permission) {
            Permission::create($permission);
        }

        // Create super admin role
        $superAdminRole = Role::create([
            'name' => 'Super Administrador',
            'slug' => 'super-admin',
            'description' => 'Acceso completo al sistema',
            'color' => '#dc2626',
            'is_system_role' => true,
            'is_active' => true,
            'sort_order' => 1
        ]);

        // Assign all permissions to super admin
        $superAdminRole->syncPermissions(Permission::all());
    }
}