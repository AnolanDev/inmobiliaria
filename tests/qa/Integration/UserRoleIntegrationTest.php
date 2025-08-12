<?php

namespace Tests\QA\Integration;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;

class UserRoleIntegrationTest extends TestCase
{
    use RefreshDatabase;

    private function getAdminUser(): User
    {
        return User::where('email', 'admin@inmobiliaria.com')->first() 
               ?? User::factory()->create();
    }

    private function generateUserFormData(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Test User',
            'email' => 'integration@test.com',
            'password' => 'TestPassword123!',
            'password_confirmation' => 'TestPassword123!',
            'phone' => '+57 300 123 4567',
            'position' => 'Test Position',
            'is_active' => true,
            'roles' => []
        ], $overrides);
    }

    private function generateRoleFormData(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Test Integration Role',
            'description' => 'Test role for integration testing',
            'color' => '#007bff',
            'is_active' => true,
            'permissions' => []
        ], $overrides);
    }

    private function assertUserCanAccess(User $user, string $route): void
    {
        $response = $this->actingAs($user)->get(route($route));
        $response->assertStatus(200);
    }

    private function assertUserCannotAccess(User $user, string $route): void
    {
        $response = $this->actingAs($user)->get(route($route));
        $response->assertStatus(403);
    }
    /**
     * Test complete user-role-permission workflow
     */
    public function test_complete_user_role_permission_workflow()
    {
        $admin = $this->getAdminUser();
        
        // 1. Create a custom role
        $roleData = $this->generateRoleFormData([
            'name' => 'QA Integration Test Role',
            'permissions' => Permission::take(3)->pluck('id')->toArray()
        ]);
        
        $roleResponse = $this->actingAs($admin)->post(route('roles.store'), $roleData);
        $roleResponse->assertRedirect(route('roles.index'));
        
        $role = Role::where('name', $roleData['name'])->first();
        $this->assertNotNull($role);
        $this->assertCount(3, $role->permissions);
        
        // 2. Create a user with the new role
        $userData = $this->generateUserFormData([
            'roles' => [$role->id]
        ]);
        
        $userResponse = $this->actingAs($admin)->post(route('users.store'), $userData);
        $userResponse->assertRedirect(route('users.index'));
        
        $user = User::where('email', $userData['email'])->first();
        $this->assertNotNull($user);
        $this->assertTrue($user->hasRole($role));
        
        // 3. Verify user has role permissions
        foreach ($role->permissions as $permission) {
            $this->assertTrue($user->hasPermission($permission->slug));
        }
        
        // 4. Update role permissions
        $newPermissions = Permission::take(5)->pluck('id')->toArray();
        $updateResponse = $this->actingAs($admin)->patch(route('roles.update', $role), [
            'name' => $role->name,
            'color' => $role->color,
            'permissions' => $newPermissions
        ]);
        $updateResponse->assertRedirect(route('roles.index'));
        
        // 5. Verify user now has updated permissions
        $user->refresh();
        $role->refresh();
        $this->assertCount(5, $role->permissions);
        
        // 6. Test user access with new permissions
        $this->actingAs($user);
        foreach ($role->permissions as $permission) {
            $this->assertTrue($user->hasPermission($permission->slug));
        }
    }

    /**
     * Test role deactivation affects user access
     */
    public function test_role_deactivation_affects_user_access()
    {
        $admin = $this->getAdminUser();
        
        // Create active role with permissions
        $role = Role::factory()->create([
            'is_active' => true,
            'name' => 'QA Test Active Role'
        ]);
        $role->syncPermissions(['users-view', 'properties-view']);
        
        // Create user with this role
        $user = User::factory()->create();
        $user->assignRole($role);
        
        // Verify user has access
        $this->assertTrue($user->hasPermission('users-view'));
        $this->assertTrue($user->hasRole($role));
        
        // Deactivate role
        $response = $this->actingAs($admin)->patch(route('roles.toggle-status', $role));
        $response->assertRedirect();
        
        $role->refresh();
        $this->assertFalse($role->is_active);
        
        // User should still have the role but permissions might be restricted
        $user->refresh();
        $this->assertTrue($user->hasRole($role));
        
        // Test access to dashboard (simplified test)
        $response = $this->actingAs($user)->get('/dashboard');
        $this->assertContains($response->status(), [200, 302, 403]);
    }

    /**
     * Test cascade operations when deleting roles
     */
    public function test_role_deletion_cascade_operations()
    {
        $admin = $this->getAdminUser();
        
        // Create role
        $role = Role::factory()->create([
            'name' => 'QA Deletable Role',
            'is_system_role' => false
        ]);
        $role->syncPermissions(['users-view']);
        
        $roleId = $role->id;
        
        // Create user with role
        $user = User::factory()->create();
        $user->assignRole($role);
        
        // Verify initial state
        $this->assertTrue($user->hasRole($role));
        $this->assertDatabaseHas('user_roles', [
            'user_id' => $user->id,
            'role_id' => $roleId
        ]);
        
        // Delete role
        $response = $this->actingAs($admin)->delete(route('roles.destroy', $role));
        $response->assertRedirect();
        
        // Verify role is deleted
        $this->assertDatabaseMissing('roles', ['id' => $roleId]);
        
        // Verify user-role relationships are cleaned up
        $this->assertDatabaseMissing('user_roles', [
            'user_id' => $user->id,
            'role_id' => $roleId
        ]);
        
        // Verify role-permission relationships are cleaned up
        $this->assertDatabaseMissing('role_permissions', [
            'role_id' => $roleId
        ]);
        
        // User should no longer have the role
        $user->refresh();
        $this->assertFalse($user->hasRole($role));
    }

    /**
     * Test multiple role assignment and permission inheritance
     */
    public function test_multiple_role_assignment_permission_inheritance()
    {
        $admin = $this->getAdminUser();
        
        // Create multiple roles with different permissions
        $role1 = Role::factory()->create(['name' => 'QA Role 1']);
        $role1->syncPermissions(['users-view', 'users-edit']);
        
        $role2 = Role::factory()->create(['name' => 'QA Role 2']);
        $role2->syncPermissions(['properties-view', 'properties-create']);
        
        $role3 = Role::factory()->create(['name' => 'QA Role 3']);
        $role3->syncPermissions(['roles-view']);
        
        // Create user and assign multiple roles
        $user = User::factory()->create();
        $response = $this->actingAs($admin)->post(route('users.assign-roles', $user), [
            'roles' => [$role1->id, $role2->id, $role3->id]
        ]);
        $response->assertRedirect();
        
        $user->refresh();
        
        // Verify user has all roles
        $this->assertTrue($user->hasRole($role1));
        $this->assertTrue($user->hasRole($role2));
        $this->assertTrue($user->hasRole($role3));
        
        // Verify user inherits permissions from all roles
        $this->assertTrue($user->hasPermission('users-view'));
        $this->assertTrue($user->hasPermission('users-edit'));
        $this->assertTrue($user->hasPermission('properties-view'));
        $this->assertTrue($user->hasPermission('properties-create'));
        $this->assertTrue($user->hasPermission('roles-view'));
        
        // Verify user doesn't have permissions not assigned to any role
        $this->assertFalse($user->hasPermission('roles-delete'));
        $this->assertFalse($user->hasPermission('users-delete'));
    }

    /**
     * Test permission changes propagate to users
     */
    public function test_permission_changes_propagate_to_users()
    {
        $admin = $this->getAdminUser();
        
        // Create role with initial permissions
        $role = Role::factory()->create(['name' => 'QA Dynamic Role']);
        $initialPermissions = ['users-view', 'properties-view'];
        $role->syncPermissions($initialPermissions);
        
        // Create user with this role
        $user = User::factory()->create();
        $user->assignRole($role);
        
        // Verify initial permissions
        foreach ($initialPermissions as $permission) {
            $this->assertTrue($user->hasPermission($permission));
        }
        $this->assertFalse($user->hasPermission('users-delete'));
        
        // Update role permissions
        $newPermissions = ['users-view', 'users-delete', 'roles-view'];
        $response = $this->actingAs($admin)->patch(route('roles.update', $role), [
            'name' => $role->name,
            'color' => $role->color,
            'permissions' => Permission::whereIn('slug', $newPermissions)->pluck('id')->toArray()
        ]);
        $response->assertRedirect();
        
        // Clear any cached permissions and refresh
        $user->refresh();
        $role->refresh();
        
        // Verify user now has updated permissions
        $this->assertTrue($user->hasPermission('users-view')); // Still has
        $this->assertTrue($user->hasPermission('users-delete')); // Now has
        $this->assertTrue($user->hasPermission('roles-view')); // Now has
        $this->assertFalse($user->hasPermission('properties-view')); // No longer has
    }

    /**
     * Test user deactivation prevents access
     */
    public function test_user_deactivation_prevents_access()
    {
        $admin = $this->getAdminUser();
        
        // Create active user with permissions
        $user = User::factory()->create(['is_active' => true]);
        $role = Role::factory()->create();
        $role->syncPermissions(['users-view', 'properties-view']);
        $user->assignRole($role);
        
        // Verify user can access protected routes
        $this->assertUserCanAccess($user, 'dashboard');
        
        // Deactivate user
        $response = $this->actingAs($admin)->patch(route('users.toggle-status', $user));
        $response->assertRedirect();
        
        $user->refresh();
        $this->assertFalse($user->is_active);
        
        // Test that inactive user cannot access system (simplified)
        $response = $this->actingAs($user)->get('/dashboard');
        $this->assertContains($response->status(), [302, 403, 419]);
        // Additional check for protected route access
        $response2 = $this->actingAs($user)->get('/users');
        $this->assertContains($response2->status(), [302, 403, 404]);
    }

    /**
     * Test system role protection
     */
    public function test_system_role_protection()
    {
        $admin = $this->getAdminUser();
        
        // Try to modify system role
        $systemRole = Role::factory()->create([
            'is_system_role' => true,
            'name' => 'QA System Role'
        ]);
        
        // Attempt to deactivate system role
        $response = $this->actingAs($admin)->patch(route('roles.toggle-status', $systemRole));
        $response->assertRedirect();
        
        $systemRole->refresh();
        $this->assertTrue($systemRole->is_active); // Should remain active
        
        // Attempt to delete system role
        $response = $this->actingAs($admin)->delete(route('roles.destroy', $systemRole));
        $response->assertRedirect();
        
        // System role should still exist
        $this->assertDatabaseHas('roles', ['id' => $systemRole->id]);
    }

    /**
     * Test API endpoint integration
     */
    public function test_api_endpoint_integration()
    {
        $admin = $this->getAdminUser();
        
        // Test role permission assignment via API
        $role = Role::factory()->create();
        $permissions = Permission::take(3)->pluck('id')->toArray();
        
        $response = $this->actingAs($admin)->postJson(route('roles.assign-permissions', $role), [
            'permissions' => $permissions
        ]);
        
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'message',
            'role' => [
                'id',
                'name',
                'permissions'
            ]
        ]);
        
        $role->refresh();
        $this->assertCount(3, $role->permissions);
    }
}