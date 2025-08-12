<template>
    <Head title="Crear Rol" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Crear Rol
                </h2>
                <Link
                    :href="route('roles.index')"
                    class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700"
                >
                    <IconArrowLeft class="w-4 h-4 mr-2" />
                    Volver
                </Link>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <form @submit.prevent="submit" class="space-y-6">
                    <!-- Basic Information -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-6">Información Básica</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Name -->
                                <div>
                                    <InputLabel for="name" value="Nombre *" />
                                    <TextInput
                                        id="name"
                                        type="text"
                                        class="mt-1 block w-full"
                                        v-model="form.name"
                                        required
                                        autofocus
                                    />
                                    <InputError class="mt-2" :message="form.errors.name" />
                                </div>

                                <!-- Slug -->
                                <div>
                                    <InputLabel for="slug" value="Slug" />
                                    <TextInput
                                        id="slug"
                                        type="text"
                                        class="mt-1 block w-full font-mono text-sm"
                                        v-model="form.slug"
                                        placeholder="Se genera automáticamente"
                                    />
                                    <p class="mt-1 text-sm text-gray-500">Deja vacío para generar automáticamente</p>
                                    <InputError class="mt-2" :message="form.errors.slug" />
                                </div>

                                <!-- Color -->
                                <div>
                                    <InputLabel for="color" value="Color *" />
                                    <div class="mt-1 flex items-center space-x-3">
                                        <input
                                            id="color"
                                            type="color"
                                            v-model="form.color"
                                            class="h-10 w-20 border border-gray-300 rounded cursor-pointer"
                                        />
                                        <TextInput
                                            type="text"
                                            class="flex-1 font-mono text-sm"
                                            v-model="form.color"
                                        />
                                    </div>
                                    <InputError class="mt-2" :message="form.errors.color" />
                                </div>

                                <!-- Sort Order -->
                                <div>
                                    <InputLabel for="sort_order" value="Orden" />
                                    <TextInput
                                        id="sort_order"
                                        type="number"
                                        class="mt-1 block w-full"
                                        v-model="form.sort_order"
                                        min="0"
                                    />
                                    <InputError class="mt-2" :message="form.errors.sort_order" />
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="mt-6">
                                <InputLabel for="description" value="Descripción" />
                                <textarea
                                    id="description"
                                    v-model="form.description"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    rows="3"
                                    maxlength="500"
                                ></textarea>
                                <p class="mt-1 text-sm text-gray-500">{{ form.description?.length || 0 }}/500 caracteres</p>
                                <InputError class="mt-2" :message="form.errors.description" />
                            </div>

                            <!-- Settings -->
                            <div class="mt-6 space-y-4">
                                <div class="flex items-center">
                                    <input
                                        id="is_active"
                                        type="checkbox"
                                        v-model="form.is_active"
                                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                    />
                                    <InputLabel for="is_active" value="Rol activo" class="ml-2" />
                                </div>
                                <p class="text-sm text-gray-500">Los roles inactivos no pueden ser asignados a usuarios.</p>
                                <InputError class="mt-2" :message="form.errors.is_active" />
                            </div>
                        </div>
                    </div>

                    <!-- Permissions -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="text-lg font-medium text-gray-900">Permisos</h3>
                                <div class="flex items-center gap-2">
                                    <button
                                        type="button"
                                        @click="selectAllPermissions"
                                        class="text-sm text-indigo-600 hover:text-indigo-900"
                                    >
                                        Seleccionar todos
                                    </button>
                                    <span class="text-gray-300">|</span>
                                    <button
                                        type="button"
                                        @click="clearAllPermissions"
                                        class="text-sm text-red-600 hover:text-red-900"
                                    >
                                        Limpiar todo
                                    </button>
                                </div>
                            </div>
                            
                            <div v-if="Object.keys(permissions).length > 0" class="space-y-6">
                                <div v-for="(modulePermissions, module) in permissions" :key="module">
                                    <div class="flex items-center justify-between mb-3">
                                        <h4 class="text-base font-medium text-gray-900 capitalize">
                                            {{ module.replace('-', ' ') }}
                                        </h4>
                                        <div class="flex items-center gap-2">
                                            <button
                                                type="button"
                                                @click="toggleModulePermissions(module, modulePermissions)"
                                                class="text-sm text-indigo-600 hover:text-indigo-900"
                                            >
                                                {{ areAllModulePermissionsSelected(modulePermissions) ? 'Deseleccionar' : 'Seleccionar' }} módulo
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                        <div
                                            v-for="permission in modulePermissions"
                                            :key="permission.id"
                                            class="relative flex items-start p-3 border rounded-lg hover:bg-gray-50 transition-colors"
                                            :class="{ 'border-indigo-500 bg-indigo-50': form.permissions.includes(permission.id) }"
                                        >
                                            <div class="flex items-center h-5">
                                                <input
                                                    :id="`permission-${permission.id}`"
                                                    type="checkbox"
                                                    :value="permission.id"
                                                    v-model="form.permissions"
                                                    class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded"
                                                />
                                            </div>
                                            <div class="ml-3 text-sm">
                                                <label :for="`permission-${permission.id}`" class="font-medium text-gray-700 cursor-pointer">
                                                    {{ permission.name }}
                                                </label>
                                                <p class="text-gray-500 text-xs font-mono">{{ permission.slug }}</p>
                                                <p v-if="permission.description" class="text-gray-400 text-xs mt-1">
                                                    {{ permission.description }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div v-else class="text-center py-8">
                                <IconKey class="mx-auto h-12 w-12 text-gray-400" />
                                <h4 class="mt-2 text-sm font-medium text-gray-900">No hay permisos disponibles</h4>
                                <p class="mt-1 text-sm text-gray-500">Contacta al administrador del sistema.</p>
                            </div>
                            
                            <InputError class="mt-2" :message="form.errors.permissions" />
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex items-center justify-end pt-6 border-t border-gray-200">
                        <Link
                            :href="route('roles.index')"
                            class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 transition ease-in-out duration-150 mr-3"
                        >
                            Cancelar
                        </Link>

                        <PrimaryButton 
                            :class="{ 'opacity-25': form.processing }" 
                            :disabled="form.processing"
                        >
                            <IconLoader2 v-if="form.processing" class="w-4 h-4 mr-2 animate-spin" />
                            <IconPlus v-else class="w-4 h-4 mr-2" />
                            {{ form.processing ? 'Creando...' : 'Crear Rol' }}
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import InputError from '@/Components/InputError.vue'
import InputLabel from '@/Components/InputLabel.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import TextInput from '@/Components/TextInput.vue'
import {
    IconArrowLeft,
    IconPlus,
    IconLoader2,
    IconKey
} from '@tabler/icons-vue'

const props = defineProps({
    permissions: {
        type: Object,
        default: () => ({})
    }
})

const form = useForm({
    name: '',
    slug: '',
    description: '',
    color: '#6366f1',
    sort_order: 0,
    is_active: true,
    permissions: []
})

const selectAllPermissions = () => {
    const allPermissionIds = []
    Object.values(props.permissions).forEach(modulePermissions => {
        modulePermissions.forEach(permission => {
            allPermissionIds.push(permission.id)
        })
    })
    form.permissions = allPermissionIds
}

const clearAllPermissions = () => {
    form.permissions = []
}

const toggleModulePermissions = (module, modulePermissions) => {
    const modulePermissionIds = modulePermissions.map(p => p.id)
    const allSelected = modulePermissionIds.every(id => form.permissions.includes(id))
    
    if (allSelected) {
        // Remove all module permissions
        form.permissions = form.permissions.filter(id => !modulePermissionIds.includes(id))
    } else {
        // Add all module permissions
        const newPermissions = [...form.permissions]
        modulePermissionIds.forEach(id => {
            if (!newPermissions.includes(id)) {
                newPermissions.push(id)
            }
        })
        form.permissions = newPermissions
    }
}

const areAllModulePermissionsSelected = (modulePermissions) => {
    const modulePermissionIds = modulePermissions.map(p => p.id)
    return modulePermissionIds.every(id => form.permissions.includes(id))
}

const submit = () => {
    const submitData = { ...form.data() }
    
    // Convert boolean values properly
    submitData.is_active = submitData.is_active ? 1 : 0
    
    form.post(route('roles.store'), {
        data: submitData,
        preserveScroll: true
    })
}
</script>