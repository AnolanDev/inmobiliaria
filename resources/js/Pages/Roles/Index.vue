<template>
    <Head title="Roles" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Gestión de Roles
            </h2>
        </template>

        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Filters and Actions -->
                <div class="mb-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <!-- Search and Filters -->
                            <div class="flex flex-col sm:flex-row gap-4 flex-1">
                                <div class="flex-1">
                                    <TextInput
                                        v-model="filters.search"
                                        type="text"
                                        placeholder="Buscar roles..."
                                        class="w-full"
                                        @input="search"
                                    />
                                </div>
                                
                                <div class="flex gap-2">
                                    <select
                                        v-model="filters.status"
                                        @change="search"
                                        class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    >
                                        <option value="">Todos</option>
                                        <option value="1">Activos</option>
                                        <option value="0">Inactivos</option>
                                    </select>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex gap-2">
                                <Link
                                    :href="route('roles.permissions')"
                                    class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700"
                                >
                                    <IconKey class="w-4 h-4 mr-2" />
                                    Permisos
                                </Link>
                                
                                <Link
                                    :href="route('roles.export', filters)"
                                    class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700"
                                >
                                    <IconDownload class="w-4 h-4 mr-2" />
                                    Exportar
                                </Link>
                                
                                <Link
                                    :href="route('roles.create')"
                                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700"
                                >
                                    <IconPlus class="w-4 h-4 mr-2" />
                                    Nuevo Rol
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Roles Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div
                        v-for="role in roles.data"
                        :key="role.id"
                        class="bg-white overflow-hidden shadow-sm sm:rounded-lg border-l-4 hover:shadow-md transition-shadow"
                        :style="`border-left-color: ${role.color}`"
                    >
                        <div class="p-6">
                            <!-- Role Header -->
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center">
                                    <div
                                        class="w-3 h-3 rounded-full mr-2"
                                        :style="`background-color: ${role.color}`"
                                    ></div>
                                    <h3 class="text-lg font-medium text-gray-900">{{ role.name }}</h3>
                                </div>
                                
                                <div class="flex items-center gap-1">
                                    <span
                                        :class="role.is_active 
                                            ? 'inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800'
                                            : 'inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800'"
                                    >
                                        {{ role.is_active ? 'Activo' : 'Inactivo' }}
                                    </span>
                                    
                                    <span
                                        v-if="role.is_system_role"
                                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800"
                                    >
                                        Sistema
                                    </span>
                                </div>
                            </div>

                            <!-- Role Description -->
                            <p class="text-sm text-gray-600 mb-4">{{ role.description }}</p>

                            <!-- Role Stats -->
                            <div class="flex justify-between items-center mb-4 text-sm text-gray-500">
                                <div class="flex items-center">
                                    <IconUsers class="w-4 h-4 mr-1" />
                                    {{ role.users_count }} usuarios
                                </div>
                                <div class="flex items-center">
                                    <IconKey class="w-4 h-4 mr-1" />
                                    {{ role.permissions_count }} permisos
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <Link
                                        :href="route('roles.show', role.id)"
                                        class="text-indigo-600 hover:text-indigo-900"
                                        title="Ver detalles"
                                    >
                                        <IconEye class="w-4 h-4" />
                                    </Link>
                                    
                                    <Link
                                        v-if="!role.is_system_role"
                                        :href="route('roles.edit', role.id)"
                                        class="text-yellow-600 hover:text-yellow-900"
                                        title="Editar"
                                    >
                                        <IconPencil class="w-4 h-4" />
                                    </Link>
                                    
                                    <button
                                        v-if="!role.is_system_role"
                                        @click="duplicateRole(role)"
                                        class="text-green-600 hover:text-green-900"
                                        title="Duplicar"
                                    >
                                        <IconCopy class="w-4 h-4" />
                                    </button>
                                    
                                    <button
                                        v-if="!role.is_system_role"
                                        @click="toggleStatus(role)"
                                        :class="role.is_active 
                                            ? 'text-red-600 hover:text-red-900'
                                            : 'text-green-600 hover:text-green-900'"
                                        :title="role.is_active ? 'Desactivar' : 'Activar'"
                                    >
                                        <IconToggleLeft v-if="role.is_active" class="w-4 h-4" />
                                        <IconToggleRight v-else class="w-4 h-4" />
                                    </button>
                                    
                                    <button
                                        v-if="!role.is_system_role && role.users_count === 0"
                                        @click="confirmDelete(role)"
                                        class="text-red-600 hover:text-red-900"
                                        title="Eliminar"
                                    >
                                        <IconTrash class="w-4 h-4" />
                                    </button>
                                </div>

                                <span class="text-xs text-gray-400">
                                    Orden: {{ role.sort_order }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pagination -->
                <div v-if="roles.last_page > 1" class="mt-6 bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                        <div class="flex items-center justify-between">
                            <div class="flex-1 flex justify-between sm:hidden">
                                <Link
                                    v-if="roles.prev_page_url"
                                    :href="roles.prev_page_url"
                                    class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                                >
                                    Anterior
                                </Link>
                                <Link
                                    v-if="roles.next_page_url"
                                    :href="roles.next_page_url"
                                    class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                                >
                                    Siguiente
                                </Link>
                            </div>
                            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                                <div>
                                    <p class="text-sm text-gray-700">
                                        Mostrando
                                        {{ roles.from }}
                                        a
                                        {{ roles.to }}
                                        de
                                        {{ roles.total }}
                                        resultados
                                    </p>
                                </div>
                                <div>
                                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                                        <Link
                                            v-if="roles.prev_page_url"
                                            :href="roles.prev_page_url"
                                            class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
                                        >
                                            <IconChevronLeft class="h-5 w-5" />
                                        </Link>
                                        
                                        <template v-for="page in roles.links" :key="page.label">
                                            <Link
                                                v-if="page.url"
                                                :href="page.url"
                                                :class="page.active
                                                    ? 'z-10 bg-indigo-50 border-indigo-500 text-indigo-600 relative inline-flex items-center px-4 py-2 border text-sm font-medium'
                                                    : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50 relative inline-flex items-center px-4 py-2 border text-sm font-medium'"
                                                v-html="page.label"
                                            />
                                        </template>
                                        
                                        <Link
                                            v-if="roles.next_page_url"
                                            :href="roles.next_page_url"
                                            class="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
                                        >
                                            <IconChevronRight class="h-5 w-5" />
                                        </Link>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Empty State -->
                <div v-if="roles.data.length === 0" class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-12 text-center">
                        <IconShield class="h-16 w-16 text-gray-300 mx-auto mb-4" />
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No se encontraron roles</h3>
                        <p class="text-gray-500 mb-6">Intenta ajustar los filtros de búsqueda.</p>
                        <Link
                            :href="route('roles.create')"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700"
                        >
                            <IconPlus class="w-4 h-4 mr-2" />
                            Crear primer rol
                        </Link>
                    </div>
                </div>
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <Modal :show="showDeleteModal" @close="showDeleteModal = false">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                    Eliminar Rol
                </h3>
                <p class="text-sm text-gray-600 mb-6">
                    ¿Estás seguro de que deseas eliminar el rol <strong>{{ roleToDelete?.name }}</strong>?
                    Esta acción no se puede deshacer.
                </p>
                <div class="flex justify-end gap-3">
                    <SecondaryButton @click="showDeleteModal = false">
                        Cancelar
                    </SecondaryButton>
                    <DangerButton @click="deleteRole">
                        Eliminar Rol
                    </DangerButton>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import TextInput from '@/Components/TextInput.vue'
import Modal from '@/Components/Modal.vue'
import SecondaryButton from '@/Components/SecondaryButton.vue'
import DangerButton from '@/Components/DangerButton.vue'
import { 
    IconPlus, 
    IconDownload, 
    IconEye, 
    IconPencil, 
    IconTrash,
    IconKey,
    IconUsers,
    IconCopy,
    IconToggleLeft,
    IconToggleRight,
    IconShield,
    IconChevronLeft,
    IconChevronRight
} from '@tabler/icons-vue'
import { debounce } from 'lodash'

const props = defineProps({
    roles: Object,
    filters: Object,
})

const filters = reactive({ ...props.filters })
const showDeleteModal = ref(false)
const roleToDelete = ref(null)

const search = debounce(() => {
    router.get(route('roles.index'), filters, {
        preserveState: true,
        replace: true,
    })
}, 300)

const toggleStatus = (role) => {
    console.log('Toggle status clicked for role:', role.id)
    router.patch(route('roles.toggle-status', role.id), {}, {
        preserveState: true,
        onSuccess: () => {
            console.log('Toggle status successful')
        },
        onError: (errors) => {
            console.error('Toggle status error:', errors)
        }
    })
}

const duplicateRole = (role) => {
    console.log('Duplicate role clicked for role:', role.id)
    router.post(route('roles.duplicate', role.id), {}, {
        onSuccess: () => {
            console.log('Duplicate role successful')
        },
        onError: (errors) => {
            console.error('Duplicate role error:', errors)
        }
    })
}

const confirmDelete = (role) => {
    console.log('Delete role clicked for role:', role.id)
    roleToDelete.value = role
    showDeleteModal.value = true
}

const deleteRole = () => {
    router.delete(route('roles.destroy', roleToDelete.value.id), {
        preserveState: true,
        onSuccess: () => {
            showDeleteModal.value = false
            roleToDelete.value = null
        },
    })
}
</script>