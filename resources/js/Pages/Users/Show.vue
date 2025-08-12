<template>
    <Head title="Usuario - {{ user.name }}" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Detalles del Usuario
                </h2>
                <div class="flex gap-2">
                    <Link
                        :href="route('users.edit', user.id)"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700"
                    >
                        <IconPencil class="w-4 h-4 mr-2" />
                        Editar
                    </Link>
                    <Link
                        :href="route('users.index')"
                        class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700"
                    >
                        <IconArrowLeft class="w-4 h-4 mr-2" />
                        Volver
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <!-- User Profile Card -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:space-x-6">
                            <!-- Avatar -->
                            <div class="flex-shrink-0 mb-4 sm:mb-0">
                                <img
                                    class="h-24 w-24 rounded-full object-cover ring-4 ring-gray-100"
                                    :src="user.avatar_url"
                                    :alt="user.name"
                                />
                            </div>

                            <!-- Basic Info -->
                            <div class="flex-1">
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                                    <div>
                                        <h3 class="text-2xl font-bold text-gray-900">{{ user.name }}</h3>
                                        <p class="text-sm text-gray-500">{{ user.email }}</p>
                                        <p v-if="user.position" class="text-sm text-gray-600 mt-1">{{ user.position }}</p>
                                    </div>
                                    
                                    <div class="mt-4 sm:mt-0">
                                        <span
                                            :class="user.is_active 
                                                ? 'inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800'
                                                : 'inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800'"
                                        >
                                            <IconCircle 
                                                :class="user.is_active ? 'text-green-400 mr-1.5 h-2.5 w-2.5' : 'text-red-400 mr-1.5 h-2.5 w-2.5'"
                                                fill="currentColor"
                                            />
                                            {{ user.is_active ? 'Activo' : 'Inactivo' }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Contact Info -->
                                <div class="mt-4 flex flex-wrap gap-4 text-sm text-gray-600">
                                    <div v-if="user.phone" class="flex items-center">
                                        <IconPhone class="h-4 w-4 mr-1.5 text-gray-400" />
                                        {{ user.phone }}
                                    </div>
                                    <div class="flex items-center">
                                        <IconCalendar class="h-4 w-4 mr-1.5 text-gray-400" />
                                        Creado {{ formatDate(user.created_at) }}
                                    </div>
                                    <div v-if="user.last_login_at" class="flex items-center">
                                        <IconClock class="h-4 w-4 mr-1.5 text-gray-400" />
                                        Último login: {{ user.last_login_formatted }}
                                    </div>
                                </div>

                                <!-- Bio -->
                                <div v-if="user.bio" class="mt-4">
                                    <p class="text-gray-700">{{ user.bio }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Roles Card -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-medium text-gray-900">Roles Asignados</h3>
                                <span class="text-sm text-gray-500">{{ user.roles.length }} roles</span>
                            </div>
                            
                            <div v-if="user.roles.length > 0" class="space-y-3">
                                <div
                                    v-for="role in user.roles"
                                    :key="role.id"
                                    class="flex items-center justify-between p-3 border border-gray-200 rounded-lg"
                                >
                                    <div class="flex items-center">
                                        <div
                                            class="w-3 h-3 rounded-full mr-3"
                                            :style="`background-color: ${role.color}`"
                                        ></div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">{{ role.name }}</p>
                                            <p class="text-xs text-gray-500">{{ role.description }}</p>
                                        </div>
                                    </div>
                                    <span class="text-xs text-gray-400">
                                        {{ role.pivot.assigned_at ? formatDate(role.pivot.assigned_at) : '' }}
                                    </span>
                                </div>
                            </div>
                            
                            <div v-else class="text-center py-6">
                                <IconShieldX class="h-12 w-12 text-gray-300 mx-auto mb-3" />
                                <p class="text-gray-500">No tiene roles asignados</p>
                            </div>
                        </div>
                    </div>

                    <!-- Activity Info -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Información de Actividad</h3>
                            
                            <div class="space-y-4">
                                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                    <span class="text-sm text-gray-600">Último login</span>
                                    <span class="text-sm font-medium">
                                        {{ user.last_login_formatted || 'Nunca' }}
                                    </span>
                                </div>
                                
                                <div v-if="user.last_login_ip" class="flex justify-between items-center py-2 border-b border-gray-100">
                                    <span class="text-sm text-gray-600">IP del último login</span>
                                    <span class="text-sm font-medium">{{ user.last_login_ip }}</span>
                                </div>
                                
                                <div class="flex justify-between items-center py-2 border-b border-gray-100">
                                    <span class="text-sm text-gray-600">Cambio de contraseña forzado</span>
                                    <span
                                        :class="user.force_password_change 
                                            ? 'text-sm font-medium text-red-600' 
                                            : 'text-sm font-medium text-green-600'"
                                    >
                                        {{ user.force_password_change ? 'Sí' : 'No' }}
                                    </span>
                                </div>
                                
                                <div v-if="user.invited_by" class="flex justify-between items-center py-2 border-b border-gray-100">
                                    <span class="text-sm text-gray-600">Invitado por</span>
                                    <span class="text-sm font-medium">{{ user.invited_by?.name }}</span>
                                </div>
                                
                                <div v-if="user.invited_at" class="flex justify-between items-center py-2 border-b border-gray-100">
                                    <span class="text-sm text-gray-600">Fecha de invitación</span>
                                    <span class="text-sm font-medium">{{ formatDate(user.invited_at) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Permissions Card -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-medium text-gray-900">Permisos</h3>
                            <span class="text-sm text-gray-500">
                                {{ Object.values(permissions).flat().length }} permisos
                            </span>
                        </div>
                        
                        <div v-if="Object.keys(permissions).length > 0">
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                <div
                                    v-for="(modulePermissions, module) in permissions"
                                    :key="module"
                                    class="border border-gray-200 rounded-lg p-4"
                                >
                                    <h4 class="font-medium text-gray-900 mb-3 capitalize">
                                        {{ getModuleName(module) }}
                                    </h4>
                                    <div class="space-y-2">
                                        <div
                                            v-for="permission in modulePermissions"
                                            :key="permission.id"
                                            class="flex items-center justify-between text-sm"
                                        >
                                            <span class="text-gray-600">{{ getActionName(permission.action) }}</span>
                                            <div
                                                class="w-2 h-2 rounded-full"
                                                :style="`background-color: ${permission.action_color}`"
                                            ></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div v-else class="text-center py-8">
                            <IconLockOpen class="h-12 w-12 text-gray-300 mx-auto mb-3" />
                            <p class="text-gray-500">No tiene permisos asignados</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { 
    IconArrowLeft, 
    IconPencil, 
    IconPhone, 
    IconCalendar, 
    IconClock,
    IconCircle,
    IconShieldX,
    IconLockOpen
} from '@tabler/icons-vue'

const props = defineProps({
    user: Object,
    permissions: Object,
})

const formatDate = (dateString) => {
    if (!dateString) return ''
    return new Date(dateString).toLocaleDateString('es-CO', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
    })
}

const getModuleName = (module) => {
    const modules = {
        'dashboard': 'Dashboard',
        'users': 'Usuarios',
        'roles': 'Roles',
        'permissions': 'Permisos',
        'properties': 'Propiedades',
        'projects': 'Proyectos',
        'clients': 'Clientes',
        'agents': 'Agentes',
        'visits': 'Visitas',
        'reports': 'Reportes',
        'settings': 'Configuración',
    }
    return modules[module] || module.charAt(0).toUpperCase() + module.slice(1)
}

const getActionName = (action) => {
    const actions = {
        'view': 'Ver',
        'create': 'Crear',
        'edit': 'Editar',
        'delete': 'Eliminar',
        'manage': 'Gestionar',
        'export': 'Exportar',
        'import': 'Importar',
    }
    return actions[action] || action.charAt(0).toUpperCase() + action.slice(1)
}
</script>