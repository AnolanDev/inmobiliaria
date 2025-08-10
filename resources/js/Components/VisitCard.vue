<template>
  <div 
    class="bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-shadow duration-200 cursor-pointer"
    @click="handleCardClick"
  >
    <div class="p-6">
      <!-- Header with Type and Priority -->
      <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-2">
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

        <!-- Status Badge -->
        <span
          :class="[
            'px-2.5 py-0.5 rounded-full text-xs font-medium',
            visit.status_color || 'bg-gray-100 text-gray-800'
          ]"
        >
          {{ statusLabel(visit.status) }}
        </span>
      </div>

      <!-- Visit Subject Info -->
      <div class="mb-4">
        <div class="flex items-center gap-2 mb-1">
          <span v-if="visit.is_project_visit" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
            </svg>
            Proyecto
          </span>
          <span v-else-if="visit.is_property_visit" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
            </svg>
            Propiedad
          </span>
        </div>
        
        <h3 class="text-lg font-semibold text-gray-900 mb-1">
          {{ visit.visit_subject }}
        </h3>
        <p class="text-sm text-gray-600 mb-2">
          {{ visit.visit_location }}
        </p>
      </div>

      <!-- Client and Agent Info -->
      <div class="mb-4 space-y-2">
        <div class="flex items-center gap-2 text-sm text-gray-600">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
          </svg>
          <span class="font-medium">Cliente:</span>
          {{ visit.client?.name || 'Cliente no disponible' }}
        </div>

        <div class="flex items-center gap-2 text-sm text-gray-600">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 00-2 2h-4a2 2 0 00-2-2V6m8 0h2a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V8a2 2 0 012-2h2"/>
          </svg>
          <span class="font-medium">Agente:</span>
          {{ visit.agent?.name || 'Agente no disponible' }}
        </div>
      </div>

      <!-- Date and Time -->
      <div class="mb-4">
        <div class="flex items-center gap-2 text-sm">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
          </svg>
          <span 
            :class="[
              'font-medium',
              visit.is_overdue ? 'text-red-600' : visit.is_today ? 'text-blue-600' : 'text-gray-900'
            ]"
          >
            {{ formatDateTime(visit.scheduled_at) }}
          </span>
        </div>

        <div v-if="visit.estimated_duration" class="flex items-center gap-2 text-sm text-gray-600 mt-1">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
          {{ visit.estimated_duration }} minutos
        </div>
      </div>

      <!-- Outcome Badge (if completed) -->
      <div v-if="visit.outcome" class="mb-4">
        <span
          :class="[
            'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
            getOutcomeColor(visit.outcome)
          ]"
        >
          {{ outcomeLabel(visit.outcome) }}
        </span>
      </div>

      <!-- Follow-up indicator -->
      <div v-if="visit.requires_follow_up" class="mb-4">
        <div class="flex items-center gap-2 text-sm text-yellow-600">
          <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
          </svg>
          Requiere seguimiento
          <span v-if="visit.follow_up_date">
            ({{ formatDate(visit.follow_up_date) }})
          </span>
        </div>
      </div>

      <!-- Notes preview -->
      <div v-if="visit.notes" class="mb-4">
        <p class="text-sm text-gray-600 line-clamp-2">
          {{ visit.notes }}
        </p>
      </div>

      <!-- Actions -->
      <div class="flex items-center justify-between pt-4 border-t border-gray-100">
        <!-- Basic Actions -->
        <div class="flex items-center gap-2">
          <button
            @click.stop="$emit('view', visit)"
            class="text-blue-600 hover:text-blue-800 text-sm font-medium"
            title="Ver detalles"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
            </svg>
          </button>

          <button
            v-if="visit.status === 'scheduled'"
            @click.stop="$emit('edit', visit)"
            class="text-gray-600 hover:text-gray-800 text-sm font-medium"
            title="Editar"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
          </button>

          <button
            @click.stop="$emit('delete', visit)"
            class="text-red-600 hover:text-red-800 text-sm font-medium"
            title="Eliminar"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
          </button>
        </div>

        <!-- Status Actions -->
        <div v-if="visit.status === 'scheduled'" class="flex items-center gap-1">
          <button
            @click.stop="$emit('complete', visit)"
            class="text-green-600 hover:text-green-800 p-1 rounded"
            title="Marcar como completada"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </button>

          <button
            @click.stop="$emit('cancel', visit)"
            class="text-red-600 hover:text-red-800 p-1 rounded"
            title="Cancelar visita"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>

          <button
            @click.stop="$emit('no-show', visit)"
            class="text-gray-600 hover:text-gray-800 p-1 rounded"
            title="No asistió"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { defineEmits, defineProps } from 'vue'

const props = defineProps({
  visit: {
    type: Object,
    required: true
  }
})

const emit = defineEmits(['view', 'edit', 'delete', 'complete', 'cancel', 'no-show'])

// Handle card click to view details
const handleCardClick = () => {
  emit('view', props.visit)
}

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
    offer_made: 'bg-blue-100 text-blue-800',
    deal_closed: 'bg-purple-100 text-purple-800'
  }
  return colors[outcome] || 'bg-gray-100 text-gray-800'
}

const formatDateTime = (dateTime) => {
  if (!dateTime) return ''
  const date = new Date(dateTime)
  return date.toLocaleDateString('es-ES', {
    weekday: 'short',
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
    hour12: true
  })
}

const formatDate = (date) => {
  if (!date) return ''
  return new Date(date).toLocaleDateString('es-ES')
}
</script>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>