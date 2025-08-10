<template>
  <AuthenticatedLayout title="Visitas">
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
          <div>
            <h1 class="text-3xl font-bold text-gray-900">Visitas</h1>
            <p class="text-gray-600 mt-2">Gestiona tu agenda de visitas inmobiliarias</p>
          </div>
          <Link
            :href="route('visits.create')"
            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium shadow-sm transition-colors inline-flex items-center gap-2"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Nueva Visita
          </Link>
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4 mb-8">
          <div 
            @click="filterByToday"
            class="rounded-lg shadow-sm p-6 cursor-pointer hover:shadow-md transition-all duration-200"
            :style="{ 
              backgroundColor: isTodayActive ? '#dbeafe' : '#ffffff',
              borderColor: isTodayActive ? '#3b82f6' : '#e5e7eb',
              borderWidth: '1px',
              borderStyle: 'solid'
            }"
          >
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-gray-600">Programadas Hoy</p>
                <p class="text-2xl font-bold text-blue-600">{{ todayCount }}</p>
              </div>
              <div class="h-12 w-12 bg-blue-100 rounded-full flex items-center justify-center">
                <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
              </div>
            </div>
          </div>

          <div 
            @click="filterByCompleted"
            class="rounded-lg shadow-sm p-6 cursor-pointer hover:shadow-md transition-all duration-200"
            :style="{ 
              backgroundColor: isCompletedActive ? '#dcfce7' : '#ffffff',
              borderColor: isCompletedActive ? '#22c55e' : '#e5e7eb',
              borderWidth: '1px',
              borderStyle: 'solid'
            }"
          >
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-gray-600">Completadas</p>
                <p class="text-2xl font-bold text-green-600">{{ completedCount }}</p>
              </div>
              <div class="h-12 w-12 bg-green-100 rounded-full flex items-center justify-center">
                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
              </div>
            </div>
          </div>

          <div 
            @click="filterByNoShow"
            class="rounded-lg shadow-sm p-6 cursor-pointer hover:shadow-md transition-all duration-200"
            :style="{ 
              backgroundColor: isNoShowActive ? '#f3f4f6' : '#ffffff',
              borderColor: isNoShowActive ? '#6b7280' : '#e5e7eb',
              borderWidth: '1px',
              borderStyle: 'solid'
            }"
          >
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-gray-600">No Asistió</p>
                <p class="text-2xl font-bold text-gray-600">{{ noShowCount }}</p>
              </div>
              <div class="h-12 w-12 bg-gray-100 rounded-full flex items-center justify-center">
                <svg class="h-6 w-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
              </div>
            </div>
          </div>

          <div 
            @click="filterByOverdue"
            class="rounded-lg shadow-sm p-6 cursor-pointer hover:shadow-md transition-all duration-200"
            :style="{ 
              backgroundColor: isOverdueActive ? '#fee2e2' : '#ffffff',
              borderColor: isOverdueActive ? '#ef4444' : '#e5e7eb',
              borderWidth: '1px',
              borderStyle: 'solid'
            }"
          >
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-gray-600">Vencidas</p>
                <p class="text-2xl font-bold text-red-600">{{ overdueCount }}</p>
              </div>
              <div class="h-12 w-12 bg-red-100 rounded-full flex items-center justify-center">
                <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
              </div>
            </div>
          </div>

          <div 
            @click="filterByFollowUp"
            class="rounded-lg shadow-sm p-6 cursor-pointer hover:shadow-md transition-all duration-200"
            :style="{ 
              backgroundColor: isFollowUpActive ? '#fef3c7' : '#ffffff',
              borderColor: isFollowUpActive ? '#f59e0b' : '#e5e7eb',
              borderWidth: '1px',
              borderStyle: 'solid'
            }"
          >
            <div class="flex items-center justify-between">
              <div>
                <p class="text-sm font-medium text-gray-600">Seguimientos</p>
                <p class="text-2xl font-bold text-yellow-600">{{ followUpCount }}</p>
              </div>
              <div class="h-12 w-12 bg-yellow-100 rounded-full flex items-center justify-center">
                <svg class="h-6 w-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
              </div>
            </div>
          </div>
        </div>

        <!-- Filters and Search -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
            <!-- Search -->
            <div class="lg:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-1">Buscar</label>
              <input
                v-model="filters.search"
                type="text"
                placeholder="Cliente, propiedad, agente..."
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              />
            </div>
            
            <!-- Status Filter -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
              <select
                v-model="filters.status"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              >
                <option value="">Todos</option>
                <option v-for="(label, value) in statuses" :key="value" :value="value">
                  {{ label }}
                </option>
              </select>
            </div>

            <!-- Type Filter -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Tipo</label>
              <select
                v-model="filters.type"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              >
                <option value="">Todos</option>
                <option v-for="(label, value) in types" :key="value" :value="value">
                  {{ label }}
                </option>
              </select>
            </div>

            <!-- Priority Filter -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Prioridad</label>
              <select
                v-model="filters.priority"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              >
                <option value="">Todas</option>
                <option v-for="(label, value) in priorities" :key="value" :value="value">
                  {{ label }}
                </option>
              </select>
            </div>

            <!-- Actions -->
            <div class="flex items-end gap-2">
              <button
                @click="clearFilters"
                class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-md font-medium transition-colors"
              >
                Limpiar
              </button>
            </div>
          </div>
        </div>


        <!-- View Toggle and Stats -->
        <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-6 gap-4">
          <div class="flex items-center gap-4">
            <!-- View Toggle -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-1 flex">
              <button
                @click="viewMode = 'cards'"
                :class="[
                  viewMode === 'cards' 
                    ? 'bg-blue-100 text-blue-700' 
                    : 'text-gray-500 hover:text-gray-700',
                  'p-2 rounded transition-colors'
                ]"
                title="Vista de tarjetas"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                </svg>
              </button>
              <button
                @click="viewMode = 'table'"
                :class="[
                  viewMode === 'table' 
                    ? 'bg-blue-100 text-blue-700' 
                    : 'text-gray-500 hover:text-gray-700',
                  'p-2 rounded transition-colors'
                ]"
                title="Vista de tabla"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                </svg>
              </button>
              <button
                @click="viewMode = 'calendar'"
                :class="[
                  viewMode === 'calendar' 
                    ? 'bg-blue-100 text-blue-700' 
                    : 'text-gray-500 hover:text-gray-700',
                  'p-2 rounded transition-colors'
                ]"
                title="Vista de calendario"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
              </button>
            </div>

            <!-- Stats -->
            <div class="text-sm text-gray-600">
              Mostrando {{ visits.data.length }} de {{ visits.total }} visitas
            </div>
          </div>
        </div>

        <!-- Cards View -->
        <div v-if="viewMode === 'cards'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <VisitCard
            v-for="visit in visits.data"
            :key="visit.id"
            :visit="visit"
            @view="viewVisit"
            @edit="editVisit"
            @delete="deleteVisit"
            @complete="completeVisit"
            @cancel="cancelVisit"
            @no-show="markNoShow"
          />
        </div>

        <!-- Table View -->
        <div v-else-if="viewMode === 'table'" class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
          <VisitTable
            :visits="visits.data"
            @view="viewVisit"
            @edit="editVisit"
            @delete="deleteVisit"
            @complete="completeVisit"
            @cancel="cancelVisit"
            @no-show="markNoShow"
          />
        </div>

        <!-- Calendar View -->
        <div v-else-if="viewMode === 'calendar'">
          <VisitCalendar
            :visits="allVisits"
            @view="viewVisit"
            @edit="editVisit"
            @delete="deleteVisit"
            @complete="completeVisit"
            @cancel="cancelVisit"
            @no-show="markNoShow"
          />
        </div>

        <!-- Pagination -->
        <div v-if="visits.data.length > 0" class="mt-8">
          <Pagination :links="visits.links" />
        </div>

        <!-- Empty State -->
        <div v-else class="text-center py-12">
          <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
          </svg>
          <h3 class="mt-2 text-sm font-medium text-gray-900">No hay visitas</h3>
          <p class="mt-1 text-sm text-gray-500">Comienza programando tu primera visita.</p>
          <div class="mt-6">
            <Link
              :href="route('visits.create')"
              class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700"
            >
              <svg class="-ml-1 mr-2 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
              </svg>
              Nueva Visita
            </Link>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, reactive, watch, computed } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import VisitCard from '@/Components/VisitCard.vue'
import VisitTable from '@/Components/VisitTable.vue'
import VisitCalendar from '@/Components/VisitCalendar.vue'
import Pagination from '@/Components/Pagination.vue'

const props = defineProps({
  visits: {
    type: Object,
    required: true
  },
  filters: {
    type: Object,
    default: () => ({})
  },
  stats: {
    type: Object,
    required: true
  },
  statuses: {
    type: Object,
    required: true
  },
  types: {
    type: Object,
    required: true
  },
  priorities: {
    type: Object,
    required: true
  },
  outcomes: {
    type: Object,
    required: true
  },
  agents: {
    type: Array,
    required: true
  },
  properties: {
    type: Array,
    required: true
  },
  clients: {
    type: Array,
    required: true
  },
  allVisits: {
    type: Array,
    required: true
  }
})

// View mode
const viewMode = ref('cards')

// Filters
const filters = reactive({
  search: props.filters.search || '',
  status: props.filters.status || '',
  type: props.filters.type || '',
  priority: props.filters.priority || '',
  agent_id: props.filters.agent_id || '',
  property_id: props.filters.property_id || '',
  client_id: props.filters.client_id || '',
  date_from: props.filters.date_from || '',
  date_to: props.filters.date_to || '',
  outcome: props.filters.outcome || '',
  overdue: props.filters.overdue || false,
  today: props.filters.today || false,
  requires_follow_up: props.filters.requires_follow_up || false,
})

// Quick stats from server-side calculations
const todayCount = computed(() => props.stats.today_scheduled)
const completedCount = computed(() => props.stats.completed)
const noShowCount = computed(() => props.stats.no_show)
const overdueCount = computed(() => props.stats.overdue)
const followUpCount = computed(() => props.stats.requires_follow_up)

// Computed properties for card states
const isTodayActive = computed(() => filters.today)
const isCompletedActive = computed(() => filters.status === 'completed')
const isNoShowActive = computed(() => filters.status === 'no_show')
const isOverdueActive = computed(() => filters.overdue)
const isFollowUpActive = computed(() => filters.requires_follow_up)

// Auto-apply filters when they change
watch(() => filters.search, (newValue) => {
  if (newValue.length >= 3 || newValue.length === 0) {
    applyFilters()
  }
}, { debounce: 500 })

// Auto-apply other filters
Object.keys(filters).forEach(key => {
  if (key !== 'search') {
    watch(() => filters[key], () => {
      applyFilters()
    })
  }
})

const applyFilters = () => {
  const queryParams = {}
  
  Object.keys(filters).forEach(key => {
    if (filters[key] !== '' && filters[key] !== false) {
      queryParams[key] = filters[key]
    }
  })

  router.get(route('visits.index'), queryParams, {
    preserveState: true,
    replace: true
  })
}

// Filter functions for stat cards
const filterByToday = () => {
  resetFilters()
  filters.today = true
  filters.status = 'scheduled'
  applyFilters()
}

const filterByCompleted = () => {
  resetFilters()
  filters.status = 'completed'
  applyFilters()
}

const filterByNoShow = () => {
  resetFilters()
  filters.status = 'no_show'
  applyFilters()
}

const filterByOverdue = () => {
  resetFilters()
  filters.overdue = true
  applyFilters()
}

const filterByFollowUp = () => {
  resetFilters()
  filters.requires_follow_up = true
  applyFilters()
}

// Reset filters without navigation (for internal use)
const resetFilters = () => {
  Object.keys(filters).forEach(key => {
    filters[key] = ''
  })
  filters.overdue = false
  filters.today = false
  filters.requires_follow_up = false
}

// Clear filters with navigation (for "Clear All" button)
const clearFilters = () => {
  resetFilters()
  router.get(route('visits.index'), {}, {
    preserveState: true,
    replace: true
  })
}

const setQuickFilter = (filterType) => {
  clearFilters()
  filters[filterType] = true
  applyFilters()
}

const viewVisit = (visit) => {
  router.get(route('visits.show', visit.id))
}

const editVisit = (visit) => {
  router.get(route('visits.edit', visit.id))
}

const deleteVisit = (visit) => {
  if (confirm(`¿Estás seguro de que deseas eliminar esta visita?`)) {
    router.delete(route('visits.destroy', visit.id), {
      onSuccess: () => {
        // Success message will be shown by flash message component
      }
    })
  }
}

const completeVisit = (visit) => {
  router.patch(route('visits.complete', visit.id), {}, {
    onSuccess: () => {
      // Success message will be shown by flash message component
    }
  })
}

const cancelVisit = (visit) => {
  const reason = prompt('Motivo de la cancelación:')
  if (reason) {
    router.patch(route('visits.cancel', visit.id), {
      cancellation_reason: reason
    }, {
      onSuccess: () => {
        // Success message will be shown by flash message component
      }
    })
  }
}

const markNoShow = (visit) => {
  if (confirm('¿Marcar como no asistió?')) {
    router.patch(route('visits.no-show', visit.id), {}, {
      onSuccess: () => {
        // Success message will be shown by flash message component
      }
    })
  }
}
</script>