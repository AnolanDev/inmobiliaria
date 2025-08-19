<template>
    <Head title="Usuarios" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Gestión de Usuarios
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
                                        placeholder="Buscar usuarios..."
                                        class="w-full"
                                        @input="search"
                                    />
                                </div>
                                
                                <div class="flex gap-2">
                                    <select
                                        v-model="filters.role"
                                        @change="search"
                                        class="rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    >
                                        <option value="">Todos los roles</option>
                                        <option v-for="role in roles" :key="role.id" :value="role.slug">
                                            {{ role.name }}
                                        </option>
                                    </select>
                                    
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
                                    :href="route('users.export', filters)"
                                    class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                                >
                                    <IconDownload class="w-4 h-4 mr-2" />
                                    Exportar
                                </Link>
                                
                                <Link
                                    :href="route('register')"
                                    class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150"
                                >
                                    <IconPlus class="w-4 h-4 mr-2" />
                                    Nuevo Usuario
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Users Table -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Usuario
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Roles
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Estado
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Último Login
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Acciones
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr v-for="user in users.data" :key="user.id" class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <img
                                                    class="h-10 w-10 rounded-full object-cover"
                                                    :src="user.avatar_url"
                                                    :alt="user.name"
                                                />
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">{{ user.name }}</div>
                                                <div class="text-sm text-gray-500">{{ user.email }}</div>
                                                <div v-if="user.position" class="text-xs text-gray-400">{{ user.position }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex flex-wrap gap-1">
                                            <span
                                                v-for="role in user.roles"
                                                :key="role.id"
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                                                :style="`background-color: ${role.color}20; color: ${role.color}`"
                                            >
                                                {{ role.name }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span
                                            :class="user.is_active 
                                                ? 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800'
                                                : 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800'"
                                        >
                                            {{ user.is_active ? 'Activo' : 'Inactivo' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ user.last_login_formatted || 'Nunca' }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex items-center gap-2">
                                            <Link
                                                :href="route('users.show', user.id)"
                                                class="text-indigo-600 hover:text-indigo-900"
                                                title="Ver detalles"
                                            >
                                                <IconEye class="w-4 h-4" />
                                            </Link>
                                            
                                            <Link
                                                :href="route('users.edit', user.id)"
                                                class="text-yellow-600 hover:text-yellow-900"
                                                title="Editar"
                                            >
                                                <IconPencil class="w-4 h-4" />
                                            </Link>
                                            
                                            <button
                                                @click="toggleStatus(user)"
                                                :class="user.is_active 
                                                    ? 'text-red-600 hover:text-red-900'
                                                    : 'text-green-600 hover:text-green-900'"
                                                :title="user.is_active ? 'Desactivar' : 'Activar'"
                                            >
                                                <IconUserX v-if="user.is_active" class="w-4 h-4" />
                                                <IconUserCheck v-else class="w-4 h-4" />
                                            </button>
                                            
                                            <button
                                                @click="confirmDelete(user)"
                                                class="text-red-600 hover:text-red-900"
                                                title="Eliminar"
                                                v-if="!user.roles.some(role => role.slug === 'super-admin')"
                                            >
                                                <IconTrash class="w-4 h-4" />
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                        <div class="flex items-center justify-between">
                            <div class="flex-1 flex justify-between sm:hidden">
                                <Link
                                    v-if="users.prev_page_url"
                                    :href="users.prev_page_url"
                                    class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                                >
                                    Anterior
                                </Link>
                                <Link
                                    v-if="users.next_page_url"
                                    :href="users.next_page_url"
                                    class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                                >
                                    Siguiente
                                </Link>
                            </div>
                            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                                <div>
                                    <p class="text-sm text-gray-700">
                                        Mostrando
                                        {{ users.from }}
                                        a
                                        {{ users.to }}
                                        de
                                        {{ users.total }}
                                        resultados
                                    </p>
                                </div>
                                <div>
                                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                                        <Link
                                            v-if="users.prev_page_url"
                                            :href="users.prev_page_url"
                                            class="relative inline-flex items-center px-2 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50"
                                        >
                                            <IconChevronLeft class="h-5 w-5" />
                                        </Link>
                                        
                                        <template v-for="page in users.links" :key="page.label">
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
                                            v-if="users.next_page_url"
                                            :href="users.next_page_url"
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
            </div>
        </div>

        <!-- Delete Confirmation Modal -->
        <Modal :show="showDeleteModal" @close="showDeleteModal = false">
            <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">
                    Eliminar Usuario
                </h3>
                <p class="text-sm text-gray-600 mb-6">
                    ¿Estás seguro de que deseas eliminar al usuario <strong>{{ userToDelete?.name }}</strong>?
                    Esta acción no se puede deshacer.
                </p>
                <div class="flex justify-end gap-3">
                    <SecondaryButton @click="showDeleteModal = false">
                        Cancelar
                    </SecondaryButton>
                    <DangerButton @click="deleteUser">
                        Eliminar Usuario
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
    IconUserX, 
    IconUserCheck,
    IconChevronLeft,
    IconChevronRight
} from '@tabler/icons-vue'
import { debounce } from 'lodash'

const props = defineProps({
    users: Object,
    roles: Array,
    filters: Object,
})

const filters = reactive({ ...props.filters })
const showDeleteModal = ref(false)
const userToDelete = ref(null)

const search = debounce(() => {
    router.get(route('users.index'), filters, {
        preserveState: true,
        replace: true,
    })
}, 300)

const toggleStatus = (user) => {
    router.patch(route('users.toggle-status', user.id), {}, {
        preserveState: true,
        onSuccess: () => {
            // Page will be refreshed automatically
        },
    })
}

const confirmDelete = (user) => {
    userToDelete.value = user
    showDeleteModal.value = true
}

const deleteUser = () => {
    router.delete(route('users.destroy', userToDelete.value.id), {
        preserveState: true,
        onSuccess: () => {
            showDeleteModal.value = false
            userToDelete.value = null
        },
    })
}
</script>