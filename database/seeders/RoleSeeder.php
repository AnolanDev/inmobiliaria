<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create default roles
        $superAdmin = Role::firstOrCreate(
            ['slug' => 'super-admin'],
            [
                'name' => 'Super Administrador',
                'description' => 'Acceso completo a todas las funcionalidades del sistema',
                'color' => '#dc2626', // red
                'sort_order' => 100,
                'metadata' => [
                    'is_system_role' => true,
                    'can_be_deleted' => false,
                    'auto_permissions' => true
                ]
            ]
        );

        $admin = Role::firstOrCreate(
            ['slug' => 'admin'],
            [
                'name' => 'Administrador',
                'description' => 'Administrador del sistema con acceso a la mayoría de funciones',
                'color' => '#ea580c', // orange
                'sort_order' => 90,
                'metadata' => [
                    'is_system_role' => true,
                    'can_be_deleted' => false
                ]
            ]
        );

        $manager = Role::firstOrCreate(
            ['slug' => 'manager'],
            [
                'name' => 'Gerente',
                'description' => 'Gerente con acceso a gestión de propiedades y reportes',
                'color' => '#d97706', // amber
                'sort_order' => 80,
            ]
        );

        $agent = Role::firstOrCreate(
            ['slug' => 'agent'],
            [
                'name' => 'Agente',
                'description' => 'Agente inmobiliario con acceso a propiedades y visitas',
                'color' => '#059669', // green
                'sort_order' => 70,
            ]
        );

        $assistant = Role::firstOrCreate(
            ['slug' => 'assistant'],
            [
                'name' => 'Asistente',
                'description' => 'Asistente con acceso limitado para apoyo operativo',
                'color' => '#0891b2', // cyan
                'sort_order' => 60,
            ]
        );

        $client = Role::firstOrCreate(
            ['slug' => 'client'],
            [
                'name' => 'Cliente',
                'description' => 'Cliente con acceso a sus propias visitas e información',
                'color' => '#7c3aed', // purple
                'sort_order' => 50,
            ]
        );

        // Assign permissions to roles
        $this->assignPermissionsToRoles();

        // Create default super admin user if none exists
        $this->createDefaultSuperAdmin($superAdmin);
    }

    private function assignPermissionsToRoles(): void
    {
        // Super Admin gets all permissions (handled automatically by HasRoles trait)
        $superAdmin = Role::where('slug', 'super-admin')->first();
        $allPermissions = Permission::all();
        $superAdmin->syncPermissions($allPermissions->pluck('id')->toArray());

        // Admin permissions (most permissions except system management)
        $admin = Role::where('slug', 'admin')->first();
        $adminPermissions = Permission::whereNotIn('slug', [
            'permissions-manage',
            'settings-manage'
        ])->get();
        $admin->syncPermissions($adminPermissions->pluck('id')->toArray());

        // Manager permissions
        $manager = Role::where('slug', 'manager')->first();
        $managerPermissions = Permission::whereIn('slug', [
            'dashboard-view',
            'properties-view', 'properties-create', 'properties-edit', 'properties-manage', 'properties-export',
            'projects-view', 'projects-create', 'projects-edit', 'projects-manage',
            'clients-view', 'clients-create', 'clients-edit', 'clients-manage', 'clients-export',
            'agents-view', 'agents-create', 'agents-edit', 'agents-manage',
            'visits-view', 'visits-create', 'visits-edit', 'visits-manage',
            'reports-view', 'reports-create', 'reports-export',
            'users-view', 'users-create', 'users-edit'
        ])->get();
        $manager->syncPermissions($managerPermissions->pluck('id')->toArray());

        // Agent permissions
        $agent = Role::where('slug', 'agent')->first();
        $agentPermissions = Permission::whereIn('slug', [
            'dashboard-view',
            'properties-view', 'properties-create', 'properties-edit',
            'projects-view',
            'clients-view', 'clients-create', 'clients-edit',
            'visits-view', 'visits-create', 'visits-edit', 'visits-manage',
            'reports-view'
        ])->get();
        $agent->syncPermissions($agentPermissions->pluck('id')->toArray());

        // Assistant permissions
        $assistant = Role::where('slug', 'assistant')->first();
        $assistantPermissions = Permission::whereIn('slug', [
            'dashboard-view',
            'properties-view',
            'projects-view',
            'clients-view', 'clients-create', 'clients-edit',
            'visits-view', 'visits-create', 'visits-edit'
        ])->get();
        $assistant->syncPermissions($assistantPermissions->pluck('id')->toArray());

        // Client permissions (very limited)
        $client = Role::where('slug', 'client')->first();
        $clientPermissions = Permission::whereIn('slug', [
            'visits-view'
        ])->get();
        $client->syncPermissions($clientPermissions->pluck('id')->toArray());
    }

    private function createDefaultSuperAdmin(Role $superAdminRole): void
    {
        // Check if any super admin user already exists
        $superAdminExists = User::whereHas('roles', function ($query) {
            $query->where('slug', 'super-admin');
        })->exists();

        if (!$superAdminExists) {
            $superAdmin = User::firstOrCreate(
                ['email' => 'admin@inmobiliaria.com'],
                [
                    'name' => 'Super Administrador',
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                    'position' => 'Super Administrador del Sistema',
                    'bio' => 'Usuario administrador principal del sistema de gestión inmobiliaria',
                    'is_active' => true,
                    'settings' => [
                        'theme' => 'light',
                        'language' => 'es',
                        'notifications' => [
                            'email' => true,
                            'browser' => true,
                            'mobile' => false
                        ]
                    ],
                    'metadata' => [
                        'created_by_seeder' => true,
                        'initial_setup' => true
                    ]
                ]
            );

            // Assign super admin role
            $superAdmin->assignRole($superAdminRole);

            $this->command->info('✓ Usuario Super Administrador creado: admin@inmobiliaria.com / password');
        }
    }
}