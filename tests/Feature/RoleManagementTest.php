<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class RoleManagementTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->createTestPermissions();
    }

    public function test_admin_can_view_roles_index()
    {
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin)
            ->get(route('roles.index'));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->component('Roles/Index')
            ->has('roles')
        );
    }

    public function test_admin_can_create_role()
    {
        $admin = $this->createAdminUser();
        $permissions = Permission::take(3)->pluck('id')->toArray();

        $roleData = [
            'name' => 'Test Role',
            'description' => 'A test role',
            'color' => '#3b82f6',
            'sort_order' => 10,
            'is_active' => true,
            'permissions' => $permissions,
        ];

        $response = $this->actingAs($admin)
            ->post(route('roles.store'), $roleData);

        $response->assertRedirect(route('roles.index'));
        
        $this->assertDatabaseHas('roles', [
            'name' => 'Test Role',
            'description' => 'A test role'
        ]);

        $role = Role::where('name', 'Test Role')->first();
        $this->assertCount(3, $role->permissions);
    }

    public function test_role_creation_validates_required_fields()
    {
        $admin = $this->createAdminUser();

        $response = $this->actingAs($admin)
            ->post(route('roles.store'), []);

        $response->assertSessionHasErrors(['name', 'color']);
    }

    public function test_admin_can_update_role()
    {
        $admin = $this->createAdminUser();
        $role = Role::factory()->create(['name' => 'Original Name']);
        $permissions = Permission::take(2)->pluck('id')->toArray();

        $updateData = [
            'name' => 'Updated Role Name',
            'description' => 'Updated description',
            'color' => '#ef4444',
            'sort_order' => 5,
            'is_active' => false,
            'permissions' => $permissions,
        ];

        $response = $this->actingAs($admin)
            ->patch(route('roles.update', $role), $updateData);

        $response->assertRedirect(route('roles.index'));
        
        $role->refresh();
        $this->assertEquals('Updated Role Name', $role->name);
        $this->assertEquals('Updated description', $role->description);
        $this->assertFalse($role->is_active);
        $this->assertCount(2, $role->permissions);
    }

    public function test_system_roles_cannot_be_updated()
    {
        $admin = $this->createAdminUser();
        $systemRole = Role::factory()->create([
            'name' => 'System Role',
            'is_system_role' => true
        ]);

        $updateData = [
            'name' => 'Hacked Name',
            'is_active' => false,
        ];

        $response = $this->actingAs($admin)
            ->patch(route('roles.update', $systemRole), $updateData);

        // Should either redirect back with error or ignore changes
        $systemRole->refresh();
        $this->assertEquals('System Role', $systemRole->name);
        $this->assertTrue($systemRole->is_active);
    }

    public function test_admin_can_toggle_role_status()
    {
        $admin = $this->createAdminUser();
        $role = Role::factory()->create(['is_active' => true]);

        $response = $this->actingAs($admin)
            ->patch(route('roles.toggle-status', $role));

        $response->assertRedirect();
        
        $role->refresh();
        $this->assertFalse($role->is_active);
    }

    public function test_system_roles_cannot_be_deactivated()
    {
        $admin = $this->createAdminUser();
        $systemRole = Role::factory()->create([
            'is_active' => true,
            'is_system_role' => true
        ]);

        $response = $this->actingAs($admin)
            ->patch(route('roles.toggle-status', $systemRole));

        $systemRole->refresh();
        $this->assertTrue($systemRole->is_active);
    }

    public function test_admin_can_duplicate_role()
    {
        $admin = $this->createAdminUser();
        $originalRole = Role::factory()->create(['name' => 'Original Role']);
        $permissions = Permission::take(3)->get();
        $originalRole->syncPermissions($permissions);

        $response = $this->actingAs($admin)
            ->post(route('roles.duplicate', $originalRole));

        $response->assertRedirect();
        
        $duplicatedRole = Role::where('name', 'Original Role (Copia)')->first();
        $this->assertNotNull($duplicatedRole);
        $this->assertCount(3, $duplicatedRole->permissions);
    }

    public function test_admin_can_delete_role_without_users()
    {
        $admin = $this->createAdminUser();
        $role = Role::factory()->create();

        $response = $this->actingAs($admin)
            ->delete(route('roles.destroy', $role));

        $response->assertRedirect(route('roles.index'));
        $this->assertDatabaseMissing('roles', ['id' => $role->id]);
    }

    public function test_cannot_delete_role_with_assigned_users()
    {
        $admin = $this->createAdminUser();
        $role = Role::factory()->create();
        $user = User::factory()->create();
        $user->assignRole($role);

        $response = $this->actingAs($admin)
            ->delete(route('roles.destroy', $role));

        $response->assertRedirect();
        $this->assertDatabaseHas('roles', ['id' => $role->id]);
    }

    public function test_system_roles_cannot_be_deleted()
    {
        $admin = $this->createAdminUser();
        $systemRole = Role::factory()->create(['is_system_role' => true]);

        $response = $this->actingAs($admin)
            ->delete(route('roles.destroy', $systemRole));

        $response->assertRedirect();
        $this->assertDatabaseHas('roles', ['id' => $systemRole->id]);
    }

    public function test_role_search_functionality()
    {
        $admin = $this->createAdminUser();
        $role1 = Role::factory()->create(['name' => 'Manager Role']);
        $role2 = Role::factory()->create(['name' => 'Developer Role']);

        $response = $this->actingAs($admin)
            ->get(route('roles.index', ['search' => 'Manager']));

        $response->assertStatus(200);
        $response->assertInertia(fn ($page) => $page
            ->has('roles.data', 1)
            ->where('roles.data.0.name', 'Manager Role')
        );
    }

    private function createAdminUser()
    {
        $admin = User::factory()->create();
        $adminRole = Role::factory()->create([
            'name' => 'Super Admin',
            'slug' => 'super-admin',
            'is_system_role' => true
        ]);
        
        // Give admin all permissions
        $adminRole->syncPermissions(Permission::all());
        $admin->assignRole($adminRole);
        
        return $admin;
    }

    private function createTestPermissions()
    {
        $modules = ['users', 'roles', 'properties', 'clients'];
        $actions = ['view', 'create', 'edit', 'delete'];

        foreach ($modules as $module) {
            foreach ($actions as $action) {
                Permission::create([
                    'name' => ucfirst($action) . ' ' . $module,
                    'slug' => $module . '-' . $action,
                    'module' => $module,
                    'action' => $action,
                    'is_active' => true
                ]);
            }
        }
    }
}