<template>
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="p-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900">
                    Asignación de Roles
                </h3>
                <button
                    @click="toggleEdit"
                    class="text-sm text-indigo-600 hover:text-indigo-500"
                >
                    {{ isEditing ? 'Cancelar' : 'Editar' }}
                </button>
            </div>
        </div>
        
        <div class="p-4">
            <!-- View Mode -->
            <div v-if="!isEditing">
                <div v-if="userRoles.length > 0" class="space-y-3">
                    <div
                        v-for="role in userRoles"
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
                        <div class="flex items-center text-xs text-gray-400">
                            <IconCalendar class="w-3 h-3 mr-1" />
                            {{ formatDate(role.pivot?.assigned_at) }}
                        </div>
                    </div>
                </div>
                
                <div v-else class="text-center py-6">
                    <IconShieldX class="h-12 w-12 text-gray-300 mx-auto mb-3" />
                    <p class="text-gray-500">No tiene roles asignados</p>
                    <button
                        @click="toggleEdit"
                        class="mt-2 text-sm text-indigo-600 hover:text-indigo-500"
                    >
                        Asignar roles
                    </button>
                </div>
            </div>

            <!-- Edit Mode -->
            <div v-else>
                <form @submit.prevent="saveRoles" class="space-y-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div
                            v-for="role in availableRoles"
                            :key="role.id"
                            class="relative flex items-start"
                        >
                            <div class="flex items-center h-5">
                                <input
                                    :id="`role-${role.id}`"
                                    v-model="selectedRoles"
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

                    <!-- Actions -->
                    <div class="flex items-center justify-end gap-2 pt-4 border-t border-gray-200">
                        <button
                            type="button"
                            @click="toggleEdit"
                            class="px-3 py-1.5 text-sm text-gray-600 hover:text-gray-800"
                        >
                            Cancelar
                        </button>
                        <button
                            type="submit"
                            :disabled="isSaving"
                            class="px-4 py-1.5 bg-indigo-600 text-white text-sm rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 disabled:opacity-50"
                        >
                            <IconLoader v-if="isSaving" class="w-4 h-4 animate-spin inline mr-1" />
                            {{ isSaving ? 'Guardando...' : 'Guardar' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import { IconCalendar, IconShieldX, IconLoader } from '@tabler/icons-vue'

const props = defineProps({
    user: {
        type: Object,
        required: true,
    },
    availableRoles: {
        type: Array,
        required: true,
    },
})

const emit = defineEmits(['rolesUpdated'])

const isEditing = ref(false)
const isSaving = ref(false)
const selectedRoles = ref([])

const userRoles = computed(() => props.user.roles || [])

// Initialize selected roles when entering edit mode
watch(isEditing, (newValue) => {
    if (newValue) {
        selectedRoles.value = userRoles.value.map(role => role.id)
    }
})

const toggleEdit = () => {
    isEditing.value = !isEditing.value
}

const saveRoles = async () => {
    isSaving.value = true
    
    try {
        const response = await fetch(route('users.assign-roles', props.user.id), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            },
            body: JSON.stringify({
                roles: selectedRoles.value,
            }),
        })

        if (response.ok) {
            const data = await response.json()
            emit('rolesUpdated', data.user)
            isEditing.value = false
            
            // Show success message
            router.reload({ 
                only: ['user'],
                onSuccess: () => {
                    // You could add a toast notification here
                }
            })
        } else {
            const error = await response.json()
            console.error('Error saving roles:', error)
        }
    } catch (error) {
        console.error('Error saving roles:', error)
    } finally {
        isSaving.value = false
    }
}

const formatDate = (dateString) => {
    if (!dateString) return ''
    return new Date(dateString).toLocaleDateString('es-CO', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    })
}
</script>