<?php

namespace Tests\QA\API;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Role;

class UserApiTest extends TestCase
{
    use RefreshDatabase;

    private function getAdminUser(): User
    {
        return User::where('email', 'admin@inmobiliaria.com')->first() 
               ?? User::factory()->create();
    }

    /**
     * Test API user listing
     */
    public function test_api_user_listing()
    {
        $admin = $this->getAdminUser();
        
        $response = $this->actingAs($admin)
            ->getJson('/api/users');
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'email',
                        'is_active'
                    ]
                ]
            ]);
    }
    
    /**
     * Test API user creation
     */
    public function test_api_user_creation()
    {
        $admin = $this->getAdminUser();
        $userData = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'TestPassword123!',
            'password_confirmation' => 'TestPassword123!',
            'phone' => '+57 300 123 4567',
            'position' => 'Test Position',
            'is_active' => true
        ];
        
        $response = $this->actingAs($admin)
            ->postJson('/api/users', $userData);
        
        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'email',
                    'is_active'
                ]
            ]);
        
        $this->assertDatabaseHas('users', [
            'email' => $userData['email']
        ]);
    }
    
    /**
     * Test API unauthorized access
     */
    public function test_api_unauthorized_access()
    {
        $response = $this->getJson('/api/users');
        
        $response->assertStatus(401);
    }
    
    /**
     * Test API permission check
     */
    public function test_api_permission_check()
    {
        $limitedUser = User::factory()->create();
        
        $response = $this->actingAs($limitedUser)
            ->getJson('/api/users');
        
        $response->assertStatus(403);
    }
}