<template>
  <Head title="Nueva Actividad" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center space-x-4">
        <Link :href="route('activities.index')" class="text-gray-500 hover:text-gray-700">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
          </svg>
        </Link>
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Nueva Actividad
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
                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                    :class="{ 'border-red-500': form.errors.type }"
                    required
                  >
                    <option value="">Seleccionar tipo</option>
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
                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
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
                  class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
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
                  class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
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
                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
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
                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
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
                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
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
                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
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
                  class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
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
                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                    :class="{ 'border-red-500': form.errors.reminder_at }"
                  />
                  <p v-if="form.errors.reminder_at" class="mt-1 text-sm text-red-600">
                    {{ form.errors.reminder_at }}
                  </p>
                </div>
              </div>
            </div>

            <!-- Related Entity -->
            <div class="pb-6">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Relacionado con</h3>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Entity Type -->
                <div>
                  <label for="related_type" class="block text-sm font-medium text-gray-700 mb-1">
                    Tipo de Entidad
                  </label>
                  <select
                    id="related_type"
                    v-model="selectedRelatedType"
                    @change="onRelatedTypeChange"
                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                  >
                    <option value="">Seleccionar tipo</option>
                    <option value="lead">Lead</option>
                    <option value="client">Cliente</option>
                    <option value="property">Propiedad</option>
                  </select>
                </div>

                <!-- Entity Selection -->
                <div v-if="selectedRelatedType">
                  <label for="related_id" class="block text-sm font-medium text-gray-700 mb-1">
                    {{ getRelatedTypeLabel() }}
                  </label>
                  <select
                    id="related_id"
                    v-model="form.related_id"
                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                    :class="{ 'border-red-500': form.errors.related_id }"
                  >
                    <option value="">Seleccionar {{ getRelatedTypeLabel().toLowerCase() }}</option>
                    <option v-for="item in getRelatedOptions()" :key="item.id" :value="item.id">
                      {{ item.name }}
                    </option>
                  </select>
                  <p v-if="form.errors.related_id" class="mt-1 text-sm text-red-600">
                    {{ form.errors.related_id }}
                  </p>
                </div>
              </div>

              <!-- Pre-selected Lead (if coming from lead view) -->
              <div v-if="relatedData && relatedData.type.includes('Lead')" class="mt-4 p-4 bg-blue-50 rounded-lg">
                <div class="flex items-center">
                  <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                  <div class="ml-3">
                    <p class="text-sm text-blue-700">
                      Esta actividad será asociada con el lead: <strong>{{ relatedData.name }}</strong>
                    </p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
              <Link
                :href="route('activities.index')"
                class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
              >
                Cancelar
              </Link>
              <button
                type="submit"
                :disabled="form.processing"
                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 disabled:opacity-50"
              >
                <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                {{ form.processing ? 'Creando...' : 'Crear Actividad' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, watch } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
  types: Object,
  statuses: Object,
  priorities: Object,
  users: Array,
  leads: Array,
  relatedData: Object
})

// Form data
const form = useForm({
  type: '',
  subject: '',
  description: '',
  status: 'pending',
  priority: 'medium',
  scheduled_at: '',
  duration: '',
  assigned_to: '',
  has_reminder: false,
  reminder_at: '',
  related_type: '',
  related_id: '',
  metadata: {}
})

// Related entity state
const selectedRelatedType = ref('')

// Set up pre-selected data if available
if (props.relatedData) {
  form.related_type = props.relatedData.type
  form.related_id = props.relatedData.id
  selectedRelatedType.value = 'lead'
}

// Methods
const onRelatedTypeChange = () => {
  form.related_id = ''
  
  // Set the Laravel model class name
  const typeMap = {
    lead: 'App\\Models\\Lead',
    client: 'App\\Models\\Client',
    property: 'App\\Models\\Property'
  }
  form.related_type = typeMap[selectedRelatedType.value] || ''
}

const getRelatedTypeLabel = () => {
  const labels = {
    lead: 'Lead',
    client: 'Cliente',
    property: 'Propiedad'
  }
  return labels[selectedRelatedType.value] || 'Entidad'
}

const getRelatedOptions = () => {
  if (selectedRelatedType.value === 'lead') {
    return props.leads || []
  }
  // TODO: Add clients and properties when available
  return []
}

// Watch for reminder checkbox changes
watch(() => form.has_reminder, (newValue) => {
  if (!newValue) {
    form.reminder_at = ''
  }
})

// Submit form
const submit = () => {
  form.post(route('activities.store'))
}
</script>