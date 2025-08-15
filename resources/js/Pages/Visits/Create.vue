<template>
  <AuthenticatedLayout title="Nueva Visita">
    <div class="py-12">
      <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
          <div class="flex items-center justify-between">
            <div>
              <h1 class="text-3xl font-bold text-gray-900">Nueva Visita</h1>
              <p class="text-gray-600 mt-2">Programa una nueva visita inmobiliaria</p>
            </div>
            <Link
              :href="route('visits.index')"
              class="text-gray-600 hover:text-gray-900 flex items-center gap-2"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
              </svg>
              Volver a Visitas
            </Link>
          </div>
        </div>

        <!-- Form -->
        <div class="bg-white shadow-sm rounded-lg border border-gray-200">
          <form @submit.prevent="submit" class="p-6 space-y-6">
            <!-- Basic Information -->
            <div>
              <h3 class="text-lg font-medium text-gray-900 mb-4">Información Básica</h3>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Visit Subject Type -->
                <div class="md:col-span-2">
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    Tipo de Destino *
                  </label>
                  <div class="flex gap-4">
                    <label class="flex items-center">
                      <input
                        v-model="visitSubjectType"
                        type="radio"
                        value="property"
                        class="rounded border-gray-300 text-green-600 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50"
                      />
                      <span class="ml-2 text-sm text-gray-700">Propiedad Individual</span>
                    </label>
                    <label class="flex items-center">
                      <input
                        v-model="visitSubjectType"
                        type="radio"
                        value="project"
                        class="rounded border-gray-300 text-green-600 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50"
                      />
                      <span class="ml-2 text-sm text-gray-700">Proyecto</span>
                    </label>
                  </div>
                </div>

                <!-- Property Selection -->
                <div v-if="visitSubjectType === 'property'">
                  <label class="block text-sm font-medium text-gray-700 mb-1">
                    Propiedad *
                  </label>
                  <select
                    v-model="form.property_id"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    :class="{ 'border-red-500': errors.property_id }"
                  >
                    <option value="">Seleccionar propiedad</option>
                    <option v-for="property in properties" :key="property.id" :value="property.id">
                      {{ property.title }} - {{ property.address }}
                    </option>
                  </select>
                  <p v-if="errors.property_id" class="text-red-500 text-sm mt-1">{{ errors.property_id }}</p>
                </div>

                <!-- Project Selection -->
                <div v-if="visitSubjectType === 'project'">
                  <label class="block text-sm font-medium text-gray-700 mb-1">
                    Proyecto *
                  </label>
                  <select
                    v-model="form.project_id"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    :class="{ 'border-red-500': errors.project_id }"
                  >
                    <option value="">Seleccionar proyecto</option>
                    <option v-for="project in projects" :key="project.id" :value="project.id">
                      {{ project.name }}
                    </option>
                  </select>
                  <p v-if="errors.project_id" class="text-red-500 text-sm mt-1">{{ errors.project_id }}</p>
                </div>

                <!-- Client Selection -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">
                    Cliente *
                  </label>
                  <select
                    v-model="form.client_id"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    :class="{ 'border-red-500': errors.client_id }"
                    required
                  >
                    <option value="">Seleccionar cliente</option>
                    <option v-for="client in clients" :key="client.id" :value="client.id">
                      {{ client.name }}
                    </option>
                  </select>
                  <p v-if="errors.client_id" class="text-red-500 text-sm mt-1">{{ errors.client_id }}</p>
                </div>

                <!-- Agent Selection -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">
                    Agente *
                  </label>
                  <select
                    v-model="form.agent_id"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    :class="{ 'border-red-500': errors.agent_id }"
                    required
                  >
                    <option value="">Seleccionar agente</option>
                    <option v-for="agent in agents" :key="agent.id" :value="agent.id">
                      {{ agent.name }}
                    </option>
                  </select>
                  <p v-if="errors.agent_id" class="text-red-500 text-sm mt-1">{{ errors.agent_id }}</p>
                </div>

                <!-- Visit Type -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">
                    Tipo de Visita *
                  </label>
                  <select
                    v-model="form.type"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    :class="{ 'border-red-500': errors.type }"
                    required
                  >
                    <option value="showing">Visita</option>
                    <option value="inspection">Inspección</option>
                    <option value="evaluation">Evaluación</option>
                    <option value="follow_up">Seguimiento</option>
                    <option value="closing">Cierre</option>
                  </select>
                  <p v-if="errors.type" class="text-red-500 text-sm mt-1">{{ errors.type }}</p>
                </div>
              </div>
            </div>

            <!-- Schedule Information -->
            <div>
              <h3 class="text-lg font-medium text-gray-900 mb-4">Programación</h3>
              <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Scheduled Date & Time -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">
                    Fecha y Hora *
                  </label>
                  <input
                    v-model="form.scheduled_at"
                    type="datetime-local"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    :class="{ 'border-red-500': errors.scheduled_at }"
                    :min="minDateTime"
                    required
                  />
                  <p v-if="errors.scheduled_at" class="text-red-500 text-sm mt-1">{{ errors.scheduled_at }}</p>
                </div>

                <!-- Estimated Duration -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">
                    Duración Estimada (minutos)
                  </label>
                  <input
                    v-model.number="form.estimated_duration"
                    type="number"
                    min="15"
                    max="480"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    :class="{ 'border-red-500': errors.estimated_duration }"
                  />
                  <p v-if="errors.estimated_duration" class="text-red-500 text-sm mt-1">{{ errors.estimated_duration }}</p>
                </div>

                <!-- Priority -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">
                    Prioridad *
                  </label>
                  <select
                    v-model="form.priority"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    :class="{ 'border-red-500': errors.priority }"
                    required
                  >
                    <option value="low">Baja</option>
                    <option value="medium">Media</option>
                    <option value="high">Alta</option>
                    <option value="urgent">Urgente</option>
                  </select>
                  <p v-if="errors.priority" class="text-red-500 text-sm mt-1">{{ errors.priority }}</p>
                </div>
              </div>
            </div>

            <!-- Contact Information -->
            <div>
              <h3 class="text-lg font-medium text-gray-900 mb-4">Información de Contacto</h3>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Client Phone -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">
                    Teléfono del Cliente
                  </label>
                  <input
                    v-model="form.client_phone"
                    type="tel"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    :class="{ 'border-red-500': errors.client_phone }"
                  />
                  <p v-if="errors.client_phone" class="text-red-500 text-sm mt-1">{{ errors.client_phone }}</p>
                </div>

                <!-- Client Email -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">
                    Email del Cliente
                  </label>
                  <input
                    v-model="form.client_email"
                    type="email"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    :class="{ 'border-red-500': errors.client_email }"
                  />
                  <p v-if="errors.client_email" class="text-red-500 text-sm mt-1">{{ errors.client_email }}</p>
                </div>
              </div>
            </div>

            <!-- Reminder Settings -->
            <div>
              <h3 class="text-lg font-medium text-gray-900 mb-4">Recordatorio</h3>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Reminder Hours Before -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">
                    Enviar recordatorio (horas antes)
                  </label>
                  <select
                    v-model.number="form.reminder_hours_before"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    :class="{ 'border-red-500': errors.reminder_hours_before }"
                  >
                    <option value="1">1 hora</option>
                    <option value="2">2 horas</option>
                    <option value="4">4 horas</option>
                    <option value="8">8 horas</option>
                    <option value="24">1 día</option>
                    <option value="48">2 días</option>
                    <option value="72">3 días</option>
                    <option value="168">1 semana</option>
                  </select>
                  <p v-if="errors.reminder_hours_before" class="text-red-500 text-sm mt-1">{{ errors.reminder_hours_before }}</p>
                </div>
              </div>
            </div>

            <!-- Additional Participants -->
            <div>
              <h3 class="text-lg font-medium text-gray-900 mb-4">Participantes Adicionales</h3>
              <div v-for="(participant, index) in form.additional_participants" :key="index" class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4 p-4 border border-gray-200 rounded-lg">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                  <input
                    v-model="participant.name"
                    type="text"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                  />
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                  <input
                    v-model="participant.phone"
                    type="tel"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                  />
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Rol</label>
                  <input
                    v-model="participant.role"
                    type="text"
                    placeholder="Ej: Cónyuge, Asesor"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                  />
                </div>
                <div class="flex items-end">
                  <button
                    type="button"
                    @click="removeParticipant(index)"
                    class="text-red-600 hover:text-red-800 p-2"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                  </button>
                </div>
              </div>
              
              <button
                type="button"
                @click="addParticipant"
                class="flex items-center gap-2 text-green-600 hover:text-green-800 text-sm font-medium"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Agregar Participante
              </button>
            </div>

            <!-- Notes -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                Notas
              </label>
              <textarea
                v-model="form.notes"
                rows="4"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                :class="{ 'border-red-500': errors.notes }"
                placeholder="Notas adicionales sobre la visita..."
              ></textarea>
              <p v-if="errors.notes" class="text-red-500 text-sm mt-1">{{ errors.notes }}</p>
            </div>

            <!-- File Attachments -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                Archivos Adjuntos
              </label>
              <input
                type="file"
                multiple
                @change="handleFileUpload"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.gif"
              />
              <p class="text-xs text-gray-500 mt-1">
                Máximo 10 archivos, 10MB cada uno. Formatos: PDF, DOC, DOCX, JPG, JPEG, PNG, GIF
              </p>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end gap-4 pt-6 border-t border-gray-200">
              <Link
                :href="route('visits.index')"
                class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 font-medium"
              >
                Cancelar
              </Link>
              <button
                type="submit"
                :disabled="processing"
                class="px-6 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 font-medium disabled:opacity-50 flex items-center gap-2"
              >
                <svg v-if="processing" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                {{ processing ? 'Guardando...' : 'Crear Visita' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, reactive, computed, onMounted, watch } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
  properties: {
    type: Array,
    required: true
  },
  projects: {
    type: Array,
    required: true
  },
  clients: {
    type: Array,
    required: true
  },
  agents: {
    type: Array,
    required: true
  },
  preselected: {
    type: Object,
    default: () => ({})
  }
})

// Determine initial visit subject type
const visitSubjectType = ref(
  props.preselected.property_id ? 'property' : 
  props.preselected.project_id ? 'project' : 'property'
)

const form = useForm({
  property_id: props.preselected.property_id || '',
  project_id: props.preselected.project_id || '',
  client_id: props.preselected.client_id || '',
  agent_id: props.preselected.agent_id || '',
  type: 'showing',
  priority: 'medium',
  scheduled_at: '',
  estimated_duration: 60,
  client_phone: '',
  client_email: '',
  reminder_hours_before: 24,
  additional_participants: [],
  notes: '',
  attachments: []
})

const { errors, processing } = form

const minDateTime = computed(() => {
  const today = new Date()
  today.setHours(0, 0, 0, 0) // 00:00:00 del día actual
  today.setMinutes(today.getMinutes() - today.getTimezoneOffset())
  return today.toISOString().slice(0, 16)
})

const addParticipant = () => {
  form.additional_participants.push({
    name: '',
    phone: '',
    role: ''
  })
}

const removeParticipant = (index) => {
  form.additional_participants.splice(index, 1)
}

const handleFileUpload = (event) => {
  const files = Array.from(event.target.files)
  form.attachments = files
}

const submit = () => {
  form.post(route('visits.store'), {
    onSuccess: () => {
      // Success handled by redirect
    },
    onError: (errors) => {
      console.log('Validation errors:', errors)
    }
  })
}

// Watch for visit subject type changes
watch(visitSubjectType, (newType) => {
  if (newType === 'property') {
    form.project_id = ''
  } else if (newType === 'project') {
    form.property_id = ''
  }
})

onMounted(() => {
  // Auto-populate client contact info when client is selected
  const selectedClient = computed(() => {
    if (!form.client_id) return null
    return props.clients.find(client => client.id === form.client_id)
  })

  // Watch for client changes and populate contact info
  const unwatchClient = watch(() => form.client_id, (newClientId) => {
    const client = props.clients.find(c => c.id === newClientId)
    if (client) {
      form.client_phone = client.phone || ''
      form.client_email = client.email || ''
    }
  })
})
</script>