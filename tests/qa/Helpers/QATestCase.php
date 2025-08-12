<?php

namespace Tests\QA\Helpers;

use Tests\TestCase;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Laravel\Dusk\Browser;

abstract class QATestCase extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(\Tests\QA\Data\QATestDataSeeder::class);
    }

    /**
     * Get QA test users by role
     */
    protected function getQAUser(string $role): User
    {
        return match($role) {
            'super-admin' => User::where('email', 'qa-super@test.com')->first(),
            'manager' => User::where('email', 'qa-manager@test.com')->first(),
            'agent' => User::where('email', 'qa-agent@test.com')->first(),
            'limited' => User::where('email', 'qa-limited@test.com')->first(),
            'inactive' => User::where('email', 'qa-inactive@test.com')->first(),
            default => throw new \InvalidArgumentException("Unknown QA user role: {$role}")
        };
    }

    /**
     * Create a test user with specific permissions
     */
    protected function createUserWithPermissions(array $permissions): User
    {
        $user = User::factory()->create([
            'password' => Hash::make('TestPassword123!')
        ]);

        $role = Role::create([
            'name' => 'Test Role ' . $this->faker->word,
            'slug' => 'test-role-' . $this->faker->slug,
            'color' => $this->faker->hexColor,
            'is_active' => true
        ]);

        $role->syncPermissions($permissions);
        $user->assignRole($role);

        return $user;
    }

    /**
     * Assert user can access route
     */
    protected function assertUserCanAccess(User $user, string $route, array $params = []): void
    {
        $response = $this->actingAs($user)->get(route($route, $params));
        $response->assertStatus(200);
    }

    /**
     * Assert user cannot access route
     */
    protected function assertUserCannotAccess(User $user, string $route, array $params = []): void
    {
        $response = $this->actingAs($user)->get(route($route, $params));
        $response->assertStatus(403);
    }

    /**
     * Assert form validation errors
     */
    protected function assertFormValidationErrors(User $user, string $route, array $data, array $expectedErrors): void
    {
        $response = $this->actingAs($user)->post(route($route), $data);
        $response->assertSessionHasErrors($expectedErrors);
    }

    /**
     * Generate test form data
     */
    protected function generateUserFormData(array $overrides = []): array
    {
        return array_merge([
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => 'TestPassword123!',
            'password_confirmation' => 'TestPassword123!',
            'phone' => $this->faker->phoneNumber,
            'position' => $this->faker->jobTitle,
            'bio' => $this->faker->sentence,
            'is_active' => true,
            'force_password_change' => false,
            'roles' => [Role::first()->id ?? 1]
        ], $overrides);
    }

    /**
     * Generate role form data
     */
    protected function generateRoleFormData(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Test Role ' . $this->faker->word,
            'description' => $this->faker->sentence,
            'color' => $this->faker->hexColor,
            'sort_order' => $this->faker->numberBetween(1, 100),
            'is_active' => true,
            'permissions' => Permission::take(3)->pluck('id')->toArray()
        ], $overrides);
    }

    /**
     * Measure execution time
     */
    protected function measureExecutionTime(callable $callback): array
    {
        $startTime = microtime(true);
        $startMemory = memory_get_usage();
        
        $result = $callback();
        
        $endTime = microtime(true);
        $endMemory = memory_get_usage();
        
        return [
            'result' => $result,
            'execution_time' => round(($endTime - $startTime) * 1000, 2), // ms
            'memory_used' => $endMemory - $startMemory, // bytes
            'peak_memory' => memory_get_peak_usage()
        ];
    }

    /**
     * Assert performance benchmarks
     */
    protected function assertPerformance(array $metrics, int $maxExecutionTime = 1000, int $maxMemoryUsage = 50 * 1024 * 1024): void
    {
        $this->assertLessThan(
            $maxExecutionTime, 
            $metrics['execution_time'], 
            "Execution time {$metrics['execution_time']}ms exceeds limit of {$maxExecutionTime}ms"
        );
        
        $this->assertLessThan(
            $maxMemoryUsage, 
            $metrics['memory_used'], 
            "Memory usage {$metrics['memory_used']} bytes exceeds limit of {$maxMemoryUsage} bytes"
        );
    }

    /**
     * Create database stress test data
     */
    protected function createStressTestData(int $userCount = 1000, int $roleCount = 50): void
    {
        // Create roles
        Role::factory($roleCount)->create();
        
        // Create users
        User::factory($userCount)->create();
        
        // Assign random roles to users
        $roles = Role::all();
        User::all()->each(function ($user) use ($roles) {
            $user->assignRole($roles->random());
        });
    }

    /**
     * Test security headers
     */
    protected function assertSecurityHeaders($response): void
    {
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('X-Frame-Options', 'DENY');
        $response->assertHeader('X-XSS-Protection', '1; mode=block');
    }

    /**
     * Generate malicious input for security testing
     */
    protected function getMaliciousInputs(): array
    {
        return [
            'sql_injection' => "'; DROP TABLE users; --",
            'xss_script' => '<script>alert("XSS")</script>',
            'xss_img' => '<img src="x" onerror="alert(1)">',
            'html_injection' => '<iframe src="javascript:alert(1)"></iframe>',
            'null_byte' => "test\0.php",
            'path_traversal' => '../../../etc/passwd',
            'command_injection' => '; cat /etc/passwd',
            'ldap_injection' => '*)(uid=*',
            'xml_injection' => '<?xml version="1.0"?><!DOCTYPE root [<!ENTITY test SYSTEM "file:///etc/passwd">]><root>&test;</root>',
            'extremely_long_string' => str_repeat('A', 10000),
        ];
    }
}