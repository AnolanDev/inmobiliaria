<?php

namespace Tests\QA\Data;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Support\Facades\Hash;

class QATestDataSeeder extends Seeder
{
    /**
     * Seed test data specifically for QA testing
     */
    public function run(): void
    {
        $this->createTestPermissions();
        $this->createTestRoles();
        $this->createTestUsers();
        $this->createEdgeCaseData();
    }

    private function createTestPermissions(): void
    {
        $permissions = [
            // Users module
            ['name' => 'Ver usuarios', 'slug' => 'users-view', 'module' => 'users', 'action' => 'view'],
            ['name' => 'Crear usuarios', 'slug' => 'users-create', 'module' => 'users', 'action' => 'create'],
            ['name' => 'Editar usuarios', 'slug' => 'users-edit', 'module' => 'users', 'action' => 'edit'],
            ['name' => 'Eliminar usuarios', 'slug' => 'users-delete', 'module' => 'users', 'action' => 'delete'],
            
            // Roles module
            ['name' => 'Ver roles', 'slug' => 'roles-view', 'module' => 'roles', 'action' => 'view'],
            ['name' => 'Crear roles', 'slug' => 'roles-create', 'module' => 'roles', 'action' => 'create'],
            ['name' => 'Editar roles', 'slug' => 'roles-edit', 'module' => 'roles', 'action' => 'edit'],
            ['name' => 'Eliminar roles', 'slug' => 'roles-delete', 'module' => 'roles', 'action' => 'delete'],
            
            // Properties module
            ['name' => 'Ver propiedades', 'slug' => 'properties-view', 'module' => 'properties', 'action' => 'view'],
            ['name' => 'Crear propiedades', 'slug' => 'properties-create', 'module' => 'properties', 'action' => 'create'],
            ['name' => 'Editar propiedades', 'slug' => 'properties-edit', 'module' => 'properties', 'action' => 'edit'],
            ['name' => 'Eliminar propiedades', 'slug' => 'properties-delete', 'module' => 'properties', 'action' => 'delete'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['slug' => $permission['slug']], $permission);
        }
    }

    private function createTestRoles(): void
    {
        // Super Admin Role
        $superAdmin = Role::firstOrCreate(['slug' => 'qa-super-admin'], [
            'name' => 'QA Super Admin',
            'description' => 'Full access for QA testing',
            'color' => '#dc2626',
            'is_system_role' => false,
            'is_active' => true,
            'sort_order' => 1
        ]);
        $superAdmin->syncPermissions(Permission::all());

        // Manager Role
        $manager = Role::firstOrCreate(['slug' => 'qa-manager'], [
            'name' => 'QA Manager',
            'description' => 'Manager level access for testing',
            'color' => '#2563eb',
            'is_active' => true,
            'sort_order' => 2
        ]);
        $manager->syncPermissions(['users-view', 'users-edit', 'properties-view', 'properties-create', 'properties-edit']);

        // Agent Role
        $agent = Role::firstOrCreate(['slug' => 'qa-agent'], [
            'name' => 'QA Agent',
            'description' => 'Agent level access for testing',
            'color' => '#16a34a',
            'is_active' => true,
            'sort_order' => 3
        ]);
        $agent->syncPermissions(['properties-view', 'properties-create']);

        // Limited User Role
        $limited = Role::firstOrCreate(['slug' => 'qa-limited'], [
            'name' => 'QA Limited User',
            'description' => 'Limited access for testing restrictions',
            'color' => '#9333ea',
            'is_active' => true,
            'sort_order' => 4
        ]);
        $limited->syncPermissions(['properties-view']);

        // Inactive Role (for testing)
        Role::firstOrCreate(['slug' => 'qa-inactive'], [
            'name' => 'QA Inactive Role',
            'description' => 'Inactive role for testing',
            'color' => '#6b7280',
            'is_active' => false,
            'sort_order' => 5
        ]);
    }

    private function createTestUsers(): void
    {
        // QA Super Admin User
        $superAdmin = User::firstOrCreate(['email' => 'qa-super@test.com'], [
            'name' => 'QA Super Administrator',
            'password' => Hash::make('QAPassword123!'),
            'phone' => '+57 300 000 0001',
            'position' => 'QA Super Admin',
            'bio' => 'QA test user with full system access',
            'is_active' => true,
            'force_password_change' => false,
            'invited_by' => null,
            'invited_at' => now(),
            'password_changed_at' => now(),
        ]);
        $superAdmin->assignRole('qa-super-admin');

        // QA Manager User
        $manager = User::firstOrCreate(['email' => 'qa-manager@test.com'], [
            'name' => 'QA Manager User',
            'password' => Hash::make('QAPassword123!'),
            'phone' => '+57 300 000 0002',
            'position' => 'QA Manager',
            'bio' => 'QA test user with manager access',
            'is_active' => true,
            'force_password_change' => false,
            'invited_by' => $superAdmin->id,
            'invited_at' => now(),
            'password_changed_at' => now(),
        ]);
        $manager->assignRole('qa-manager');

        // QA Agent User
        $agent = User::firstOrCreate(['email' => 'qa-agent@test.com'], [
            'name' => 'QA Agent User',
            'password' => Hash::make('QAPassword123!'),
            'phone' => '+57 300 000 0003',
            'position' => 'QA Agent',
            'bio' => 'QA test user with agent access',
            'is_active' => true,
            'force_password_change' => false,
            'invited_by' => $manager->id,
            'invited_at' => now(),
            'password_changed_at' => now(),
        ]);
        $agent->assignRole('qa-agent');

        // QA Limited User
        $limited = User::firstOrCreate(['email' => 'qa-limited@test.com'], [
            'name' => 'QA Limited User',
            'password' => Hash::make('QAPassword123!'),
            'phone' => '+57 300 000 0004',
            'position' => 'QA Limited',
            'bio' => 'QA test user with limited access',
            'is_active' => true,
            'force_password_change' => false,
            'invited_by' => $manager->id,
            'invited_at' => now(),
            'password_changed_at' => now(),
        ]);
        $limited->assignRole('qa-limited');

        // QA Inactive User
        User::firstOrCreate(['email' => 'qa-inactive@test.com'], [
            'name' => 'QA Inactive User',
            'password' => Hash::make('QAPassword123!'),
            'phone' => '+57 300 000 0005',
            'position' => 'QA Inactive',
            'bio' => 'QA test user - inactive',
            'is_active' => false,
            'force_password_change' => true,
            'invited_by' => $manager->id,
            'invited_at' => now(),
            'password_changed_at' => now()->subMonths(6),
        ]);
    }

    private function createEdgeCaseData(): void
    {
        // User with very long name (edge case)
        User::firstOrCreate(['email' => 'qa-long-name@test.com'], [
            'name' => 'QA Usuario Con Un Nombre Extremadamente Largo Para Probar Los Límites Del Sistema Y Validaciones De Longitud',
            'password' => Hash::make('QAPassword123!'),
            'phone' => '+57 300 000 0099',
            'position' => 'QA Edge Case Tester',
            'is_active' => true,
        ]);

        // User with special characters
        User::firstOrCreate(['email' => 'qa-special@test.com'], [
            'name' => 'QA Spéciàl Ñoél Tëster',
            'password' => Hash::make('QAPassword123!'),
            'phone' => '+57 300 000 0098',
            'position' => 'QA Spéciàl Tëster',
            'bio' => 'Testing special characters: áéíóú ñ ü ç',
            'is_active' => true,
        ]);

        // User scheduled for password change
        User::firstOrCreate(['email' => 'qa-password-change@test.com'], [
            'name' => 'QA Password Change Required',
            'password' => Hash::make('TemporaryPassword123!'),
            'phone' => '+57 300 000 0097',
            'position' => 'QA Password Tester',
            'is_active' => true,
            'force_password_change' => true,
            'password_changed_at' => now()->subMonths(3),
        ]);
    }
}