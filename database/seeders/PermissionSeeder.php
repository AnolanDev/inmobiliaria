<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            // Dashboard permissions
            [
                'name' => 'Ver Dashboard',
                'module' => 'dashboard',
                'action' => 'view',
                'description' => 'Permite ver el dashboard principal con métricas y estadísticas',
                'sort_order' => 1,
            ],

            // Users management permissions
            [
                'name' => 'Ver Usuarios',
                'module' => 'users',
                'action' => 'view',
                'description' => 'Permite ver la lista de usuarios del sistema',
                'sort_order' => 1,
            ],
            [
                'name' => 'Crear Usuarios',
                'module' => 'users',
                'action' => 'create',
                'description' => 'Permite crear nuevos usuarios en el sistema',
                'sort_order' => 2,
            ],
            [
                'name' => 'Editar Usuarios',
                'module' => 'users',
                'action' => 'edit',
                'description' => 'Permite editar información de usuarios existentes',
                'sort_order' => 3,
            ],
            [
                'name' => 'Eliminar Usuarios',
                'module' => 'users',
                'action' => 'delete',
                'description' => 'Permite eliminar usuarios del sistema',
                'sort_order' => 4,
            ],
            [
                'name' => 'Gestionar Usuarios',
                'module' => 'users',
                'action' => 'manage',
                'description' => 'Permite gestión completa de usuarios incluyendo activar/desactivar',
                'sort_order' => 5,
            ],

            // Roles management permissions
            [
                'name' => 'Ver Roles',
                'module' => 'roles',
                'action' => 'view',
                'description' => 'Permite ver la lista de roles del sistema',
                'sort_order' => 1,
            ],
            [
                'name' => 'Crear Roles',
                'module' => 'roles',
                'action' => 'create',
                'description' => 'Permite crear nuevos roles en el sistema',
                'sort_order' => 2,
            ],
            [
                'name' => 'Editar Roles',
                'module' => 'roles',
                'action' => 'edit',
                'description' => 'Permite editar roles existentes',
                'sort_order' => 3,
            ],
            [
                'name' => 'Eliminar Roles',
                'module' => 'roles',
                'action' => 'delete',
                'description' => 'Permite eliminar roles del sistema',
                'sort_order' => 4,
            ],
            [
                'name' => 'Gestionar Roles',
                'module' => 'roles',
                'action' => 'manage',
                'description' => 'Permite gestión completa de roles y asignación de permisos',
                'sort_order' => 5,
            ],

            // Permissions management permissions
            [
                'name' => 'Ver Permisos',
                'module' => 'permissions',
                'action' => 'view',
                'description' => 'Permite ver la lista de permisos del sistema',
                'sort_order' => 1,
            ],
            [
                'name' => 'Gestionar Permisos',
                'module' => 'permissions',
                'action' => 'manage',
                'description' => 'Permite gestión completa de permisos del sistema',
                'sort_order' => 2,
            ],

            // Properties permissions
            [
                'name' => 'Ver Propiedades',
                'module' => 'properties',
                'action' => 'view',
                'description' => 'Permite ver la lista de propiedades',
                'sort_order' => 1,
            ],
            [
                'name' => 'Crear Propiedades',
                'module' => 'properties',
                'action' => 'create',
                'description' => 'Permite crear nuevas propiedades',
                'sort_order' => 2,
            ],
            [
                'name' => 'Editar Propiedades',
                'module' => 'properties',
                'action' => 'edit',
                'description' => 'Permite editar propiedades existentes',
                'sort_order' => 3,
            ],
            [
                'name' => 'Eliminar Propiedades',
                'module' => 'properties',
                'action' => 'delete',
                'description' => 'Permite eliminar propiedades',
                'sort_order' => 4,
            ],
            [
                'name' => 'Gestionar Propiedades',
                'module' => 'properties',
                'action' => 'manage',
                'description' => 'Permite gestión completa de propiedades',
                'sort_order' => 5,
            ],
            [
                'name' => 'Exportar Propiedades',
                'module' => 'properties',
                'action' => 'export',
                'description' => 'Permite exportar datos de propiedades',
                'sort_order' => 6,
            ],
            [
                'name' => 'Importar Propiedades',
                'module' => 'properties',
                'action' => 'import',
                'description' => 'Permite importar datos de propiedades',
                'sort_order' => 7,
            ],

            // Projects permissions
            [
                'name' => 'Ver Proyectos',
                'module' => 'projects',
                'action' => 'view',
                'description' => 'Permite ver la lista de proyectos',
                'sort_order' => 1,
            ],
            [
                'name' => 'Crear Proyectos',
                'module' => 'projects',
                'action' => 'create',
                'description' => 'Permite crear nuevos proyectos',
                'sort_order' => 2,
            ],
            [
                'name' => 'Editar Proyectos',
                'module' => 'projects',
                'action' => 'edit',
                'description' => 'Permite editar proyectos existentes',
                'sort_order' => 3,
            ],
            [
                'name' => 'Eliminar Proyectos',
                'module' => 'projects',
                'action' => 'delete',
                'description' => 'Permite eliminar proyectos',
                'sort_order' => 4,
            ],
            [
                'name' => 'Gestionar Proyectos',
                'module' => 'projects',
                'action' => 'manage',
                'description' => 'Permite gestión completa de proyectos',
                'sort_order' => 5,
            ],

            // Clients permissions
            [
                'name' => 'Ver Clientes',
                'module' => 'clients',
                'action' => 'view',
                'description' => 'Permite ver la lista de clientes',
                'sort_order' => 1,
            ],
            [
                'name' => 'Crear Clientes',
                'module' => 'clients',
                'action' => 'create',
                'description' => 'Permite crear nuevos clientes',
                'sort_order' => 2,
            ],
            [
                'name' => 'Editar Clientes',
                'module' => 'clients',
                'action' => 'edit',
                'description' => 'Permite editar clientes existentes',
                'sort_order' => 3,
            ],
            [
                'name' => 'Eliminar Clientes',
                'module' => 'clients',
                'action' => 'delete',
                'description' => 'Permite eliminar clientes',
                'sort_order' => 4,
            ],
            [
                'name' => 'Gestionar Clientes',
                'module' => 'clients',
                'action' => 'manage',
                'description' => 'Permite gestión completa de clientes',
                'sort_order' => 5,
            ],
            [
                'name' => 'Exportar Clientes',
                'module' => 'clients',
                'action' => 'export',
                'description' => 'Permite exportar datos de clientes',
                'sort_order' => 6,
            ],
            [
                'name' => 'Importar Clientes',
                'module' => 'clients',
                'action' => 'import',
                'description' => 'Permite importar datos de clientes',
                'sort_order' => 7,
            ],

            // Agents permissions
            [
                'name' => 'Ver Agentes',
                'module' => 'agents',
                'action' => 'view',
                'description' => 'Permite ver la lista de agentes',
                'sort_order' => 1,
            ],
            [
                'name' => 'Crear Agentes',
                'module' => 'agents',
                'action' => 'create',
                'description' => 'Permite crear nuevos agentes',
                'sort_order' => 2,
            ],
            [
                'name' => 'Editar Agentes',
                'module' => 'agents',
                'action' => 'edit',
                'description' => 'Permite editar agentes existentes',
                'sort_order' => 3,
            ],
            [
                'name' => 'Eliminar Agentes',
                'module' => 'agents',
                'action' => 'delete',
                'description' => 'Permite eliminar agentes',
                'sort_order' => 4,
            ],
            [
                'name' => 'Gestionar Agentes',
                'module' => 'agents',
                'action' => 'manage',
                'description' => 'Permite gestión completa de agentes',
                'sort_order' => 5,
            ],

            // Visits permissions
            [
                'name' => 'Ver Visitas',
                'module' => 'visits',
                'action' => 'view',
                'description' => 'Permite ver el calendario y lista de visitas',
                'sort_order' => 1,
            ],
            [
                'name' => 'Crear Visitas',
                'module' => 'visits',
                'action' => 'create',
                'description' => 'Permite agendar nuevas visitas',
                'sort_order' => 2,
            ],
            [
                'name' => 'Editar Visitas',
                'module' => 'visits',
                'action' => 'edit',
                'description' => 'Permite modificar visitas existentes',
                'sort_order' => 3,
            ],
            [
                'name' => 'Eliminar Visitas',
                'module' => 'visits',
                'action' => 'delete',
                'description' => 'Permite eliminar visitas',
                'sort_order' => 4,
            ],
            [
                'name' => 'Gestionar Visitas',
                'module' => 'visits',
                'action' => 'manage',
                'description' => 'Permite gestión completa del calendario de visitas',
                'sort_order' => 5,
            ],

            // Reports permissions
            [
                'name' => 'Ver Reportes',
                'module' => 'reports',
                'action' => 'view',
                'description' => 'Permite ver reportes y estadísticas del sistema',
                'sort_order' => 1,
            ],
            [
                'name' => 'Crear Reportes',
                'module' => 'reports',
                'action' => 'create',
                'description' => 'Permite crear reportes personalizados',
                'sort_order' => 2,
            ],
            [
                'name' => 'Exportar Reportes',
                'module' => 'reports',
                'action' => 'export',
                'description' => 'Permite exportar reportes en diferentes formatos',
                'sort_order' => 3,
            ],

            // Settings permissions
            [
                'name' => 'Ver Configuración',
                'module' => 'settings',
                'action' => 'view',
                'description' => 'Permite ver la configuración del sistema',
                'sort_order' => 1,
            ],
            [
                'name' => 'Gestionar Configuración',
                'module' => 'settings',
                'action' => 'manage',
                'description' => 'Permite gestionar la configuración general del sistema',
                'sort_order' => 2,
            ],

            // Marketing permissions
            [
                'name' => 'Ver Marketing',
                'module' => 'marketing',
                'action' => 'view',
                'description' => 'Permite acceder al módulo de marketing',
                'sort_order' => 1,
            ],

            // Campaigns permissions
            [
                'name' => 'Ver Campañas',
                'module' => 'campaigns',
                'action' => 'view',
                'description' => 'Permite ver la lista de campañas de marketing',
                'sort_order' => 1,
            ],
            [
                'name' => 'Crear Campañas',
                'module' => 'campaigns',
                'action' => 'create',
                'description' => 'Permite crear nuevas campañas de marketing',
                'sort_order' => 2,
            ],
            [
                'name' => 'Editar Campañas',
                'module' => 'campaigns',
                'action' => 'edit',
                'description' => 'Permite editar campañas de marketing existentes',
                'sort_order' => 3,
            ],
            [
                'name' => 'Eliminar Campañas',
                'module' => 'campaigns',
                'action' => 'delete',
                'description' => 'Permite eliminar campañas de marketing',
                'sort_order' => 4,
            ],
            [
                'name' => 'Gestionar Campañas',
                'module' => 'campaigns',
                'action' => 'manage',
                'description' => 'Permite gestión completa de campañas de marketing',
                'sort_order' => 5,
            ],

            // Leads permissions
            [
                'name' => 'Ver Leads',
                'module' => 'leads',
                'action' => 'view',
                'description' => 'Permite ver la lista de leads',
                'sort_order' => 1,
            ],
            [
                'name' => 'Crear Leads',
                'module' => 'leads',
                'action' => 'create',
                'description' => 'Permite crear nuevos leads',
                'sort_order' => 2,
            ],
            [
                'name' => 'Editar Leads',
                'module' => 'leads',
                'action' => 'edit',
                'description' => 'Permite editar leads existentes',
                'sort_order' => 3,
            ],
            [
                'name' => 'Eliminar Leads',
                'module' => 'leads',
                'action' => 'delete',
                'description' => 'Permite eliminar leads',
                'sort_order' => 4,
            ],
            [
                'name' => 'Gestionar Leads',
                'module' => 'leads',
                'action' => 'manage',
                'description' => 'Permite gestión completa de leads',
                'sort_order' => 5,
            ],
            [
                'name' => 'Exportar Leads',
                'module' => 'leads',
                'action' => 'export',
                'description' => 'Permite exportar datos de leads',
                'sort_order' => 6,
            ],

            // Activities permissions
            [
                'name' => 'Ver Actividades',
                'module' => 'activities',
                'action' => 'view',
                'description' => 'Permite ver la lista de actividades y timeline',
                'sort_order' => 1,
            ],
            [
                'name' => 'Crear Actividades',
                'module' => 'activities',
                'action' => 'create',
                'description' => 'Permite crear nuevas actividades y tareas',
                'sort_order' => 2,
            ],
            [
                'name' => 'Editar Actividades',
                'module' => 'activities',
                'action' => 'edit',
                'description' => 'Permite editar actividades y cambiar su estado',
                'sort_order' => 3,
            ],
            [
                'name' => 'Eliminar Actividades',
                'module' => 'activities',
                'action' => 'delete',
                'description' => 'Permite eliminar actividades',
                'sort_order' => 4,
            ],
            [
                'name' => 'Gestionar Actividades',
                'module' => 'activities',
                'action' => 'manage',
                'description' => 'Permite gestión completa del sistema de actividades',
                'sort_order' => 5,
            ],

            // Blogs permissions
            [
                'name' => 'Ver Blogs',
                'module' => 'blogs',
                'action' => 'view',
                'description' => 'Permite ver la lista de blogs y artículos',
                'sort_order' => 1,
            ],
            [
                'name' => 'Crear Blogs',
                'module' => 'blogs',
                'action' => 'create',
                'description' => 'Permite crear nuevos blogs y artículos',
                'sort_order' => 2,
            ],
            [
                'name' => 'Editar Blogs',
                'module' => 'blogs',
                'action' => 'edit',
                'description' => 'Permite editar blogs existentes y cambiar su estado',
                'sort_order' => 3,
            ],
            [
                'name' => 'Eliminar Blogs',
                'module' => 'blogs',
                'action' => 'delete',
                'description' => 'Permite eliminar blogs y artículos',
                'sort_order' => 4,
            ],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                [
                    'module' => $permission['module'],
                    'action' => $permission['action'],
                ],
                $permission
            );
        }
    }
}