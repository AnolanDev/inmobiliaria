<?php

namespace Tests\QA\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;

class UserModelTest extends TestCase
{
    use RefreshDatabase;
    /**
     * Test user factory creates valid user
     */
    public function test_user_factory_creates_valid_user()
    {
        $user = User::factory()->create();
        
        $this->assertNotNull($user->id);
        $this->assertNotNull($user->name);
        $this->assertNotNull($user->email);
        $this->assertNotNull($user->password);
        $this->assertEquals(1, $user->is_active);
        $this->assertInstanceOf(User::class, $user);
    }
    
    /**
     * Test user role assignment
     */
    public function test_user_can_be_assigned_roles()
    {
        $user = User::factory()->create();
        $role = Role::factory()->create(['name' => 'Test Role']);
        
        $user->assignRole($role);
        
        // Refresh the user to load the relationship
        $user->refresh();
        
        $this->assertTrue($user->hasRole($role));
        $this->assertTrue($user->hasRole($role->slug));
        $this->assertCount(1, $user->roles);
    }
    
    /**
     * Test user permission checking
     */
    public function test_user_permission_checking()
    {
        $user = User::factory()->create();
        $role = Role::factory()->create();
        $permission = Permission::factory()->create(['slug' => 'test-permission']);
        
        $role->givePermissionTo($permission);
        $user->assignRole($role);
        
        $this->assertTrue($user->hasPermission('test-permission'));
        $this->assertTrue($user->hasPermission($permission));
    }
    
    /**
     * Test user without permissions
     */
    public function test_user_without_permissions_returns_false()
    {
        $user = User::factory()->create();
        
        $this->assertFalse($user->hasPermission('nonexistent-permission'));
        $this->assertFalse($user->hasRole('nonexistent-role'));
    }
    
    /**
     * Test inactive user status
     */
    public function test_user_can_be_deactivated()
    {
        $user = User::factory()->create(['is_active' => true]);
        
        $this->assertTrue($user->is_active);
        
        $user->update(['is_active' => false]);
        
        $this->assertFalse($user->is_active);
    }
}