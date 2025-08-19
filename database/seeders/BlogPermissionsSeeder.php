<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class BlogPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create blog permissions
        $permissions = [
            'blogs:view' => 'Ver blogs',
            'blogs:create' => 'Crear blogs',
            'blogs:edit' => 'Editar blogs',
            'blogs:delete' => 'Eliminar blogs',
        ];

        foreach ($permissions as $name => $description) {
            Permission::updateOrCreate(
                ['name' => $name],
                [
                    'guard_name' => 'web',
                    'description' => $description,
                    'module' => 'blogs',
                    'category' => 'Marketing'
                ]
            );
        }

        // Assign permissions to roles
        $this->assignPermissionsToRoles();

        $this->command->info('Blog permissions created successfully.');
    }

    /**
     * Assign permissions to existing roles
     */
    private function assignPermissionsToRoles(): void
    {
        // Super Admin gets all permissions
        $superAdminRole = Role::where('name', 'Super Admin')->first();
        if ($superAdminRole) {
            $superAdminRole->givePermissionTo([
                'blogs:view',
                'blogs:create', 
                'blogs:edit',
                'blogs:delete'
            ]);
        }

        // Admin gets all permissions except delete
        $adminRole = Role::where('name', 'Admin')->first();
        if ($adminRole) {
            $adminRole->givePermissionTo([
                'blogs:view',
                'blogs:create',
                'blogs:edit'
            ]);
        }

        // Manager gets view and create
        $managerRole = Role::where('name', 'Manager')->first();
        if ($managerRole) {
            $managerRole->givePermissionTo([
                'blogs:view',
                'blogs:create'
            ]);
        }

        // Editor gets view and edit
        $editorRole = Role::where('name', 'Editor')->first();
        if ($editorRole) {
            $editorRole->givePermissionTo([
                'blogs:view',
                'blogs:edit'
            ]);
        }

        // Viewer gets only view permission
        $viewerRole = Role::where('name', 'Viewer')->first();
        if ($viewerRole) {
            $viewerRole->givePermissionTo([
                'blogs:view'
            ]);
        }
    }
}
