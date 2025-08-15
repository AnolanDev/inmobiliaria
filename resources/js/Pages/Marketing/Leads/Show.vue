<template>
  <Head :title="`Lead: ${lead.full_name}`" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
          <Link :href="route('leads.index')" class="text-gray-500 hover:text-gray-700">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
          </Link>
          <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
              {{ lead.full_name }}
            </h2>
            <p class="text-sm text-gray-600">{{ lead.formatted_status }} • {{ lead.formatted_source }}</p>
          </div>
        </div>
        <div class="flex items-center space-x-3">
          <button
            v-if="lead.status !== 'converted' && can && can['leads.convert']"
            @click="showConvertModal = true"
            class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700"
          >
            Convertir a Cliente
          </button>
          <Link
            :href="route('leads.edit', lead.id)"
            class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700"
          >
            Editar
          </Link>
        </div>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <!-- Lead Info & Contact Details ---->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <!-- Lead Details -->
          <div class="lg:col-span-2 bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-6">
              <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">Información del Lead</h3>
                <span :class="getPriorityColor(lead.priority)" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                  {{ lead.formatted_priority }}
                </span>
              </div>
              
              <div class="space-y-4">
                <!-- Contact Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <dt class="text-sm font-medium text-gray-500">Nombre Completo</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ lead.full_name }}</dd>
                  </div>
                  <div>
                    <dt class="text-sm font-medium text-gray-500">Email</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                      <a :href="`mailto:${lead.email}`" class="text-green-600 hover:text-green-900">
                        {{ lead.email }}
                      </a>
                    </dd>
                  </div>
                  <div v-if="lead.phone">
                    <dt class="text-sm font-medium text-gray-500">Teléfono</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                      <a :href="`tel:${lead.phone}`" class="text-green-600 hover:text-green-900">
                        {{ lead.phone }}
                      </a>
                    </dd>
                  </div>
                  <div>
                    <dt class="text-sm font-medium text-gray-500">Estado</dt>
                    <dd class="mt-1">
                      <span :class="getStatusColor(lead.status)" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                        {{ lead.formatted_status }}
                      </span>
                    </dd>
                  </div>
                </div>

                <!-- Source and Campaign -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <dt class="text-sm font-medium text-gray-500">Fuente</dt>
                    <dd class="mt-1">
                      <span :class="getSourceColor(lead.source)" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                        {{ lead.formatted_source }}
                      </span>
                    </dd>
                  </div>
                  <div v-if="lead.campaign">
                    <dt class="text-sm font-medium text-gray-500">Campaña</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                      <Link :href="route('campaigns.show', lead.campaign.id)" class="text-green-600 hover:text-green-900">
                        {{ lead.campaign.name }}
                      </Link>
                    </dd>
                  </div>
                </div>

                <!-- Budget and Agent -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div v-if="lead.budget_min">
                    <dt class="text-sm font-medium text-gray-500">Presupuesto Mínimo</dt>
                    <dd class="mt-1 text-sm text-gray-900">${{ formatCurrency(lead.budget_min) }}</dd>
                  </div>
                  <div>
                    <dt class="text-sm font-medium text-gray-500">Agente Asignado</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ lead.assigned_agent?.name || 'Sin asignar' }}</dd>
                  </div>
                </div>

                <!-- Dates -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <dt class="text-sm font-medium text-gray-500">Fecha de Creación</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ formatDate(lead.created_at) }}</dd>
                  </div>
                  <div v-if="lead.last_contact_at">
                    <dt class="text-sm font-medium text-gray-500">Último Contacto</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ formatDate(lead.last_contact_at) }}</dd>
                  </div>
                </div>

                <!-- Notes -->
                <div v-if="lead.notes">
                  <dt class="text-sm font-medium text-gray-500">Notas</dt>
                  <dd class="mt-1 text-sm text-gray-900 whitespace-pre-wrap">{{ lead.notes }}</dd>
                </div>

                <!-- Interests -->
                <div v-if="lead.interests && lead.interests.length > 0">
                  <dt class="text-sm font-medium text-gray-500 mb-2">Intereses</dt>
                  <dd class="flex flex-wrap gap-2">
                    <span 
                      v-for="interest in lead.interests" 
                      :key="interest"
                      class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800"
                    >
                      {{ interest }}
                    </span>
                  </dd>
                </div>
              </div>
            </div>
          </div>

          <!-- Actions & Timeline -->
          <div class="space-y-6">
            <!-- Quick Actions -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
              <div class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Acciones Rápidas</h3>
                
                <div class="space-y-3">
                  <button
                    @click="markAsContacted"
                    :disabled="lead.status === 'contacted'"
                    class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-yellow-600 hover:bg-yellow-700 disabled:opacity-50 disabled:cursor-not-allowed"
                  >
                    Marcar como Contactado
                  </button>
                  
                  <button
                    @click="markAsQualified"
                    :disabled="lead.status === 'qualified' || lead.status === 'converted'"
                    class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed"
                  >
                    Marcar como Calificado
                  </button>

                  <button
                    @click="markAsLost"
                    :disabled="lead.status === 'lost' || lead.status === 'converted'"
                    class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
                  >
                    Marcar como Perdido
                  </button>

                  <Link
                    :href="route('activities.create', { related_type: 'App\\Models\\Lead', related_id: lead.id })"
                    class="w-full inline-flex justify-center items-center px-4 py-2 border border-green-300 text-sm font-medium rounded-md text-green-700 bg-green-50 hover:bg-green-100"
                  >
                    Nueva Actividad
                  </Link>
                </div>
              </div>
            </div>

            <!-- Activities Timeline -->
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
              <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                  <h3 class="text-lg font-medium text-gray-900">Actividades & Timeline</h3>
                  <Link
                    :href="route('activities.create', { related_type: 'App\\Models\\Lead', related_id: lead.id })"
                    class="inline-flex items-center px-3 py-1 border border-transparent text-sm leading-4 font-medium rounded-md text-green-700 bg-green-100 hover:bg-green-200"
                  >
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Actividad
                  </Link>
                </div>
                
                <div class="flow-root">
                  <ul class="-mb-8">
                    <!-- Activities -->
                    <li v-for="activity in lead.activities" :key="`activity-${activity.id}`" class="relative pb-8">
                      <div class="absolute top-5 left-5 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></div>
                      <div class="relative flex items-start space-x-3">
                        <div class="relative">
                          <div :class="getActivityIconColor(activity.type)" class="h-10 w-10 rounded-full flex items-center justify-center ring-8 ring-white">
                            <component :is="getActivityIcon(activity.type)" class="h-5 w-5" />
                          </div>
                          <span v-if="activity.status === 'completed'" class="absolute -bottom-0.5 -right-1 bg-green-400 rounded-full p-0.5">
                            <svg class="h-3 w-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                              <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                          </span>
                          <span v-else-if="activity.is_overdue" class="absolute -bottom-0.5 -right-1 bg-red-400 rounded-full p-0.5">
                            <svg class="h-3 w-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01"/>
                            </svg>
                          </span>
                        </div>
                        <div class="min-w-0 flex-1">
                          <div class="flex items-center justify-between">
                            <div>
                              <Link :href="route('activities.show', activity.id)" class="text-sm font-medium text-gray-900 hover:text-green-600">
                                {{ activity.subject }}
                              </Link>
                              <div class="flex items-center space-x-2 mt-1">
                                <span :class="getActivityTypeColor(activity.type)" class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium">
                                  {{ activity.formatted_type }}
                                </span>
                                <span :class="getActivityStatusColor(activity.status)" class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium">
                                  {{ activity.formatted_status }}
                                </span>
                                <span :class="getActivityPriorityColor(activity.priority)" class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium">
                                  {{ activity.formatted_priority }}
                                </span>
                              </div>
                            </div>
                            <div class="flex items-center space-x-2">
                              <div class="text-right text-xs text-gray-500">
                                <div v-if="activity.scheduled_at">{{ formatDate(activity.scheduled_at) }}</div>
                                <div v-else>{{ formatDate(activity.created_at) }}</div>
                              </div>
                              <div class="flex space-x-1">
                                <Link :href="route('activities.show', activity.id)" class="text-green-600 hover:text-green-900">
                                  <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                  </svg>
                                </Link>
                                <button v-if="activity.status === 'pending'" @click="completeActivity(activity)" class="text-green-600 hover:text-green-900">
                                  <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                  </svg>
                                </button>
                              </div>
                            </div>
                          </div>
                          <div v-if="activity.description" class="mt-1 text-sm text-gray-600">
                            {{ truncateText(activity.description, 100) }}
                          </div>
                        </div>
                      </div>
                    </li>

                    <!-- Lead Creation -->
                    <li class="relative pb-8">
                      <div class="absolute top-5 left-5 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></div>
                      <div class="relative flex space-x-3">
                        <div>
                          <span class="h-10 w-10 rounded-full bg-green-500 flex items-center justify-center ring-8 ring-white">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                              <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                          </span>
                        </div>
                        <div class="min-w-0 flex-1">
                          <div>
                            <p class="text-sm font-medium text-gray-900">Lead creado</p>
                            <p class="text-xs text-gray-500 mt-1">
                              Fuente: {{ lead.formatted_source }}
                              <span v-if="lead.campaign"> • Campaña: {{ lead.campaign.name }}</span>
                            </p>
                            <p class="text-xs text-gray-400">{{ formatDate(lead.created_at) }}</p>
                          </div>
                        </div>
                      </div>
                    </li>

                    <!-- Conversion Status -->
                    <li v-if="lead.status === 'converted'" class="relative">
                      <div class="relative flex space-x-3">
                        <div>
                          <span class="h-10 w-10 rounded-full bg-green-500 flex items-center justify-center ring-8 ring-white">
                            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                              <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                            </svg>
                          </span>
                        </div>
                        <div class="min-w-0 flex-1">
                          <div>
                            <p class="text-sm font-medium text-gray-900">Convertido a cliente</p>
                            <p v-if="lead.converted_client" class="text-xs text-green-600 mt-1">
                              Cliente: {{ lead.converted_client.first_name }} {{ lead.converted_client.last_name }}
                            </p>
                            <p class="text-xs text-gray-400">{{ formatDate(lead.updated_at) }}</p>
                          </div>
                        </div>
                      </div>
                    </li>
                  </ul>
                </div>

                <!-- Empty state for activities -->
                <div v-if="!lead.activities || lead.activities.length === 0" class="text-center py-8">
                  <svg class="mx-auto h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z"/>
                  </svg>
                  <p class="mt-2 text-sm text-gray-600">No hay actividades registradas</p>
                  <p class="text-xs text-gray-500">Crea la primera actividad para empezar el seguimiento</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Convert to Client Modal -->
    <Modal :show="showConvertModal" @close="showConvertModal = false">
      <div class="p-6">
        <div class="flex items-center">
          <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
            <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </div>
          <div class="ml-4">
            <h3 class="text-lg font-medium text-gray-900">Convertir a Cliente</h3>
            <p class="mt-2 text-sm text-gray-500">
              ¿Estás seguro de que deseas convertir este lead en cliente? Esta acción creará un nuevo registro de cliente.
            </p>
          </div>
        </div>
        <div class="mt-6 flex justify-end space-x-3">
          <button @click="showConvertModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
            Cancelar
          </button>
          <button @click="convertToClient" class="px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md hover:bg-green-700">
            Convertir
          </button>
        </div>
      </div>
    </Modal>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, h } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Modal from '@/Components/Modal.vue'

const props = defineProps({
  lead: Object,
  can: Object
})

// State
const showConvertModal = ref(false)

// Methods
const getStatusColor = (status) => {
  const colors = {
    'new': 'bg-green-100 text-green-800',
    'contacted': 'bg-yellow-100 text-yellow-800',
    'qualified': 'bg-green-100 text-green-800',
    'converted': 'bg-purple-100 text-purple-800',
    'lost': 'bg-red-100 text-red-800'
  }
  return colors[status] || 'bg-gray-100 text-gray-800'
}

const getSourceColor = (source) => {
  const colors = {
    'website': 'bg-green-100 text-green-800',
    'social': 'bg-purple-100 text-purple-800',
    'campaign': 'bg-green-100 text-green-800',
    'referral': 'bg-yellow-100 text-yellow-800',
    'phone': 'bg-indigo-100 text-indigo-800',
    'walk_in': 'bg-gray-100 text-gray-800'
  }
  return colors[source] || 'bg-gray-100 text-gray-800'
}

const getPriorityColor = (priority) => {
  const colors = {
    'low': 'bg-green-100 text-green-800',
    'medium': 'bg-yellow-100 text-yellow-800',
    'high': 'bg-red-100 text-red-800'
  }
  return colors[priority] || 'bg-gray-100 text-gray-800'
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

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('es-CO').format(amount)
}

// Activity helper methods
const getActivityIcon = (type) => {
  const icons = {
    call: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
      h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z' })
    ]),
    email: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
      h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z' })
    ]),
    meeting: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
      h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z' })
    ]),
    task: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
      h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4' })
    ]),
    note: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
      h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z' })
    ]),
    sms: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
      h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z' })
    ]),
    whatsapp: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
      h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z' })
    ]),
    visit: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
      h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z' }),
      h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M15 11a3 3 0 11-6 0 3 3 0 016 0z' })
    ])
  }
  return icons[type] || icons.task
}

const getActivityIconColor = (type) => {
  const colors = {
    call: 'bg-green-100 text-green-600',
    email: 'bg-green-100 text-green-600',
    meeting: 'bg-purple-100 text-purple-600',
    note: 'bg-yellow-100 text-yellow-600',
    task: 'bg-gray-100 text-gray-600',
    sms: 'bg-indigo-100 text-indigo-600',
    whatsapp: 'bg-green-100 text-green-600',
    visit: 'bg-orange-100 text-orange-600'
  }
  return colors[type] || 'bg-gray-100 text-gray-600'
}

const getActivityTypeColor = (type) => {
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

const getActivityStatusColor = (status) => {
  const colors = {
    pending: 'bg-yellow-100 text-yellow-800',
    completed: 'bg-green-100 text-green-800',
    cancelled: 'bg-red-100 text-red-800'
  }
  return colors[status] || 'bg-gray-100 text-gray-800'
}

const getActivityPriorityColor = (priority) => {
  const colors = {
    low: 'bg-green-100 text-green-800',
    medium: 'bg-yellow-100 text-yellow-800',
    high: 'bg-orange-100 text-orange-800',
    urgent: 'bg-red-100 text-red-800'
  }
  return colors[priority] || 'bg-gray-100 text-gray-800'
}

const truncateText = (text, length) => {
  if (!text) return ''
  return text.length > length ? text.substring(0, length) + '...' : text
}

const completeActivity = (activity) => {
  router.post(route('activities.complete', activity.id), {}, {
    preserveScroll: true
  })
}

// Lead actions
const markAsContacted = () => {
  router.patch(route('leads.update-status', props.lead.id), {
    status: 'contacted'
  })
}

const markAsQualified = () => {
  router.patch(route('leads.update-status', props.lead.id), {
    status: 'qualified'
  })
}

const markAsLost = () => {
  router.patch(route('leads.update-status', props.lead.id), {
    status: 'lost'
  })
}

const scheduleFollowUp = () => {
  // Implementation for scheduling follow-up
  alert('Funcionalidad de seguimiento en desarrollo')
}

const convertToClient = () => {
  router.post(route('leads.convert', props.lead.id), {}, {
    onSuccess: () => {
      showConvertModal.value = false
    }
  })
}
</script>