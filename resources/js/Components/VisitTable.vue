<template>
  <div class="overflow-x-auto">
    <table class="min-w-full divide-y divide-gray-200">
      <thead class="bg-gray-50">
        <tr>
          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
            Visita
          </th>
          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
            Destino
          </th>
          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
            Cliente
          </th>
          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
            Agente
          </th>
          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
            Fecha y Hora
          </th>
          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
            Estado
          </th>
          <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
            Resultado
          </th>
          <th scope="col" class="relative px-6 py-3">
            <span class="sr-only">Acciones</span>
          </th>
        </tr>
      </thead>
      <tbody class="bg-white divide-y divide-gray-200">
        <tr v-for="visit in visits" :key="visit.id" class="hover:bg-gray-50">
          <td class="px-6 py-4 whitespace-nowrap">
            <div class="flex flex-col">
              <div class="flex items-center gap-2 mb-1">
                <!-- Type Badge -->
                <span
                  :class="[
                    'px-2 py-1 rounded-full text-xs font-medium',
                    visit.type_color || 'bg-gray-100 text-gray-800'
                  ]"
                >
                  {{ typeLabel(visit.type) }}
                </span>
                <!-- Priority Badge -->
                <span
                  :class="[
                    'px-2 py-1 rounded-full text-xs font-medium',
                    visit.priority_color || 'bg-gray-100 text-gray-800'
                  ]"
                >
                  {{ priorityLabel(visit.priority) }}
                </span>
              </div>
              
              <!-- Follow-up indicator -->
              <div v-if="visit.requires_follow_up" class="flex items-center gap-1 text-xs text-yellow-600">
                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                Seguimiento
              </div>
            </div>
          </td>

          <td class="px-6 py-4">
            <div>
              <div class="flex items-center gap-2 mb-1">
                <span v-if="visit.is_project_visit" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                  Proyecto
                </span>
                <span v-else-if="visit.is_property_visit" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                  Propiedad
                </span>
              </div>
              <div class="text-sm font-medium text-gray-900">
                {{ visit.visit_subject }}
              </div>
              <div class="text-sm text-gray-500">
                {{ visit.visit_location }}
              </div>
            </div>
          </td>

          <td class="px-6 py-4">
            <div>
              <div class="text-sm font-medium text-gray-900">
                {{ visit.client?.name || 'Cliente no disponible' }}
              </div>
              <div v-if="visit.client?.email" class="text-sm text-gray-500">
                {{ visit.client.email }}
              </div>
              <div v-if="visit.client?.phone" class="text-sm text-gray-500">
                {{ visit.client.phone }}
              </div>
            </div>
          </td>

          <td class="px-6 py-4">
            <div>
              <div class="text-sm font-medium text-gray-900">
                {{ visit.agent?.name || 'Agente no disponible' }}
              </div>
              <div v-if="visit.agent?.email" class="text-sm text-gray-500">
                {{ visit.agent.email }}
              </div>
            </div>
          </td>

          <td class="px-6 py-4 whitespace-nowrap">
            <div class="flex flex-col">
              <div 
                :class="[
                  'text-sm font-medium',
                  visit.is_overdue ? 'text-red-600' : visit.is_today ? 'text-green-600' : 'text-gray-900'
                ]"
              >
                {{ formatDateTime(visit.scheduled_at) }}
              </div>
              <div v-if="visit.estimated_duration" class="text-xs text-gray-500">
                {{ visit.estimated_duration }}min
              </div>
            </div>
          </td>

          <td class="px-6 py-4 whitespace-nowrap">
            <span
              :class="[
                'inline-flex px-2 py-1 text-xs font-semibold rounded-full',
                visit.status_color || 'bg-gray-100 text-gray-800'
              ]"
            >
              {{ statusLabel(visit.status) }}
            </span>
          </td>

          <td class="px-6 py-4 whitespace-nowrap">
            <span
              v-if="visit.outcome"
              :class="[
                'inline-flex px-2 py-1 text-xs font-semibold rounded-full',
                getOutcomeColor(visit.outcome)
              ]"
            >
              {{ outcomeLabel(visit.outcome) }}
            </span>
            <span v-else class="text-sm text-gray-400">-</span>
          </td>

          <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
            <div class="flex items-center gap-2 justify-end">
              <!-- View Button -->
              <button
                @click="$emit('view', visit)"
                class="text-green-600 hover:text-green-900"
                title="Ver detalles"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
              </button>

              <!-- Edit Button (only for scheduled visits) -->
              <button
                v-if="visit.status === 'scheduled'"
                @click="$emit('edit', visit)"
                class="text-gray-600 hover:text-gray-900"
                title="Editar"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
              </button>

              <!-- Status Action Buttons -->
              <div v-if="visit.status === 'scheduled'" class="flex items-center gap-1">
                <button
                  @click="$emit('complete', visit)"
                  class="text-green-600 hover:text-green-900"
                  title="Marcar como completada"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                </button>

                <button
                  @click="$emit('cancel', visit)"
                  class="text-red-600 hover:text-red-900"
                  title="Cancelar"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                  </svg>
                </button>

                <button
                  @click="$emit('no-show', visit)"
                  class="text-gray-600 hover:text-gray-900"
                  title="No asistió"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                </button>
              </div>

              <!-- Delete Button -->
              <button
                @click="$emit('delete', visit)"
                class="text-red-600 hover:text-red-900"
                title="Eliminar"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                </svg>
              </button>
            </div>
          </td>
        </tr>
      </tbody>
    </table>

    <!-- Empty state for table -->
    <div v-if="visits.length === 0" class="text-center py-8">
      <svg class="mx-auto h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
      </svg>
      <p class="mt-2 text-sm text-gray-500">No hay visitas que mostrar</p>
    </div>
  </div>
</template>

<script setup>
import { defineEmits } from 'vue'

defineProps({
  visits: {
    type: Array,
    required: true
  }
})

defineEmits(['view', 'edit', 'delete', 'complete', 'cancel', 'no-show'])

const typeLabel = (type) => {
  const labels = {
    showing: 'Visita',
    inspection: 'Inspección',
    evaluation: 'Evaluación',
    follow_up: 'Seguimiento',
    closing: 'Cierre'
  }
  return labels[type] || type
}

const priorityLabel = (priority) => {
  const labels = {
    low: 'Baja',
    medium: 'Media',
    high: 'Alta',
    urgent: 'Urgente'
  }
  return labels[priority] || priority
}

const statusLabel = (status) => {
  const labels = {
    scheduled: 'Programada',
    completed: 'Completada',
    cancelled: 'Cancelada',
    no_show: 'No Asistió'
  }
  return labels[status] || status
}

const outcomeLabel = (outcome) => {
  const labels = {
    interested: 'Interesado',
    not_interested: 'No Interesado',
    needs_follow_up: 'Requiere Seguimiento',
    offer_made: 'Oferta Realizada',
    deal_closed: 'Trato Cerrado'
  }
  return labels[outcome] || outcome
}

const getOutcomeColor = (outcome) => {
  const colors = {
    interested: 'bg-green-100 text-green-800',
    not_interested: 'bg-red-100 text-red-800',
    needs_follow_up: 'bg-yellow-100 text-yellow-800',
    offer_made: 'bg-green-100 text-green-800',
    deal_closed: 'bg-purple-100 text-purple-800'
  }
  return colors[outcome] || 'bg-gray-100 text-gray-800'
}

const formatDateTime = (dateTime) => {
  if (!dateTime) return ''
  const date = new Date(dateTime)
  return date.toLocaleDateString('es-ES', {
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
    hour12: true
  })
}
</script>