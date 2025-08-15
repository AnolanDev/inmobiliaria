<template>
    <Head title="Editar Usuario" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Editar Usuario
                </h2>
                <Link
                    :href="route('users.index')"
                    class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700"
                >
                    <IconArrowLeft class="w-4 h-4 mr-2" />
                    Volver
                </Link>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <form @submit.prevent="submit" class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Personal Information -->
                            <div class="md:col-span-2">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Información Personal</h3>
                            </div>

                            <!-- Avatar -->
                            <div class="md:col-span-2">
                                <InputLabel for="avatar" value="Avatar" />
                                <div class="mt-1 flex items-center space-x-4">
                                    <div class="flex-shrink-0">
                                        <img
                                            v-if="avatarPreview"
                                            :src="avatarPreview"
                                            alt="Preview"
                                            class="h-16 w-16 rounded-full object-cover"
                                        />
                                        <img
                                            v-else-if="user.avatar_url"
                                            :src="user.avatar_url"
                                            alt="Avatar actual"
                                            class="h-16 w-16 rounded-full object-cover"
                                        />
                                        <div
                                            v-else
                                            class="h-16 w-16 rounded-full bg-gray-200 flex items-center justify-center"
                                        >
                                            <IconUser class="h-8 w-8 text-gray-400" />
                                        </div>
                                    </div>
                                    <div>
                                        <input
                                            id="avatar"
                                            type="file"
                                            accept="image/*"
                                            @change="handleAvatarChange"
                                            class="sr-only"
                                            ref="avatarInput"
                                        />
                                        <button
                                            type="button"
                                            @click="$refs.avatarInput.click()"
                                            class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50"
                                        >
                                            <IconCamera class="w-4 h-4 mr-2" />
                                            Cambiar Avatar
                                        </button>
                                    </div>
                                </div>
                                <InputError class="mt-2" :message="form.errors.avatar" />
                            </div>

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
                                    autocomplete="name"
                                />
                                <InputError class="mt-2" :message="form.errors.name" />
                            </div>

                            <!-- Email -->
                            <div>
                                <InputLabel for="email" value="Email *" />
                                <TextInput
                                    id="email"
                                    type="email"
                                    class="mt-1 block w-full"
                                    v-model="form.email"
                                    required
                                    autocomplete="username"
                                />
                                <InputError class="mt-2" :message="form.errors.email" />
                            </div>

                            <!-- Phone -->
                            <div>
                                <InputLabel for="phone" value="Teléfono" />
                                <TextInput
                                    id="phone"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.phone"
                                    autocomplete="tel"
                                />
                                <InputError class="mt-2" :message="form.errors.phone" />
                            </div>

                            <!-- Position -->
                            <div>
                                <InputLabel for="position" value="Cargo" />
                                <TextInput
                                    id="position"
                                    type="text"
                                    class="mt-1 block w-full"
                                    v-model="form.position"
                                    autocomplete="organization-title"
                                />
                                <InputError class="mt-2" :message="form.errors.position" />
                            </div>

                            <!-- Bio -->
                            <div class="md:col-span-2">
                                <InputLabel for="bio" value="Biografía" />
                                <textarea
                                    id="bio"
                                    v-model="form.bio"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    rows="3"
                                    maxlength="500"
                                ></textarea>
                                <p class="mt-1 text-sm text-gray-500">{{ form.bio?.length || 0 }}/500 caracteres</p>
                                <InputError class="mt-2" :message="form.errors.bio" />
                            </div>

                            <!-- Password Section -->
                            <div class="md:col-span-2 border-t pt-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Cambiar Contraseña (Opcional)</h3>
                                <p class="text-sm text-gray-600 mb-4">Deja estos campos vacíos si no deseas cambiar la contraseña.</p>
                            </div>

                            <!-- Password -->
                            <div>
                                <InputLabel for="password" value="Nueva Contraseña" />
                                <TextInput
                                    id="password"
                                    type="password"
                                    class="mt-1 block w-full"
                                    v-model="form.password"
                                    autocomplete="new-password"
                                />
                                <InputError class="mt-2" :message="form.errors.password" />
                            </div>

                            <!-- Password Confirmation -->
                            <div>
                                <InputLabel for="password_confirmation" value="Confirmar Contraseña" />
                                <TextInput
                                    id="password_confirmation"
                                    type="password"
                                    class="mt-1 block w-full"
                                    v-model="form.password_confirmation"
                                    autocomplete="new-password"
                                />
                                <InputError class="mt-2" :message="form.errors.password_confirmation" />
                            </div>

                            <!-- Settings Section -->
                            <div class="md:col-span-2 border-t pt-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Configuración</h3>
                            </div>

                            <!-- Status -->
                            <div class="md:col-span-2">
                                <div class="flex items-center">
                                    <input
                                        id="is_active"
                                        type="checkbox"
                                        v-model="form.is_active"
                                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                    />
                                    <InputLabel for="is_active" value="Usuario activo" class="ml-2" />
                                </div>
                                <p class="mt-1 text-sm text-gray-500">Los usuarios inactivos no pueden iniciar sesión en el sistema.</p>
                                <InputError class="mt-2" :message="form.errors.is_active" />
                            </div>

                            <!-- Force Password Change -->
                            <div class="md:col-span-2">
                                <div class="flex items-center">
                                    <input
                                        id="force_password_change"
                                        type="checkbox"
                                        v-model="form.force_password_change"
                                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                    />
                                    <InputLabel for="force_password_change" value="Forzar cambio de contraseña en el próximo login" class="ml-2" />
                                </div>
                                <InputError class="mt-2" :message="form.errors.force_password_change" />
                            </div>

                            <!-- Roles Section -->
                            <div class="md:col-span-2 border-t pt-6">
                                <h3 class="text-lg font-medium text-gray-900 mb-4">Roles y Permisos</h3>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                                    <div
                                        v-for="role in roles"
                                        :key="role.id"
                                        class="relative flex items-start p-4 border rounded-lg hover:bg-gray-50 transition-colors"
                                        :class="{ 'border-green-500 bg-green-50': form.roles.includes(role.id) }"
                                    >
                                        <div class="flex items-center h-5">
                                            <input
                                                :id="`role-${role.id}`"
                                                type="checkbox"
                                                :value="role.id"
                                                v-model="form.roles"
                                                class="focus:ring-green-500 h-4 w-4 text-green-600 border-gray-300 rounded"
                                            />
                                        </div>
                                        <div class="ml-3 text-sm">
                                            <label :for="`role-${role.id}`" class="font-medium text-gray-700 cursor-pointer">
                                                {{ role.name }}
                                            </label>
                                            <p class="text-gray-500 text-xs mt-1">{{ role.description || 'Sin descripción' }}</p>
                                            <div class="mt-2 flex flex-wrap gap-1">
                                                <span
                                                    v-for="permission in role.permissions?.slice(0, 3)"
                                                    :key="permission.id"
                                                    class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800"
                                                >
                                                    {{ permission.name }}
                                                </span>
                                                <span
                                                    v-if="role.permissions?.length > 3"
                                                    class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800"
                                                >
                                                    +{{ role.permissions.length - 3 }} más
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <InputError class="mt-2" :message="form.errors.roles" />
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="flex items-center justify-end mt-8 pt-6 border-t border-gray-200">
                            <Link
                                :href="route('users.index')"
                                class="inline-flex items-center px-4 py-2 bg-gray-300 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-400 transition ease-in-out duration-150 mr-3"
                            >
                                Cancelar
                            </Link>

                            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                <IconLoader2 v-if="form.processing" class="w-4 h-4 mr-2 animate-spin" />
                                <IconCheck v-else class="w-4 h-4 mr-2" />
                                {{ form.processing ? 'Actualizando...' : 'Actualizar Usuario' }}
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import InputError from '@/Components/InputError.vue'
import InputLabel from '@/Components/InputLabel.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import TextInput from '@/Components/TextInput.vue'
import { 
    IconArrowLeft, 
    IconCamera, 
    IconUser, 
    IconCheck, 
    IconLoader2
} from '@tabler/icons-vue'

const props = defineProps({
    user: {
        type: Object,
        required: true
    },
    roles: {
        type: Array,
        default: () => []
    }
})

const avatarPreview = ref(null)

const form = useForm({
    name: props.user.name || '',
    email: props.user.email || '',
    phone: props.user.phone || '',
    position: props.user.position || '',
    bio: props.user.bio || '',
    password: '',
    password_confirmation: '',
    avatar: null,
    is_active: props.user.is_active ?? true,
    force_password_change: props.user.force_password_change ?? false,
    roles: props.user.roles?.map(role => role.id) || [],
    settings: props.user.settings || {},
    metadata: props.user.metadata || {}
})

const handleAvatarChange = (event) => {
    const file = event.target.files[0]
    if (file) {
        form.avatar = file
        
        // Create preview
        const reader = new FileReader()
        reader.onload = (e) => {
            avatarPreview.value = e.target.result
        }
        reader.readAsDataURL(file)
    }
}

const submit = () => {
    // Remove password fields if they're empty
    const submitData = { ...form.data() }
    if (!submitData.password) {
        delete submitData.password
        delete submitData.password_confirmation
    }
    
    // Convert boolean values properly
    submitData.is_active = submitData.is_active ? 1 : 0
    submitData.force_password_change = submitData.force_password_change ? 1 : 0
    
    if (submitData.avatar) {
        // Use FormData for file upload
        const formPayload = new FormData()
        
        Object.keys(submitData).forEach(key => {
            if (key === 'roles') {
                submitData[key].forEach((roleId, index) => {
                    formPayload.append(`roles[${index}]`, roleId)
                })
            } else if (key === 'settings' || key === 'metadata') {
                formPayload.append(key, JSON.stringify(submitData[key]))
            } else if (submitData[key] !== null && submitData[key] !== undefined) {
                formPayload.append(key, submitData[key])
            }
        })
        
        formPayload.append('_method', 'PATCH')
        
        form.post(route('users.update', props.user.id), {
            data: formPayload,
            preserveScroll: true
        })
    } else {
        // Regular JSON payload when no file upload
        form.patch(route('users.update', props.user.id), {
            data: submitData,
            preserveScroll: true
        })
    }
}
</script>