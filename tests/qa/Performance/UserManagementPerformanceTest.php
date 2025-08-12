<?php

namespace Tests\QA\Performance;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\DB;

class UserManagementPerformanceTest extends TestCase
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
            'name' => 'Performance Test User',
            'email' => 'perf' . time() . '@test.com',
            'password' => 'TestPassword123!',
            'password_confirmation' => 'TestPassword123!',
            'phone' => '+57 300 123 4567',
            'position' => 'Performance Tester',
            'is_active' => true
        ], $overrides);
    }

    private function measureExecutionTime(callable $callback): array
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

    private function assertPerformance(array $metrics, int $maxExecutionTime = 1000, int $maxMemoryUsage = 50 * 1024 * 1024): void
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

    private function createStressTestData(int $userCount = 100, int $roleCount = 5): void
    {
        // Create roles
        Role::factory($roleCount)->create();
        
        // Create users
        User::factory($userCount)->create();
        
        // Assign random roles to users (simplified)
        $roles = Role::all();
        User::all()->take(50)->each(function ($user) use ($roles) {
            if ($roles->count() > 0) {
                $user->assignRole($roles->random());
            }
        });
    }
    /**
     * Test user index page load time with large dataset
     */
    public function test_user_index_performance_with_large_dataset()
    {
        // Create stress test data
        $this->createStressTestData(1000, 20);
        
        $admin = $this->getAdminUser();
        
        $metrics = $this->measureExecutionTime(function() use ($admin) {
            return $this->actingAs($admin)->get(route('users.index'));
        });
        
        // Assert performance benchmarks
        $this->assertPerformance($metrics, 2000, 100 * 1024 * 1024); // 2s, 100MB
        $metrics['result']->assertStatus(200);
        
        echo "\n📊 User Index Performance Metrics:\n";
        echo "⏱️  Execution Time: {$metrics['execution_time']}ms\n";
        echo "💾 Memory Used: " . round($metrics['memory_used'] / 1024 / 1024, 2) . "MB\n";
        echo "🔝 Peak Memory: " . round($metrics['peak_memory'] / 1024 / 1024, 2) . "MB\n";
    }

    /**
     * Test user search performance
     */
    public function test_user_search_performance()
    {
        $this->createStressTestData(2000, 10);
        $admin = $this->getAdminUser();
        
        $searchTerms = ['John', 'Manager', '@gmail', 'Agent', 'Test'];
        
        foreach ($searchTerms as $term) {
            $metrics = $this->measureExecutionTime(function() use ($admin, $term) {
                return $this->actingAs($admin)->get(route('users.index', ['search' => $term]));
            });
            
            // Search should be fast even with large dataset
            $this->assertPerformance($metrics, 1500, 50 * 1024 * 1024); // 1.5s, 50MB
            $metrics['result']->assertStatus(200);
            
            echo "\n🔍 Search Performance for '{$term}':\n";
            echo "⏱️  Time: {$metrics['execution_time']}ms\n";
            echo "💾 Memory: " . round($metrics['memory_used'] / 1024 / 1024, 2) . "MB\n";
        }
    }

    /**
     * Test user creation performance
     */
    public function test_user_creation_performance()
    {
        $admin = $this->getAdminUser();
        $userData = $this->generateUserFormData();
        
        $metrics = $this->measureExecutionTime(function() use ($admin, $userData) {
            return $this->actingAs($admin)->post(route('users.store'), $userData);
        });
        
        // User creation should be fast
        $this->assertPerformance($metrics, 1000, 20 * 1024 * 1024); // 1s, 20MB
        $metrics['result']->assertRedirect();
        
        echo "\n➕ User Creation Performance:\n";
        echo "⏱️  Time: {$metrics['execution_time']}ms\n";
        echo "💾 Memory: " . round($metrics['memory_used'] / 1024 / 1024, 2) . "MB\n";
    }

    /**
     * Test bulk user operations performance
     */
    public function test_bulk_user_operations_performance()
    {
        $admin = $this->getAdminUser();
        
        // Create 50 users and measure time
        $metrics = $this->measureExecutionTime(function() use ($admin) {
            $users = [];
            for ($i = 0; $i < 50; $i++) {
                $userData = $this->generateUserFormData([
                    'email' => "bulk-test-{$i}@test.com"
                ]);
                
                $response = $this->actingAs($admin)->post(route('users.store'), $userData);
                $users[] = $response;
            }
            return $users;
        });
        
        // Bulk operations should complete within reasonable time
        $this->assertPerformance($metrics, 30000, 200 * 1024 * 1024); // 30s, 200MB
        
        echo "\n📦 Bulk Operations Performance (50 users):\n";
        echo "⏱️  Total Time: {$metrics['execution_time']}ms\n";
        echo "⏱️  Average per User: " . round($metrics['execution_time'] / 50, 2) . "ms\n";
        echo "💾 Total Memory: " . round($metrics['memory_used'] / 1024 / 1024, 2) . "MB\n";
    }

    /**
     * Test database query efficiency
     */
    public function test_database_query_efficiency()
    {
        $this->createStressTestData(500, 10);
        $admin = $this->getAdminUser();
        
        // Enable query logging
        DB::enableQueryLog();
        
        $metrics = $this->measureExecutionTime(function() use ($admin) {
            return $this->actingAs($admin)->get(route('users.index'));
        });
        
        $queries = DB::getQueryLog();
        DB::disableQueryLog();
        
        // Should not have N+1 query problems
        $this->assertLessThan(10, count($queries), 
            'Too many database queries detected. Possible N+1 problem.');
        
        // Check for slow queries (over 100ms)
        $slowQueries = array_filter($queries, function($query) {
            return $query['time'] > 100;
        });
        
        $this->assertEmpty($slowQueries, 
            'Slow database queries detected: ' . json_encode($slowQueries));
        
        echo "\n🗄️  Database Query Performance:\n";
        echo "📊 Total Queries: " . count($queries) . "\n";
        echo "⏱️  Average Query Time: " . round(array_sum(array_column($queries, 'time')) / count($queries), 2) . "ms\n";
        echo "🐌 Slow Queries: " . count($slowQueries) . "\n";
    }

    /**
     * Test role assignment performance
     */
    public function test_role_assignment_performance()
    {
        $admin = $this->getAdminUser();
        $user = User::factory()->create();
        $roles = Role::take(5)->pluck('id')->toArray();
        
        $metrics = $this->measureExecutionTime(function() use ($admin, $user, $roles) {
            return $this->actingAs($admin)->post(route('users.assign-roles', $user), [
                'roles' => $roles
            ]);
        });
        
        // Role assignment should be fast
        $this->assertPerformance($metrics, 500, 10 * 1024 * 1024); // 500ms, 10MB
        
        echo "\n🔐 Role Assignment Performance:\n";
        echo "⏱️  Time: {$metrics['execution_time']}ms\n";
        echo "💾 Memory: " . round($metrics['memory_used'] / 1024 / 1024, 2) . "MB\n";
    }

    /**
     * Test permission checking performance
     */
    public function test_permission_checking_performance()
    {
        $user = $this->getAdminUser();
        
        $metrics = $this->measureExecutionTime(function() use ($user) {
            $results = [];
            // Check multiple permissions rapidly
            for ($i = 0; $i < 100; $i++) {
                $results[] = $user->hasPermission('users-view');
                $results[] = $user->hasPermission('users-edit');
                $results[] = $user->hasPermission('properties-view');
                $results[] = $user->hasPermission('roles-view');
            }
            return $results;
        });
        
        // Permission checking should be very fast
        $this->assertPerformance($metrics, 100, 5 * 1024 * 1024); // 100ms, 5MB
        
        echo "\n🔍 Permission Checking Performance (400 checks):\n";
        echo "⏱️  Total Time: {$metrics['execution_time']}ms\n";
        echo "⏱️  Average per Check: " . round($metrics['execution_time'] / 400, 4) . "ms\n";
        echo "💾 Memory: " . round($metrics['memory_used'] / 1024 / 1024, 2) . "MB\n";
    }

    /**
     * Test memory usage during large operations
     */
    public function test_memory_usage_large_operations()
    {
        $this->createStressTestData(1500, 25);
        
        $initialMemory = memory_get_usage();
        
        // Load large dataset
        $users = User::with(['roles.permissions'])->get();
        
        $memoryAfterLoad = memory_get_usage();
        $memoryIncrease = $memoryAfterLoad - $initialMemory;
        
        // Memory usage should be reasonable
        $this->assertLessThan(150 * 1024 * 1024, $memoryIncrease, 
            'Excessive memory usage detected: ' . round($memoryIncrease / 1024 / 1024, 2) . 'MB');
        
        echo "\n💾 Memory Usage Analysis:\n";
        echo "🔄 Initial Memory: " . round($initialMemory / 1024 / 1024, 2) . "MB\n";
        echo "📈 After Loading {$users->count()} Users: " . round($memoryAfterLoad / 1024 / 1024, 2) . "MB\n";
        echo "📊 Memory Increase: " . round($memoryIncrease / 1024 / 1024, 2) . "MB\n";
        echo "🔝 Peak Memory: " . round(memory_get_peak_usage() / 1024 / 1024, 2) . "MB\n";
    }

    /**
     * Test concurrent user operations simulation
     */
    public function test_concurrent_operations_simulation()
    {
        $admin = $this->getAdminUser();
        $startTime = microtime(true);
        
        // Simulate concurrent operations
        $operations = [];
        for ($i = 0; $i < 20; $i++) {
            $operations[] = function() use ($admin, $i) {
                return $this->actingAs($admin)->get(route('users.index', ['page' => $i % 5 + 1]));
            };
        }
        
        // Execute operations
        $results = [];
        foreach ($operations as $operation) {
            $results[] = $operation();
        }
        
        $totalTime = (microtime(true) - $startTime) * 1000;
        
        // All operations should complete successfully
        foreach ($results as $result) {
            $result->assertStatus(200);
        }
        
        // Should handle concurrent load reasonably
        $this->assertLessThan(10000, $totalTime, 
            'Concurrent operations took too long: ' . $totalTime . 'ms');
        
        echo "\n🔄 Concurrent Operations Performance (20 operations):\n";
        echo "⏱️  Total Time: " . round($totalTime, 2) . "ms\n";
        echo "⏱️  Average per Operation: " . round($totalTime / 20, 2) . "ms\n";
        echo "💾 Peak Memory: " . round(memory_get_peak_usage() / 1024 / 1024, 2) . "MB\n";
    }
}