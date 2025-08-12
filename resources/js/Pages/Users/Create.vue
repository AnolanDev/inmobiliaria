<template>
    <Head title="Crear Usuario" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Crear Usuario
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
                                            ref="avatarInput"
                                            type="file"
                                            accept="image/*"
                                            @change="handleAvatarChange"
                                            class="sr-only"
                                        />
                                        <button
                                            type="button"
                                            @click="$refs.avatarInput.click()"
                                            class="bg-white py-2 px-3 border border-gray-300 rounded-md shadow-sm text-sm leading-4 font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                                        >
                                            Seleccionar imagen
                                        </button>
                                        <p class="mt-1 text-sm text-gray-500">JPG, PNG hasta 2MB</p>
                                    </div>
                                </div>
                                <InputError class="mt-2" :message="form.errors.avatar" />
                            </div>

                            <!-- Name -->
                            <div>
                                <InputLabel for="name" value="Nombre completo" />
                                <TextInput
                                    id="name"
                                    v-model="form.name"
                                    type="text"
                                    class="mt-1 block w-full"
                                    required
                                />
                                <InputError class="mt-2" :message="form.errors.name" />
                            </div>

                            <!-- Email -->
                            <div>
                                <InputLabel for="email" value="Correo electrónico" />
                                <TextInput
                                    id="email"
                                    v-model="form.email"
                                    type="email"
                                    class="mt-1 block w-full"
                                    required
                                />
                                <InputError class="mt-2" :message="form.errors.email" />
                            </div>

                            <!-- Phone -->
                            <div>
                                <InputLabel for="phone" value="Teléfono" />
                                <TextInput
                                    id="phone"
                                    v-model="form.phone"
                                    type="text"
                                    class="mt-1 block w-full"
                                    placeholder="+57 300 123 4567"
                                />
                                <InputError class="mt-2" :message="form.errors.phone" />
                            </div>

                            <!-- Position -->
                            <div>
                                <InputLabel for="position" value="Cargo/Posición" />
                                <TextInput
                                    id="position"
                                    v-model="form.position"
                                    type="text"
                                    class="mt-1 block w-full"
                                    placeholder="Agente de ventas"
                                />
                                <InputError class="mt-2" :message="form.errors.position" />
                            </div>

                            <!-- Bio -->
                            <div class="md:col-span-2">
                                <InputLabel for="bio" value="Biografía" />
                                <textarea
                                    id="bio"
                                    v-model="form.bio"
                                    rows="3"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                    placeholder="Breve descripción profesional..."
                                ></textarea>
                                <InputError class="mt-2" :message="form.errors.bio" />
                            </div>

                            <!-- Account Settings -->
                            <div class="md:col-span-2">
                                <h3 class="text-lg font-medium text-gray-900 mb-4 mt-8 border-t pt-6">Configuración de Cuenta</h3>
                            </div>

                            <!-- Password -->
                            <div>
                                <InputLabel for="password" value="Contraseña" />
                                <TextInput
                                    id="password"
                                    v-model="form.password"
                                    type="password"
                                    class="mt-1 block w-full"
                                    required
                                />
                                <InputError class="mt-2" :message="form.errors.password" />
                            </div>

                            <!-- Confirm Password -->
                            <div>
                                <InputLabel for="password_confirmation" value="Confirmar contraseña" />
                                <TextInput
                                    id="password_confirmation"
                                    v-model="form.password_confirmation"
                                    type="password"
                                    class="mt-1 block w-full"
                                    required
                                />
                                <InputError class="mt-2" :message="form.errors.password_confirmation" />
                            </div>

                            <!-- Status -->
                            <div class="flex items-center">
                                <input
                                    id="is_active"
                                    v-model="form.is_active"
                                    type="checkbox"
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                />
                                <label for="is_active" class="ml-2 text-sm text-gray-700">
                                    Usuario activo
                                </label>
                            </div>

                            <!-- Force Password Change -->
                            <div class="flex items-center">
                                <input
                                    id="force_password_change"
                                    v-model="form.force_password_change"
                                    type="checkbox"
                                    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                />
                                <label for="force_password_change" class="ml-2 text-sm text-gray-700">
                                    Forzar cambio de contraseña en el primer login
                                </label>
                            </div>

                            <!-- Roles -->
                            <div class="md:col-span-2">
                                <InputLabel value="Roles" />
                                <div class="mt-2 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                    <div
                                        v-for="role in roles"
                                        :key="role.id"
                                        class="relative flex items-start"
                                    >
                                        <div class="flex items-center h-5">
                                            <input
                                                :id="`role-${role.id}`"
                                                v-model="form.roles"
                                                :value="role.id"
                                                type="checkbox"
                                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                            />
                                        </div>
                                        <div class="ml-3 text-sm">
                                            <label :for="`role-${role.id}`" class="font-medium text-gray-700">
                                                <span
                                                    class="inline-flex items-center px-2 py-1 rounded text-xs font-medium mr-2"
                                                    :style="`background-color: ${role.color}20; color: ${role.color}`"
                                                >
                                                    {{ role.name }}
                                                </span>
                                            </label>
                                            <p class="text-gray-500 text-xs">{{ role.description }}</p>
                                        </div>
                                    </div>
                                </div>
                                <InputError class="mt-2" :message="form.errors.roles" />
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="mt-8 border-t pt-6 flex items-center justify-end gap-3">
                            <Link
                                :href="route('users.index')"
                                class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                            >
                                Cancelar
                            </Link>
                            <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                                <IconUserPlus class="w-4 h-4 mr-2" />
                                Crear Usuario
                            </PrimaryButton>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import InputError from '@/Components/InputError.vue'
import InputLabel from '@/Components/InputLabel.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'
import TextInput from '@/Components/TextInput.vue'
import { IconArrowLeft, IconUser, IconUserPlus } from '@tabler/icons-vue'

const props = defineProps({
    roles: Array,
})

const avatarPreview = ref(null)

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    phone: '',
    position: '',
    bio: '',
    avatar: null,
    is_active: true,
    force_password_change: false,
    roles: [],
})

const handleAvatarChange = (event) => {
    const file = event.target.files[0]
    if (file) {
        form.avatar = file
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
        
        form.post(route('users.store'), {
            data: formPayload,
            onSuccess: () => console.log('Create successful!'),
            onError: (errors) => console.error('Create validation errors:', errors),
            preserveScroll: true
        })
    } else {
        // Regular JSON payload when no file upload
        form.post(route('users.store'), {
            data: submitData,
            onSuccess: () => console.log('Create successful!'),
            onError: (errors) => console.error('Create validation errors:', errors),
            preserveScroll: true
        })
    }
}
</script>