<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class UserManagementBrowserTest extends DuskTestCase
{
    use DatabaseMigrations;

    protected function setUp(): void
    {
        parent::setUp();
        $this->createTestData();
    }

    public function test_admin_can_navigate_to_users_page()
    {
        $admin = User::where('email', 'admin@test.com')->first();

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visit('/dashboard')
                    ->waitForText('Dashboard')
                    ->click('@sidebar-users-link')
                    ->waitForText('Gestión de Usuarios')
                    ->assertSee('Usuarios')
                    ->assertSee('Nuevo Usuario');
        });
    }

    public function test_admin_can_create_user_through_ui()
    {
        $admin = User::where('email', 'admin@test.com')->first();

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visit('/users')
                    ->click('@create-user-button')
                    ->waitForText('Crear Usuario')
                    ->type('name', 'Test User UI')
                    ->type('email', 'testui@example.com')
                    ->type('password', 'password123')
                    ->type('password_confirmation', 'password123')
                    ->type('phone', '+57 300 123 4567')
                    ->type('position', 'QA Tester')
                    ->check('is_active')
                    ->click('@role-checkbox-1') // Check first role
                    ->click('@submit-button')
                    ->waitForText('Usuario creado exitosamente')
                    ->assertSee('Test User UI');
        });
    }

    public function test_user_search_functionality_works()
    {
        $admin = User::where('email', 'admin@test.com')->first();
        
        // Create test users
        User::factory()->create(['name' => 'John Searchable']);
        User::factory()->create(['name' => 'Jane Invisible']);

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visit('/users')
                    ->type('@search-input', 'John')
                    ->pause(500) // Wait for debounce
                    ->waitForText('John Searchable')
                    ->assertSee('John Searchable')
                    ->assertDontSee('Jane Invisible');
        });
    }

    public function test_user_edit_modal_works()
    {
        $admin = User::where('email', 'admin@test.com')->first();
        $testUser = User::factory()->create(['name' => 'Edit Me']);

        $this->browse(function (Browser $browser) use ($admin, $testUser) {
            $browser->loginAs($admin)
                    ->visit('/users')
                    ->click("@edit-user-{$testUser->id}")
                    ->waitForText('Editar Usuario')
                    ->clear('name')
                    ->type('name', 'Edited Name')
                    ->clear('position')
                    ->type('position', 'New Position')
                    ->click('@submit-button')
                    ->waitForText('Usuario actualizado exitosamente')
                    ->assertSee('Edited Name');
        });
    }

    public function test_user_can_toggle_status()
    {
        $admin = User::where('email', 'admin@test.com')->first();
        $testUser = User::factory()->create(['name' => 'Toggle Me', 'is_active' => true]);

        $this->browse(function (Browser $browser) use ($admin, $testUser) {
            $browser->loginAs($admin)
                    ->visit('/users')
                    ->click("@toggle-status-{$testUser->id}")
                    ->waitForText('Usuario desactivado exitosamente')
                    ->refresh()
                    ->assertSee('Inactivo');
        });
    }

    public function test_user_delete_confirmation_works()
    {
        $admin = User::where('email', 'admin@test.com')->first();
        $testUser = User::factory()->create(['name' => 'Delete Me']);

        $this->browse(function (Browser $browser) use ($admin, $testUser) {
            $browser->loginAs($admin)
                    ->visit('/users')
                    ->click("@delete-user-{$testUser->id}")
                    ->waitForText('¿Estás seguro de que deseas eliminar')
                    ->assertSee('Delete Me')
                    ->click('@confirm-delete-button')
                    ->waitForText('Usuario eliminado exitosamente')
                    ->assertDontSee('Delete Me');
        });
    }

    public function test_user_without_permissions_cannot_access_users()
    {
        $limitedUser = User::factory()->create();
        $basicRole = Role::factory()->create(['name' => 'Basic User']);
        $limitedUser->assignRole($basicRole);

        $this->browse(function (Browser $browser) use ($limitedUser) {
            $browser->loginAs($limitedUser)
                    ->visit('/users')
                    ->assertSee('403')
                    ->assertSee('Forbidden');
        });
    }

    public function test_responsive_navigation_works()
    {
        $admin = User::where('email', 'admin@test.com')->first();

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                    ->resize(375, 667) // Mobile size
                    ->visit('/dashboard')
                    ->click('@mobile-menu-button')
                    ->waitFor('@mobile-sidebar')
                    ->assertVisible('@mobile-sidebar')
                    ->click('@mobile-users-link')
                    ->waitForText('Gestión de Usuarios')
                    ->assertPathIs('/users');
        });
    }

    public function test_form_validation_displays_errors()
    {
        $admin = User::where('email', 'admin@test.com')->first();

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visit('/users/create')
                    ->click('@submit-button')
                    ->waitForText('The name field is required')
                    ->assertSee('The name field is required')
                    ->assertSee('The email field is required')
                    ->assertSee('The password field is required');
        });
    }

    public function test_avatar_upload_works()
    {
        $admin = User::where('email', 'admin@test.com')->first();

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->loginAs($admin)
                    ->visit('/users/create')
                    ->type('name', 'Avatar User')
                    ->type('email', 'avatar@test.com')
                    ->type('password', 'password123')
                    ->type('password_confirmation', 'password123')
                    ->attach('avatar', __DIR__.'/test-avatar.jpg')
                    ->check('is_active')
                    ->click('@submit-button')
                    ->waitForText('Usuario creado exitosamente')
                    ->assertSee('Avatar User');
        });
    }

    private function createTestData()
    {
        // Create permissions
        $permissions = [
            ['name' => 'Ver usuarios', 'slug' => 'users-view', 'module' => 'users'],
            ['name' => 'Crear usuarios', 'slug' => 'users-create', 'module' => 'users'],
            ['name' => 'Editar usuarios', 'slug' => 'users-edit', 'module' => 'users'],
            ['name' => 'Eliminar usuarios', 'slug' => 'users-delete', 'module' => 'users'],
        ];

        foreach ($permissions as $permission) {
            Permission::create($permission);
        }

        // Create admin role
        $adminRole = Role::create([
            'name' => 'Super Admin',
            'slug' => 'super-admin',
            'description' => 'Full system access',
            'color' => '#dc2626',
            'is_system_role' => true,
            'is_active' => true
        ]);

        $adminRole->syncPermissions(Permission::all());

        // Create admin user
        $admin = User::create([
            'name' => 'Test Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'is_active' => true
        ]);

        $admin->assignRole($adminRole);
    }
}