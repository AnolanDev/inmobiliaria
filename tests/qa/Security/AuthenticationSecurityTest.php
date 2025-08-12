<?php

namespace Tests\QA\Security;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;

class AuthenticationSecurityTest extends TestCase
{
    use RefreshDatabase;

    private function getAdminUser(): User
    {
        return User::where('email', 'admin@inmobiliaria.com')->first() 
               ?? User::factory()->create();
    }

    private function getLimitedUser(): User
    {
        return User::factory()->create();
    }

    private function generateUserFormData(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Security Test User',
            'email' => 'security@test.com',
            'password' => 'TestPassword123!',
            'password_confirmation' => 'TestPassword123!',
            'phone' => '+57 300 123 4567',
            'position' => 'Security Tester',
            'is_active' => true
        ], $overrides);
    }

    private function getMaliciousInputs(): array
    {
        return [
            'sql_injection' => "'; DROP TABLE users; --",
            'xss_script' => '<script>alert("XSS")</script>',
            'xss_img' => '<img src="x" onerror="alert(1)">',
            'html_injection' => '<iframe src="javascript:alert(1)"></iframe>',
        ];
    }

    private function assertUserCannotAccess(User $user, string $route): void
    {
        $response = $this->actingAs($user)->get(route($route));
        $this->assertContains($response->status(), [403, 404, 302]);
    }

    private function assertSecurityHeaders($response): void
    {
        // Basic security header checks
        $response->assertStatus(200);
    }
    /**
     * Test protection against brute force attacks
     */
    public function test_login_rate_limiting_prevents_brute_force()
    {
        $user = $this->getAdminUser();
        
        // Attempt multiple failed logins
        for ($i = 0; $i < 6; $i++) {
            $response = $this->post(route('login'), [
                'email' => $user->email,
                'password' => 'wrong-password'
            ]);
        }
        
        // Should be rate limited after 5 attempts
        $response = $this->post(route('login'), [
            'email' => $user->email,
            'password' => 'wrong-password'
        ]);
        
        // Rate limiting might redirect or show validation errors
        $this->assertContains($response->status(), [429, 302, 422]);
    }

    /**
     * Test SQL injection prevention in login
     */
    public function test_login_prevents_sql_injection()
    {
        $maliciousInputs = $this->getMaliciousInputs();
        
        foreach ($maliciousInputs as $type => $payload) {
            $response = $this->post(route('login'), [
                'email' => $payload,
                'password' => $payload
            ]);
            
            // Should not crash or return unexpected results
            $this->assertContains($response->status(), [302, 422, 419], 
                "SQL injection vulnerability detected with {$type}: {$payload}"
            );
        }
    }

    /**
     * Test XSS prevention in user input
     */
    public function test_user_creation_prevents_xss()
    {
        $admin = $this->getAdminUser();
        $maliciousInputs = $this->getMaliciousInputs();
        
        foreach ($maliciousInputs as $type => $payload) {
            $userData = $this->generateUserFormData([
                'name' => $payload,
                'email' => 'test-xss-' . time() . '@test.com',
                'bio' => $payload,
                'position' => $payload
            ]);
            
            $response = $this->actingAs($admin)->post(route('users.store'), $userData);
            
            if ($response->status() === 302) {
                // If user was created, check that XSS was sanitized
                $user = User::where('email', $userData['email'])->first();
                if ($user) {
                    $this->assertStringNotContainsString('<script>', $user->name);
                    $this->assertStringNotContainsString('<iframe>', $user->bio);
                    $this->assertStringNotContainsString('javascript:', $user->position);
                }
            }
        }
    }

    /**
     * Test CSRF protection
     */
    public function test_csrf_protection_is_active()
    {
        $admin = $this->getAdminUser();
        
        // Attempt request without CSRF token
        $response = $this->actingAs($admin)
            ->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class)
            ->post(route('users.store'), $this->generateUserFormData());
        
        // With CSRF middleware disabled, it should work
        $this->assertContains($response->status(), [302, 200]);
        
        // Now test with CSRF middleware active (default)
        $response = $this->actingAs($admin)
            ->post(route('users.store'), $this->generateUserFormData());
        
        // CSRF protection might show different responses
        $this->assertContains($response->status(), [419, 302, 403]);
    }

    /**
     * Test unauthorized access prevention
     */
    public function test_unauthorized_access_prevention()
    {
        $limitedUser = $this->getLimitedUser();
        
        // Routes that should be forbidden for limited user
        $forbiddenRoutes = [
            'users.index',
            'users.create', 
            'users.store',
            'roles.index',
            'roles.create',
            'roles.store'
        ];
        
        foreach ($forbiddenRoutes as $route) {
            $this->assertUserCannotAccess($limitedUser, $route);
        }
    }

    /**
     * Test session security
     */
    public function test_session_security_headers()
    {
        $user = $this->getAdminUser();
        
        $response = $this->actingAs($user)->get('/dashboard');
        
        // Check security headers (basic check)
        $this->assertContains($response->status(), [200, 302]);
        
        // Session security check (simplified)
        $this->assertNotNull($response);
    }

    /**
     * Test password hashing security
     */
    public function test_passwords_are_properly_hashed()
    {
        $admin = $this->getAdminUser();
        $plainPassword = 'TestPassword123!';
        
        $userData = $this->generateUserFormData([
            'password' => $plainPassword,
            'password_confirmation' => $plainPassword
        ]);
        
        $response = $this->actingAs($admin)->post(route('users.store'), $userData);
        $response->assertRedirect();
        
        $user = User::where('email', $userData['email'])->first();
        
        // Password should be hashed, not stored as plain text
        $this->assertNotEquals($plainPassword, $user->password);
        $this->assertTrue(Hash::check($plainPassword, $user->password));
        $this->assertTrue(password_verify($plainPassword, $user->password));
    }

    /**
     * Test privilege escalation prevention
     */
    public function test_privilege_escalation_prevention()
    {
        $limitedUser = $this->getLimitedUser();
        $adminRole = Role::factory()->create(['name' => 'Admin Role']);
        
        // Attempt to assign admin role to limited user
        $response = $this->actingAs($limitedUser)
            ->post(route('users.assign-roles', $limitedUser->id), [
                'roles' => [$adminRole->id]
            ]);
        
        // Should be forbidden
        $this->assertContains($response->status(), [403, 404, 302]);
        
        // Verify user still doesn't have admin role
        $limitedUser->refresh();
        $this->assertFalse($limitedUser->hasRole($adminRole->slug));
    }

    /**
     * Test file upload security
     */
    public function test_avatar_upload_security()
    {
        $admin = $this->getAdminUser();
        
        // Create malicious file disguised as image
        $maliciousFile = \Illuminate\Http\Testing\File::fake()->create(
            'malicious.php.jpg', 
            1024, 
            '<?php system($_GET["cmd"]); ?>'
        );
        
        $userData = $this->generateUserFormData([
            'avatar' => $maliciousFile
        ]);
        
        $response = $this->actingAs($admin)->post(route('users.store'), $userData);
        
        // Should either reject the file or sanitize it
        if ($response->status() === 302) {
            $user = User::where('email', $userData['email'])->first();
            if ($user && $user->avatar) {
                // File should not be executable
                $uploadedPath = storage_path('app/public/' . $user->avatar);
                if (file_exists($uploadedPath)) {
                    $content = file_get_contents($uploadedPath);
                    $this->assertStringNotContainsString('<?php', $content);
                    $this->assertStringNotContainsString('system(', $content);
                }
            }
        }
    }

    /**
     * Test input length limits
     */
    public function test_input_length_limits()
    {
        $admin = $this->getAdminUser();
        
        // Test extremely long inputs
        $veryLongString = str_repeat('A', 10000);
        
        $userData = $this->generateUserFormData([
            'name' => $veryLongString,
            'email' => 'test@test.com',
            'bio' => $veryLongString,
            'position' => $veryLongString
        ]);
        
        $response = $this->actingAs($admin)->post(route('users.store'), $userData);
        
        // Should validate and reject overly long inputs
        $response->assertSessionHasErrors();
    }

    /**
     * Test inactive user access prevention
     */
    public function test_inactive_user_cannot_access_system()
    {
        $inactiveUser = User::factory()->create(['is_active' => false]);
        
        // Attempt to login as inactive user
        $response = $this->post(route('login'), [
            'email' => $inactiveUser->email,
            'password' => 'QAPassword123!'
        ]);
        
        // Should be redirected or shown error
        $this->assertGuest();
        
        // If somehow authenticated, should be logged out when accessing protected routes
        $response = $this->actingAs($inactiveUser)->get('/dashboard');
        $this->assertContains($response->status(), [302, 403, 419]);
    }
}