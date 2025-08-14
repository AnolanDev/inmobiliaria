<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Permission;

class EmailMarketingPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Email Marketing permissions
        $permissions = [
            [
                'name' => 'Ver Email Marketing',
                'slug' => 'email-marketing-view',
                'description' => 'Ver templates y campañas de email',
                'module' => 'email-marketing',
                'action' => 'view'
            ],
            [
                'name' => 'Crear Email Marketing',
                'slug' => 'email-marketing-create',
                'description' => 'Crear templates y campañas de email',
                'module' => 'email-marketing',
                'action' => 'create'
            ],
            [
                'name' => 'Editar Email Marketing',
                'slug' => 'email-marketing-edit',
                'description' => 'Editar templates y campañas de email',
                'module' => 'email-marketing',
                'action' => 'edit'
            ],
            [
                'name' => 'Eliminar Email Marketing',
                'slug' => 'email-marketing-delete',
                'description' => 'Eliminar templates y campañas de email',
                'module' => 'email-marketing',
                'action' => 'delete'
            ],
            [
                'name' => 'Configurar Email Marketing',
                'slug' => 'email-marketing-config',
                'description' => 'Configurar servicios y parámetros de email marketing',
                'module' => 'email-marketing',
                'action' => 'config'
            ]
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['slug' => $permission['slug']],
                $permission
            );
        }

        $this->command->info('Email Marketing permissions created successfully.');
    }
}