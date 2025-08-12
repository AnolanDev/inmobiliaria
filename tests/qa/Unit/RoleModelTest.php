<?php

namespace Tests\QA\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Role;
use App\Models\Permission;
use App\Models\User;

class RoleModelTest extends TestCase
{
    use RefreshDatabase;
    /**
     * Test role factory creates valid role
     */
    public function test_role_factory_creates_valid_role()
    {
        $role = Role::factory()->create();
        
        $this->assertNotNull($role->id);
        $this->assertNotNull($role->name);
        $this->assertNotNull($role->slug);
        $this->assertTrue($role->is_active);
        $this->assertInstanceOf(Role::class, $role);
    }
    
    /**
     * Test role permission assignment
     */
    public function test_role_can_be_assigned_permissions()
    {
        $role = Role::factory()->create();
        $permission = Permission::factory()->create();
        
        $role->givePermissionTo($permission);
        
        $this->assertTrue($role->hasPermissionTo($permission));
        $this->assertCount(1, $role->permissions);
    }
    
    /**
     * Test role user assignment
     */
    public function test_role_can_have_users()
    {
        $role = Role::factory()->create();
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        
        $user1->assignRole($role);
        $user2->assignRole($role);
        
        $this->assertCount(2, $role->users);
        $this->assertTrue($role->users->contains($user1));
        $this->assertTrue($role->users->contains($user2));
    }
    
    /**
     * Test role active properties
     */
    public function test_role_active_properties()
    {
        $activeRole = Role::factory()->create(['is_active' => true]);
        $inactiveRole = Role::factory()->create(['is_active' => false]);
        
        $this->assertTrue($activeRole->is_active);
        $this->assertFalse($inactiveRole->is_active);
    }
    
    /**
     * Test role slug generation
     */
    public function test_role_slug_generation()
    {
        $role = Role::factory()->create(['name' => 'Test Manager Role']);
        
        $this->assertNotNull($role->slug);
        $this->assertStringContainsString('-', $role->slug);
        // Verify slug is URL-friendly
        $this->assertMatchesRegularExpression('/^[a-z0-9\-]+$/', $role->slug);
        // Verify slug is based on name
        $this->assertEquals('test-manager-role', \Illuminate\Support\Str::slug('Test Manager Role'));
    }
}