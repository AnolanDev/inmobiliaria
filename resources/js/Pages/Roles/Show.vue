<template>
    <Head :title="`Rol: ${role.name}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Detalles del Rol
                </h2>
                <div class="flex items-center gap-2">
                    <Link
                        :href="route('roles.index')"
                        class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700"
                    >
                        <IconArrowLeft class="w-4 h-4 mr-2" />
                        Volver
                    </Link>
                    
                    <Link
                        v-if="!role.is_system_role"
                        :href="route('roles.edit', role.id)"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700"
                    >
                        <IconPencil class="w-4 h-4 mr-2" />
                        Editar
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <!-- Role Info Card -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex items-center justify-between mb-6">
                            <div class="flex items-center">
                                <div
                                    class="w-4 h-4 rounded-full mr-3"
                                    :style="`background-color: ${role.color}`"
                                ></div>
                                <h3 class="text-2xl font-bold text-gray-900">{{ role.name }}</h3>
                            </div>
                            
                            <div class="flex items-center gap-2">
                                <span
                                    :class="role.is_active 
                                        ? 'inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800'
                                        : 'inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800'"
                                >
                                    {{ role.is_active ? 'Activo' : 'Inactivo' }}
                                </span>
                                
                                <span
                                    v-if="role.is_system_role"
                                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800"
                                >
                                    <IconShield class="w-4 h-4 mr-1" />
                                    Sistema
                                </span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Descripción</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ role.description || 'Sin descripción' }}</dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Slug</dt>
                                <dd class="mt-1 text-sm text-gray-900 font-mono bg-gray-100 px-2 py-1 rounded">{{ role.slug }}</dd>
                            </div>
                            
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Orden</dt>
                                <dd class="mt-1 text-sm text-gray-900">{{ role.sort_order }}</dd>
                            </div>
                        </div>

                        <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="bg-gray-50 p-4 rounded-lg text-center">
                                <dt class="text-sm font-medium text-gray-500">Usuarios Asignados</dt>
                                <dd class="mt-2 text-3xl font-bold text-indigo-600">{{ role.users_count }}</dd>
                            </div>
                            
                            <div class="bg-gray-50 p-4 rounded-lg text-center">
                                <dt class="text-sm font-medium text-gray-500">Permisos</dt>
                                <dd class="mt-2 text-3xl font-bold text-green-600">{{ role.permissions_count }}</dd>
                            </div>
                            
                            <div class="bg-gray-50 p-4 rounded-lg text-center">
                                <dt class="text-sm font-medium text-gray-500">Creado</dt>
                                <dd class="mt-2 text-sm text-gray-900">{{ formatDate(role.created_at) }}</dd>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Permissions Section -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-6">Permisos Asignados</h3>
                        
                        <div v-if="groupedPermissions && Object.keys(groupedPermissions).length > 0" class="space-y-6">
                            <div v-for="(permissions, module) in groupedPermissions" :key="module">
                                <h4 class="text-base font-medium text-gray-900 mb-3 capitalize">
                                    {{ module.replace('-', ' ') }}
                                </h4>
                                
                                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                                    <div
                                        v-for="permission in permissions"
                                        :key="permission.id"
                                        class="flex items-center p-3 bg-gray-50 rounded-lg"
                                    >
                                        <div class="flex-shrink-0">
                                            <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                        </div>
                                        <div class="ml-3">
                                            <p class="text-sm font-medium text-gray-900">{{ permission.name }}</p>
                                            <p class="text-xs text-gray-500 font-mono">{{ permission.slug }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div v-else class="text-center py-8">
                            <IconKey class="mx-auto h-12 w-12 text-gray-400" />
                            <h4 class="mt-2 text-sm font-medium text-gray-900">Sin permisos asignados</h4>
                            <p class="mt-1 text-sm text-gray-500">Este rol no tiene permisos asignados.</p>
                        </div>
                    </div>
                </div>

                <!-- Users Section -->
                <div v-if="role.users && role.users.length > 0" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-6">Usuarios con este Rol</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div
                                v-for="user in role.users"
                                :key="user.id"
                                class="flex items-center p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors"
                            >
                                <div class="flex-shrink-0">
                                    <img
                                        v-if="user.avatar_url"
                                        :src="user.avatar_url"
                                        :alt="user.name"
                                        class="h-10 w-10 rounded-full object-cover"
                                    />
                                    <div
                                        v-else
                                        class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center"
                                    >
                                        <IconUser class="h-5 w-5 text-gray-600" />
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-900">{{ user.name }}</p>
                                    <p class="text-sm text-gray-500">{{ user.email }}</p>
                                    <p v-if="user.position" class="text-xs text-gray-400">{{ user.position }}</p>
                                </div>
                                <div class="ml-auto">
                                    <Link
                                        :href="route('users.show', user.id)"
                                        class="text-indigo-600 hover:text-indigo-900"
                                        title="Ver usuario"
                                    >
                                        <IconEye class="w-4 h-4" />
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Metadata Section (if exists) -->
                <div v-if="role.metadata && Object.keys(role.metadata).length > 0" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-6">Metadatos</h3>
                        
                        <div class="bg-gray-50 rounded-lg p-4">
                            <pre class="text-sm text-gray-700">{{ JSON.stringify(role.metadata, null, 2) }}</pre>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { computed } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import {
    IconArrowLeft,
    IconPencil,
    IconShield,
    IconKey,
    IconUser,
    IconEye
} from '@tabler/icons-vue'

const props = defineProps({
    role: {
        type: Object,
        required: true
    }
})

const groupedPermissions = computed(() => {
    if (!props.role.permissions) return {}
    
    return props.role.permissions.reduce((groups, permission) => {
        const module = permission.module || 'other'
        if (!groups[module]) {
            groups[module] = []
        }
        groups[module].push(permission)
        return groups
    }, {})
})

const formatDate = (dateString) => {
    if (!dateString) return 'N/A'
    
    const date = new Date(dateString)
    return date.toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    })
}
</script>