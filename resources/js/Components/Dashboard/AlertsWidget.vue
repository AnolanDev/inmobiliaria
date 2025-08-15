<template>
  <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
    <div class="px-4 py-5 sm:p-6">
      <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4 flex items-center">
        <svg class="w-5 h-5 text-yellow-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.996-.833-2.732 0L3.732 19c-.77.833.192 2.5 1.732 2.5z"/>
        </svg>
        Alertas Importantes
        <span v-if="totalAlerts > 0" class="ml-2 px-2 py-1 text-xs bg-red-100 text-red-800 rounded-full">
          {{ totalAlerts }}
        </span>
      </h3>

      <div class="space-y-4 max-h-96 overflow-y-auto">
        <!-- Overdue Visits -->
        <div v-if="alerts.overdue_visits && alerts.overdue_visits.length > 0" class="border-l-4 border-red-400 bg-red-50 p-4">
          <div class="flex">
            <div class="flex-shrink-0">
              <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
              </svg>
            </div>
            <div class="ml-3 flex-1">
              <h3 class="text-sm font-medium text-red-800">
                Visitas Vencidas ({{ alerts.overdue_visits.length }})
              </h3>
              <div class="mt-2 text-sm text-red-700">
                <div class="space-y-2">
                  <div
                    v-for="visit in alerts.overdue_visits.slice(0, 3)"
                    :key="visit.id"
                    class="flex items-center justify-between p-2 bg-white bg-opacity-50 rounded cursor-pointer hover:bg-opacity-75"
                    @click="$emit('view-visit', visit)"
                  >
                    <div>
                      <p class="font-medium">{{ visit.client?.name || 'Sin cliente' }}</p>
                      <p class="text-xs">{{ visit.property?.title || visit.project?.name || 'Sin propiedad' }}</p>
                    </div>
                    <div class="text-xs text-right">
                      {{ formatOverdueTime(visit.scheduled_at) }}
                    </div>
                  </div>
                  <div v-if="alerts.overdue_visits.length > 3" class="text-center">
                    <button class="text-xs text-red-600 hover:text-red-800 font-medium">
                      Ver {{ alerts.overdue_visits.length - 3 }} más...
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Follow-up Required -->
        <div v-if="alerts.follow_up_required && alerts.follow_up_required.length > 0" class="border-l-4 border-yellow-400 bg-yellow-50 p-4">
          <div class="flex">
            <div class="flex-shrink-0">
              <svg class="h-5 w-5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
              </svg>
            </div>
            <div class="ml-3 flex-1">
              <h3 class="text-sm font-medium text-yellow-800">
                Seguimientos Requeridos ({{ alerts.follow_up_required.length }})
              </h3>
              <div class="mt-2 text-sm text-yellow-700">
                <div class="space-y-2">
                  <div
                    v-for="visit in alerts.follow_up_required.slice(0, 3)"
                    :key="visit.id"
                    class="flex items-center justify-between p-2 bg-white bg-opacity-50 rounded cursor-pointer hover:bg-opacity-75"
                    @click="$emit('view-visit', visit)"
                  >
                    <div>
                      <p class="font-medium">{{ visit.client?.name || 'Sin cliente' }}</p>
                      <p class="text-xs">{{ visit.property?.title || visit.project?.name || 'Sin propiedad' }}</p>
                    </div>
                    <div class="text-xs text-right">
                      {{ formatDate(visit.follow_up_date) }}
                    </div>
                  </div>
                  <div v-if="alerts.follow_up_required.length > 3" class="text-center">
                    <button class="text-xs text-yellow-600 hover:text-yellow-800 font-medium">
                      Ver {{ alerts.follow_up_required.length - 3 }} más...
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Expiring Properties -->
        <div v-if="alerts.expiring_soon && alerts.expiring_soon.length > 0" class="border-l-4 border-orange-400 bg-orange-50 p-4">
          <div class="flex">
            <div class="flex-shrink-0">
              <svg class="h-5 w-5 text-orange-400" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM7 9a1 1 0 000 2h6a1 1 0 100-2H7z" clip-rule="evenodd"/>
              </svg>
            </div>
            <div class="ml-3 flex-1">
              <h3 class="text-sm font-medium text-orange-800">
                Propiedades por Vencer ({{ alerts.expiring_soon.length }})
              </h3>
              <div class="mt-2 text-sm text-orange-700">
                <div class="space-y-2">
                  <div
                    v-for="property in alerts.expiring_soon.slice(0, 3)"
                    :key="property.id"
                    class="flex items-center justify-between p-2 bg-white bg-opacity-50 rounded cursor-pointer hover:bg-opacity-75"
                    @click="$emit('view-property', property)"
                  >
                    <div>
                      <p class="font-medium">{{ property.title }}</p>
                      <p class="text-xs">{{ property.address }}</p>
                    </div>
                    <div class="text-xs text-right">
                      {{ formatDate(property.expires_at) }}
                    </div>
                  </div>
                  <div v-if="alerts.expiring_soon.length > 3" class="text-center">
                    <button class="text-xs text-orange-600 hover:text-orange-800 font-medium">
                      Ver {{ alerts.expiring_soon.length - 3 }} más...
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- No Alerts -->
        <div v-if="totalAlerts === 0" class="text-center py-8">
          <svg class="mx-auto h-12 w-12 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
          </svg>
          <h3 class="mt-2 text-sm font-medium text-gray-900">¡Todo en orden!</h3>
          <p class="mt-1 text-sm text-gray-500">No hay alertas importantes en este momento.</p>
        </div>
      </div>

      <!-- Action Buttons -->
      <div v-if="totalAlerts > 0" class="mt-6 flex justify-between">
        <button
          @click="$emit('dismiss-all')"
          class="text-sm text-gray-600 hover:text-gray-800"
        >
          Marcar todo como visto
        </button>
        <button
          @click="$emit('view-all-alerts')"
          class="text-sm text-green-600 hover:text-green-800 font-medium"
        >
          Ver todas las alertas
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  alerts: {
    type: Object,
    required: true
  }
})

defineEmits(['view-visit', 'view-property', 'dismiss-all', 'view-all-alerts'])

const totalAlerts = computed(() => {
  const overdueCount = props.alerts.overdue_visits?.length || 0
  const followUpCount = props.alerts.follow_up_required?.length || 0
  const expiringCount = props.alerts.expiring_soon?.length || 0
  return overdueCount + followUpCount + expiringCount
})

const formatDate = (date) => {
  if (!date) return ''
  return new Date(date).toLocaleDateString('es-ES', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

const formatOverdueTime = (dateTime) => {
  if (!dateTime) return ''
  const date = new Date(dateTime)
  const now = new Date()
  const diffInHours = Math.floor((now - date) / (1000 * 60 * 60))
  
  if (diffInHours < 1) return 'Vencida hace minutos'
  if (diffInHours < 24) return `Vencida hace ${diffInHours} hora${diffInHours > 1 ? 's' : ''}`
  
  const diffInDays = Math.floor(diffInHours / 24)
  return `Vencida hace ${diffInDays} día${diffInDays > 1 ? 's' : ''}`
}
</script>