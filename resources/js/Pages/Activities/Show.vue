<template>
  <Head :title="`Actividad: ${activity.subject}`" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
          <Link :href="route('activities.index')" class="text-gray-500 hover:text-gray-700">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
          </Link>
          <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
              {{ activity.subject }}
            </h2>
            <div class="flex items-center space-x-4 mt-1">
              <span :class="getTypeColor(activity.type)" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                {{ activity.formatted_type }}
              </span>
              <span :class="getStatusColor(activity.status)" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                {{ activity.formatted_status }}
              </span>
              <span :class="getPriorityColor(activity.priority)" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                {{ activity.formatted_priority }}
              </span>
            </div>
          </div>
        </div>
        <div class="flex items-center space-x-3">
          <button
            v-if="activity.status === 'pending'"
            @click="markCompleted"
            class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            Marcar Completada
          </button>
          <button
            v-if="activity.status === 'pending'"
            @click="showFollowUpModal = true"
            class="inline-flex items-center px-4 py-2 bg-yellow-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-yellow-700"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Programar Seguimiento
          </button>
          <Link
            :href="route('activities.edit', activity.id)"
            class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700"
          >
            Editar
          </Link>
        </div>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <!-- Activity Details -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <!-- Main Details -->
          <div class="lg:col-span-2 bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-6">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Detalles de la Actividad</h3>
              
              <div class="space-y-4">
                <!-- Description -->
                <div v-if="activity.description">
                  <dt class="text-sm font-medium text-gray-500">Descripción</dt>
                  <dd class="mt-1 text-sm text-gray-900 whitespace-pre-wrap">{{ activity.description }}</dd>
                </div>

                <!-- Basic Info Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <dt class="text-sm font-medium text-gray-500">Tipo</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ activity.formatted_type }}</dd>
                  </div>
                  <div>
                    <dt class="text-sm font-medium text-gray-500">Estado</dt>
                    <dd class="mt-1">
                      <span :class="getStatusColor(activity.status)" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                        {{ activity.formatted_status }}
                      </span>
                    </dd>
                  </div>
                  <div>
                    <dt class="text-sm font-medium text-gray-500">Prioridad</dt>
                    <dd class="mt-1">
                      <span :class="getPriorityColor(activity.priority)" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                        {{ activity.formatted_priority }}
                      </span>
                    </dd>
                  </div>
                  <div v-if="activity.duration">
                    <dt class="text-sm font-medium text-gray-500">Duración</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ activity.duration }} minutos</dd>
                  </div>
                </div>

                <!-- Scheduling Info -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div v-if="activity.scheduled_at">
                    <dt class="text-sm font-medium text-gray-500">Programada para</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                      {{ formatDate(activity.scheduled_at) }}
                      <div v-if="activity.is_overdue" class="text-xs text-red-600 mt-1">
                        <svg class="inline w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                        Actividad vencida
                      </div>
                    </dd>
                  </div>
                  <div v-if="activity.completed_at">
                    <dt class="text-sm font-medium text-gray-500">Completada el</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ formatDate(activity.completed_at) }}</dd>
                  </div>
                </div>

                <!-- People -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <dt class="text-sm font-medium text-gray-500">Creado por</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ activity.user?.name }}</dd>
                  </div>
                  <div>
                    <dt class="text-sm font-medium text-gray-500">Asignado a</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ activity.assigned_user?.name || activity.user?.name }}</dd>
                  </div>
                </div>

                <!-- Related Entity -->
                <div v-if="activity.related">
                  <dt class="text-sm font-medium text-gray-500">Relacionado con</dt>
                  <dd class="mt-1">
                    <div class="flex items-center space-x-2">
                      <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                        {{ getRelatedTypeName(activity.related_type) }}
                      </span>
                      <Link
                        v-if="getRelatedLink(activity.related_type, activity.related.id)"
                        :href="getRelatedLink(activity.related_type, activity.related.id)"
                        class="text-green-600 hover:text-green-900"
                      >
                        {{ getRelatedName(activity.related) }}
                      </Link>
                      <span v-else class="text-sm text-gray-900">
                        {{ getRelatedName(activity.related) }}
                      </span>
                    </div>
                  </dd>
                </div>

                <!-- Timestamps -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-4 border-t border-gray-200">
                  <div>
                    <dt class="text-sm font-medium text-gray-500">Creado el</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ formatDate(activity.created_at) }}</dd>
                  </div>
                  <div v-if="activity.updated_at !== activity.created_at">
                    <dt class="text-sm font-medium text-gray-500">Última actualización</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ formatDate(activity.updated_at) }}</dd>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Sidebar -->
          <div class="space-y-6">
            <!-- Reminder Info -->
            <div v-if="activity.has_reminder" class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
              <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Recordatorio</h3>
                <div class="space-y-3">
                  <div>
                    <dt class="text-sm font-medium text-gray-500">Programado para</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ formatDate(activity.reminder_at) }}</dd>
                  </div>
                  <div>
                    <dt class="text-sm font-medium text-gray-500">Estado</dt>
                    <dd class="mt-1">
                      <span :class="activity.reminder_sent ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800'" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                        {{ activity.reminder_sent ? 'Enviado' : 'Pendiente' }}
                      </span>
                    </dd>
                  </div>
                </div>
              </div>
            </div>

            <!-- Parent Activity -->
            <div v-if="activity.parent_activity" class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
              <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Actividad Padre</h3>
                <div class="space-y-3">
                  <div>
                    <Link
                      :href="route('activities.show', activity.parent_activity.id)"
                      class="text-green-600 hover:text-green-900 text-sm font-medium"
                    >
                      {{ activity.parent_activity.subject }}
                    </Link>
                    <p class="text-xs text-gray-500 mt-1">{{ activity.parent_activity.formatted_type }}</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Follow-up Activities -->
            <div v-if="activity.follow_up_activities && activity.follow_up_activities.length > 0" class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
              <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Seguimientos</h3>
                <div class="space-y-3">
                  <div v-for="followUp in activity.follow_up_activities" :key="followUp.id" class="flex items-start space-x-3">
                    <div class="flex-shrink-0">
                      <span :class="getStatusColor(followUp.status)" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium">
                        {{ followUp.formatted_status }}
                      </span>
                    </div>
                    <div class="flex-1 min-w-0">
                      <Link
                        :href="route('activities.show', followUp.id)"
                        class="text-green-600 hover:text-green-900 text-sm font-medium"
                      >
                        {{ followUp.subject }}
                      </Link>
                      <p class="text-xs text-gray-500 mt-1">
                        {{ followUp.user?.name }} • {{ formatDate(followUp.created_at) }}
                      </p>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
              <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Acciones Rápidas</h3>
                <div class="space-y-3">
                  <button
                    v-if="activity.status === 'pending'"
                    @click="markCompleted"
                    class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700"
                  >
                    Marcar como Completada
                  </button>
                  <button
                    v-if="activity.status === 'pending'"
                    @click="markCancelled"
                    class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                  >
                    Cancelar Actividad
                  </button>
                  <button
                    @click="showFollowUpModal = true"
                    class="w-full inline-flex justify-center items-center px-4 py-2 border border-green-300 text-sm font-medium rounded-md text-green-700 bg-green-50 hover:bg-green-100"
                  >
                    Programar Seguimiento
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Follow-up Modal -->
    <Modal :show="showFollowUpModal" @close="showFollowUpModal = false">
      <div class="p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Programar Seguimiento</h3>
        
        <form @submit.prevent="createFollowUp" class="space-y-4">
          <div>
            <label for="followup_type" class="block text-sm font-medium text-gray-700">Tipo</label>
            <select
              id="followup_type"
              v-model="followUpForm.type"
              class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-green-500 focus:border-green-500"
              required
            >
              <option v-for="(label, value) in types" :key="value" :value="value">
                {{ label }}
              </option>
            </select>
          </div>

          <div>
            <label for="followup_subject" class="block text-sm font-medium text-gray-700">Asunto</label>
            <input
              id="followup_subject"
              v-model="followUpForm.subject"
              type="text"
              class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-green-500 focus:border-green-500"
              required
            />
          </div>

          <div>
            <label for="followup_description" class="block text-sm font-medium text-gray-700">Descripción</label>
            <textarea
              id="followup_description"
              v-model="followUpForm.description"
              rows="3"
              class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-green-500 focus:border-green-500"
            />
          </div>

          <div>
            <label for="followup_scheduled_at" class="block text-sm font-medium text-gray-700">Fecha y Hora</label>
            <input
              id="followup_scheduled_at"
              v-model="followUpForm.scheduled_at"
              type="datetime-local"
              class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-green-500 focus:border-green-500"
              required
            />
          </div>

          <div>
            <label for="followup_priority" class="block text-sm font-medium text-gray-700">Prioridad</label>
            <select
              id="followup_priority"
              v-model="followUpForm.priority"
              class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-green-500 focus:border-green-500"
              required
            >
              <option v-for="(label, value) in priorities" :key="value" :value="value">
                {{ label }}
              </option>
            </select>
          </div>

          <div class="flex justify-end space-x-3 pt-4">
            <button
              type="button"
              @click="showFollowUpModal = false"
              class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50"
            >
              Cancelar
            </button>
            <button
              type="submit"
              :disabled="followUpForm.processing"
              class="px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md hover:bg-green-700 disabled:opacity-50"
            >
              Crear Seguimiento
            </button>
          </div>
        </form>
      </div>
    </Modal>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Head, Link, router, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Modal from '@/Components/Modal.vue'

const props = defineProps({
  activity: Object,
  types: Object,
  statuses: Object,
  priorities: Object
})

// State
const showFollowUpModal = ref(false)

// Follow-up form
const followUpForm = useForm({
  type: 'call',
  subject: '',
  description: '',
  scheduled_at: '',
  priority: 'medium'
})

// Methods
const getTypeColor = (type) => {
  const colors = {
    call: 'bg-green-100 text-green-800',
    email: 'bg-green-100 text-green-800',
    meeting: 'bg-purple-100 text-purple-800',
    note: 'bg-yellow-100 text-yellow-800',
    task: 'bg-gray-100 text-gray-800',
    sms: 'bg-indigo-100 text-indigo-800',
    whatsapp: 'bg-green-100 text-green-800',
    visit: 'bg-orange-100 text-orange-800'
  }
  return colors[type] || 'bg-gray-100 text-gray-800'
}

const getStatusColor = (status) => {
  const colors = {
    pending: 'bg-yellow-100 text-yellow-800',
    completed: 'bg-green-100 text-green-800',
    cancelled: 'bg-red-100 text-red-800'
  }
  return colors[status] || 'bg-gray-100 text-gray-800'
}

const getPriorityColor = (priority) => {
  const colors = {
    low: 'bg-green-100 text-green-800',
    medium: 'bg-yellow-100 text-yellow-800',
    high: 'bg-orange-100 text-orange-800',
    urgent: 'bg-red-100 text-red-800'
  }
  return colors[priority] || 'bg-gray-100 text-gray-800'
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

const getRelatedLink = (type, id) => {
  if (type?.includes('Lead')) return route('leads.show', id)
  if (type?.includes('Client')) return route('clients.show', id)
  if (type?.includes('Property')) return route('properties.show', id)
  return null
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

const markCompleted = () => {
  router.post(route('activities.complete', props.activity.id))
}

const markCancelled = () => {
  router.post(route('activities.cancel', props.activity.id))
}

const createFollowUp = () => {
  followUpForm.post(route('activities.follow-up', props.activity.id), {
    onSuccess: () => {
      showFollowUpModal.value = false
      followUpForm.reset()
    }
  })
}
</script>