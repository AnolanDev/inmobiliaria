<template>
  <Head :title="`Editar: ${activity.subject}`" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center space-x-4">
        <Link :href="route('activities.show', activity.id)" class="text-gray-500 hover:text-gray-700">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
          </svg>
        </Link>
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Editar: {{ activity.subject }}
        </h2>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <form @submit.prevent="submit" class="p-6 space-y-6">
            <!-- Basic Information -->
            <div class="border-b border-gray-200 pb-6">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Información Básica</h3>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Type -->
                <div>
                  <label for="type" class="block text-sm font-medium text-gray-700 mb-1">
                    Tipo de Actividad *
                  </label>
                  <select
                    id="type"
                    v-model="form.type"
                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-green-500 focus:border-green-500"
                    :class="{ 'border-red-500': form.errors.type }"
                    required
                  >
                    <option v-for="(label, value) in types" :key="value" :value="value">
                      {{ label }}
                    </option>
                  </select>
                  <p v-if="form.errors.type" class="mt-1 text-sm text-red-600">
                    {{ form.errors.type }}
                  </p>
                </div>

                <!-- Priority -->
                <div>
                  <label for="priority" class="block text-sm font-medium text-gray-700 mb-1">
                    Prioridad *
                  </label>
                  <select
                    id="priority"
                    v-model="form.priority"
                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-green-500 focus:border-green-500"
                    :class="{ 'border-red-500': form.errors.priority }"
                    required
                  >
                    <option v-for="(label, value) in priorities" :key="value" :value="value">
                      {{ label }}
                    </option>
                  </select>
                  <p v-if="form.errors.priority" class="mt-1 text-sm text-red-600">
                    {{ form.errors.priority }}
                  </p>
                </div>
              </div>

              <!-- Subject -->
              <div class="mt-6">
                <label for="subject" class="block text-sm font-medium text-gray-700 mb-1">
                  Asunto *
                </label>
                <input
                  id="subject"
                  v-model="form.subject"
                  type="text"
                  class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-green-500 focus:border-green-500"
                  :class="{ 'border-red-500': form.errors.subject }"
                  placeholder="Describe brevemente la actividad"
                  required
                />
                <p v-if="form.errors.subject" class="mt-1 text-sm text-red-600">
                  {{ form.errors.subject }}
                </p>
              </div>

              <!-- Description -->
              <div class="mt-6">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                  Descripción
                </label>
                <textarea
                  id="description"
                  v-model="form.description"
                  rows="3"
                  class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-green-500 focus:border-green-500"
                  :class="{ 'border-red-500': form.errors.description }"
                  placeholder="Detalles adicionales de la actividad..."
                />
                <p v-if="form.errors.description" class="mt-1 text-sm text-red-600">
                  {{ form.errors.description }}
                </p>
              </div>
            </div>

            <!-- Scheduling -->
            <div class="border-b border-gray-200 pb-6">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Programación</h3>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Status -->
                <div>
                  <label for="status" class="block text-sm font-medium text-gray-700 mb-1">
                    Estado *
                  </label>
                  <select
                    id="status"
                    v-model="form.status"
                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-green-500 focus:border-green-500"
                    :class="{ 'border-red-500': form.errors.status }"
                    required
                  >
                    <option v-for="(label, value) in statuses" :key="value" :value="value">
                      {{ label }}
                    </option>
                  </select>
                  <p v-if="form.errors.status" class="mt-1 text-sm text-red-600">
                    {{ form.errors.status }}
                  </p>
                </div>

                <!-- Duration -->
                <div>
                  <label for="duration" class="block text-sm font-medium text-gray-700 mb-1">
                    Duración (minutos)
                  </label>
                  <input
                    id="duration"
                    v-model="form.duration"
                    type="number"
                    min="1"
                    step="1"
                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-green-500 focus:border-green-500"
                    :class="{ 'border-red-500': form.errors.duration }"
                    placeholder="30"
                  />
                  <p v-if="form.errors.duration" class="mt-1 text-sm text-red-600">
                    {{ form.errors.duration }}
                  </p>
                </div>

                <!-- Scheduled At -->
                <div>
                  <label for="scheduled_at" class="block text-sm font-medium text-gray-700 mb-1">
                    Fecha y Hora Programada
                  </label>
                  <input
                    id="scheduled_at"
                    v-model="form.scheduled_at"
                    type="datetime-local"
                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-green-500 focus:border-green-500"
                    :class="{ 'border-red-500': form.errors.scheduled_at }"
                  />
                  <p v-if="form.errors.scheduled_at" class="mt-1 text-sm text-red-600">
                    {{ form.errors.scheduled_at }}
                  </p>
                </div>

                <!-- Assigned To -->
                <div>
                  <label for="assigned_to" class="block text-sm font-medium text-gray-700 mb-1">
                    Asignado a
                  </label>
                  <select
                    id="assigned_to"
                    v-model="form.assigned_to"
                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-green-500 focus:border-green-500"
                    :class="{ 'border-red-500': form.errors.assigned_to }"
                  >
                    <option v-for="user in users" :key="user.id" :value="user.id">
                      {{ user.name }}
                    </option>
                  </select>
                  <p v-if="form.errors.assigned_to" class="mt-1 text-sm text-red-600">
                    {{ form.errors.assigned_to }}
                  </p>
                </div>
              </div>
            </div>

            <!-- Reminders -->
            <div class="border-b border-gray-200 pb-6">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Recordatorio</h3>
              
              <div class="flex items-center space-x-3 mb-4">
                <input
                  id="has_reminder"
                  v-model="form.has_reminder"
                  type="checkbox"
                  class="rounded border-gray-300 text-green-600 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50"
                />
                <label for="has_reminder" class="text-sm font-medium text-gray-700">
                  Configurar recordatorio
                </label>
              </div>

              <div v-if="form.has_reminder" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label for="reminder_at" class="block text-sm font-medium text-gray-700 mb-1">
                    Fecha y Hora del Recordatorio
                  </label>
                  <input
                    id="reminder_at"
                    v-model="form.reminder_at"
                    type="datetime-local"
                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-green-500 focus:border-green-500"
                    :class="{ 'border-red-500': form.errors.reminder_at }"
                  />
                  <p v-if="form.errors.reminder_at" class="mt-1 text-sm text-red-600">
                    {{ form.errors.reminder_at }}
                  </p>
                </div>
                <div v-if="activity.reminder_sent" class="flex items-center">
                  <div class="p-4 bg-green-50 rounded-lg">
                    <div class="flex items-center">
                      <svg class="h-5 w-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                      </svg>
                      <p class="ml-2 text-sm text-green-700">Recordatorio enviado</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Related Entity (Read-only for editing) -->
            <div v-if="activity.related" class="pb-6">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Relacionado con</h3>
              
              <div class="p-4 bg-gray-50 rounded-lg">
                <div class="flex items-center space-x-2">
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                    {{ getRelatedTypeName(activity.related_type) }}
                  </span>
                  <span class="text-sm text-gray-900">{{ getRelatedName(activity.related) }}</span>
                </div>
                <p class="text-xs text-gray-500 mt-2">
                  La relación con esta entidad no se puede modificar desde aquí
                </p>
              </div>
            </div>

            <!-- Activity History (if completed) -->
            <div v-if="activity.status === 'completed'" class="pb-6">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Historial</h3>
              
              <div class="bg-green-50 rounded-lg p-4">
                <div class="flex items-center">
                  <svg class="h-5 w-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                  </svg>
                  <div class="ml-3">
                    <p class="text-sm text-green-700">
                      Actividad completada el {{ formatDate(activity.completed_at) }}
                    </p>
                    <p class="text-xs text-green-600 mt-1">
                      Por: {{ activity.assigned_user?.name || activity.user?.name }}
                    </p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
              <Link
                :href="route('activities.show', activity.id)"
                class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
              >
                Cancelar
              </Link>
              <button
                type="submit"
                :disabled="form.processing"
                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 disabled:opacity-50"
              >
                {{ form.processing ? 'Guardando...' : 'Guardar Cambios' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { watch } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
  activity: Object,
  types: Object,
  statuses: Object,
  priorities: Object,
  users: Array,
  leads: Array
})

// Form data
const form = useForm({
  type: props.activity.type,
  subject: props.activity.subject,
  description: props.activity.description,
  status: props.activity.status,
  priority: props.activity.priority,
  scheduled_at: props.activity.scheduled_at ? formatDateTimeForInput(props.activity.scheduled_at) : '',
  duration: props.activity.duration,
  assigned_to: props.activity.assigned_to,
  has_reminder: props.activity.has_reminder,
  reminder_at: props.activity.reminder_at ? formatDateTimeForInput(props.activity.reminder_at) : '',
  metadata: props.activity.metadata || {}
})

// Helper function to format datetime for input
function formatDateTimeForInput(dateString) {
  if (!dateString) return ''
  const date = new Date(dateString)
  const year = date.getFullYear()
  const month = String(date.getMonth() + 1).padStart(2, '0')
  const day = String(date.getDate()).padStart(2, '0')
  const hours = String(date.getHours()).padStart(2, '0')
  const minutes = String(date.getMinutes()).padStart(2, '0')
  return `${year}-${month}-${day}T${hours}:${minutes}`
}

const getRelatedTypeName = (type) => {
  if (type?.includes('Lead')) return 'Lead'
  if (type?.includes('Client')) return 'Cliente'
  if (type?.includes('Property')) return 'Propiedad'
  return 'Otro'
}

const getRelatedName = (related) => {
  if (related?.full_name) return related.full_name
  if (related?.first_name && related?.last_name) return `${related.first_name} ${related.last_name}`
  if (related?.name) return related.name
  if (related?.title) return related.title
  return 'Sin nombre'
}

const formatDate = (date) => {
  return new Intl.DateTimeFormat('es-CO', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  }).format(new Date(date))
}

// Watch for reminder checkbox changes
watch(() => form.has_reminder, (newValue) => {
  if (!newValue) {
    form.reminder_at = ''
  }
})

// Submit form
const submit = () => {
  form.patch(route('activities.update', props.activity.id))
}
</script>