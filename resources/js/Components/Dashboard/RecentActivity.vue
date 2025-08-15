<template>
  <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
    <div class="px-4 py-5 sm:p-6">
      <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Actividad Reciente</h3>
      
      <!-- Activity Tabs -->
      <div class="mb-4">
        <nav class="flex space-x-4">
          <button
            v-for="tab in tabs"
            :key="tab.key"
            @click="activeTab = tab.key"
            :class="[
              'px-3 py-2 text-sm font-medium rounded-md transition-colors',
              activeTab === tab.key
                ? 'bg-green-100 text-green-700'
                : 'text-gray-500 hover:text-gray-700 hover:bg-gray-100'
            ]"
          >
            {{ tab.label }}
            <span v-if="getTabCount(tab.key) > 0" class="ml-2 px-2 py-0.5 text-xs bg-gray-200 rounded-full">
              {{ getTabCount(tab.key) }}
            </span>
          </button>
        </nav>
      </div>

      <!-- Activity Content -->
      <div class="space-y-4 max-h-96 overflow-y-auto">
        <!-- Visits -->
        <div v-if="activeTab === 'visits'" class="space-y-3">
          <div
            v-for="visit in activity.visits"
            :key="visit.id"
            class="flex items-start space-x-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors cursor-pointer"
            @click="$emit('view-visit', visit)"
          >
            <div class="flex-shrink-0">
              <div :class="[
                'w-8 h-8 rounded-full flex items-center justify-center text-xs font-medium text-white',
                getVisitStatusColor(visit.status)
              ]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
              </div>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-sm font-medium text-gray-900">
                Visita con {{ visit.client?.name || 'Cliente' }}
              </p>
              <p class="text-sm text-gray-500">
                {{ visit.property?.title || visit.project?.name || 'Propiedad' }}
              </p>
              <p class="text-xs text-gray-400">
                {{ formatDateTime(visit.scheduled_at) }}
              </p>
            </div>
            <div class="flex-shrink-0">
              <span :class="[
                'px-2 py-1 text-xs font-medium rounded-full',
                getVisitStatusBadge(visit.status)
              ]">
                {{ getVisitStatusLabel(visit.status) }}
              </span>
            </div>
          </div>
          <div v-if="activity.visits.length === 0" class="text-sm text-gray-500 text-center py-4">
            No hay visitas recientes
          </div>
        </div>

        <!-- Clients -->
        <div v-if="activeTab === 'clients'" class="space-y-3">
          <div
            v-for="client in activity.clients"
            :key="client.id"
            class="flex items-start space-x-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors cursor-pointer"
            @click="$emit('view-client', client)"
          >
            <div class="flex-shrink-0">
              <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
              </div>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-sm font-medium text-gray-900">{{ client.name }}</p>
              <p class="text-sm text-gray-500">{{ client.email }}</p>
              <p class="text-xs text-gray-400">
                Registrado {{ formatRelativeTime(client.created_at) }}
              </p>
            </div>
            <div class="flex-shrink-0">
              <span :class="[
                'px-2 py-1 text-xs font-medium rounded-full',
                getClientStatusBadge(client.status)
              ]">
                {{ getClientStatusLabel(client.status) }}
              </span>
            </div>
          </div>
          <div v-if="activity.clients.length === 0" class="text-sm text-gray-500 text-center py-4">
            No hay clientes recientes
          </div>
        </div>

        <!-- Properties -->
        <div v-if="activeTab === 'properties'" class="space-y-3">
          <div
            v-for="property in activity.properties"
            :key="property.id"
            class="flex items-start space-x-3 p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors cursor-pointer"
            @click="$emit('view-property', property)"
          >
            <div class="flex-shrink-0">
              <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
              </div>
            </div>
            <div class="flex-1 min-w-0">
              <p class="text-sm font-medium text-gray-900">{{ property.title }}</p>
              <p class="text-sm text-gray-500">{{ property.address }}</p>
              <div class="flex items-center mt-1">
                <p class="text-xs text-gray-400">
                  Agregada {{ formatRelativeTime(property.created_at) }}
                </p>
                <span v-if="property.price" class="ml-2 text-xs font-medium text-green-600">
                  {{ formatCurrency(property.price) }}
                </span>
              </div>
            </div>
            <div class="flex-shrink-0">
              <span :class="[
                'px-2 py-1 text-xs font-medium rounded-full',
                getPropertyStatusBadge(property.status)
              ]">
                {{ getPropertyStatusLabel(property.status) }}
              </span>
            </div>
          </div>
          <div v-if="activity.properties.length === 0" class="text-sm text-gray-500 text-center py-4">
            No hay propiedades recientes
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'

const props = defineProps({
  activity: {
    type: Object,
    required: true
  }
})

defineEmits(['view-visit', 'view-client', 'view-property'])

const activeTab = ref('visits')

const tabs = [
  { key: 'visits', label: 'Visitas' },
  { key: 'clients', label: 'Clientes' },
  { key: 'properties', label: 'Propiedades' }
]

const getTabCount = (tabKey) => {
  return props.activity[tabKey]?.length || 0
}

// Visit related methods
const getVisitStatusColor = (status) => {
  const colors = {
    scheduled: 'bg-green-500',
    completed: 'bg-green-500',
    cancelled: 'bg-red-500',
    no_show: 'bg-gray-500'
  }
  return colors[status] || 'bg-gray-500'
}

const getVisitStatusBadge = (status) => {
  const badges = {
    scheduled: 'bg-green-100 text-green-800',
    completed: 'bg-green-100 text-green-800',
    cancelled: 'bg-red-100 text-red-800',
    no_show: 'bg-gray-100 text-gray-800'
  }
  return badges[status] || 'bg-gray-100 text-gray-800'
}

const getVisitStatusLabel = (status) => {
  const labels = {
    scheduled: 'Programada',
    completed: 'Completada',
    cancelled: 'Cancelada',
    no_show: 'No Asistió'
  }
  return labels[status] || status
}

// Client related methods
const getClientStatusBadge = (status) => {
  const badges = {
    active: 'bg-green-100 text-green-800',
    potential: 'bg-yellow-100 text-yellow-800',
    client: 'bg-green-100 text-green-800'
  }
  return badges[status] || 'bg-gray-100 text-gray-800'
}

const getClientStatusLabel = (status) => {
  const labels = {
    active: 'Activo',
    potential: 'Potencial',
    client: 'Cliente'
  }
  return labels[status] || status
}

// Property related methods
const getPropertyStatusBadge = (status) => {
  const badges = {
    available: 'bg-green-100 text-green-800',
    sold: 'bg-green-100 text-green-800',
    rented: 'bg-yellow-100 text-yellow-800',
    reserved: 'bg-purple-100 text-purple-800'
  }
  return badges[status] || 'bg-gray-100 text-gray-800'
}

const getPropertyStatusLabel = (status) => {
  const labels = {
    available: 'Disponible',
    sold: 'Vendida',
    rented: 'Alquilada',
    reserved: 'Reservada'
  }
  return labels[status] || status
}

// Formatting methods
const formatDateTime = (dateTime) => {
  if (!dateTime) return ''
  return new Date(dateTime).toLocaleDateString('es-ES', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}

const formatRelativeTime = (dateTime) => {
  if (!dateTime) return ''
  const date = new Date(dateTime)
  const now = new Date()
  const diffInHours = Math.floor((now - date) / (1000 * 60 * 60))
  
  if (diffInHours < 1) return 'hace un momento'
  if (diffInHours < 24) return `hace ${diffInHours} hora${diffInHours > 1 ? 's' : ''}`
  
  const diffInDays = Math.floor(diffInHours / 24)
  if (diffInDays < 7) return `hace ${diffInDays} día${diffInDays > 1 ? 's' : ''}`
  
  return date.toLocaleDateString('es-ES', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('es-CO', {
    style: 'currency',
    currency: 'COP',
    minimumFractionDigits: 0,
    maximumFractionDigits: 0
  }).format(amount)
}
</script>