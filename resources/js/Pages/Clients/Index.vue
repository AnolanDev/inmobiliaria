<template>
  <AuthenticatedLayout title="Clientes">
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
          <div>
            <h1 class="text-3xl font-bold text-gray-900">Clientes</h1>
            <p class="text-gray-600 mt-2">Gestiona tu base de datos de clientes</p>
          </div>
          <Link
            :href="route('clients.create')"
            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium shadow-sm transition-colors inline-flex items-center gap-2"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Nuevo Cliente
          </Link>
        </div>

        <!-- Filters and Search -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <!-- Search -->
            <div class="lg:col-span-2">
              <label class="block text-sm font-medium text-gray-700 mb-1">Buscar</label>
              <input
                v-model="filters.search"
                type="text"
                placeholder="Nombre, teléfono, email o documento..."
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

            <!-- Interest Level Filter -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">Nivel de Interés</label>
              <select
                v-model="filters.interest_level"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
              >
                <option value="">Todos</option>
                <option v-for="(label, value) in interestLevels" :key="value" :value="value">
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
                Limpiar Filtros
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
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                </svg>
              </button>
            </div>

            <!-- Stats -->
            <div class="text-sm text-gray-600">
              Mostrando {{ clients.data.length }} de {{ clients.total }} clientes
            </div>
          </div>

          <!-- Export Options -->
          <div class="flex items-center gap-2">
            <button
              @click="exportExcel"
              class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md font-medium transition-colors inline-flex items-center gap-2"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
              </svg>
              Excel
            </button>
            <button
              @click="exportPdf"
              class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md font-medium transition-colors inline-flex items-center gap-2"
            >
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
              </svg>
              PDF
            </button>
          </div>
        </div>

        <!-- Cards View -->
        <div v-if="viewMode === 'cards'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
          <ClientCard
            v-for="client in clients.data"
            :key="client.id"
            :client="client"
            @view="viewClient"
            @edit="editClient"
            @delete="deleteClient"
          />
        </div>

        <!-- Table View -->
        <div v-else class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
          <ClientTable
            :clients="clients.data"
            @view="viewClient"
            @edit="editClient" 
            @delete="deleteClient"
          />
        </div>

        <!-- Pagination -->
        <div v-if="clients.data.length > 0" class="mt-8">
          <Pagination :links="clients.links" />
        </div>

        <!-- Empty State -->
        <div v-else class="text-center py-12">
          <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
          </svg>
          <h3 class="mt-2 text-sm font-medium text-gray-900">No hay clientes</h3>
          <p class="mt-1 text-sm text-gray-500">Comienza creando tu primer cliente.</p>
          <div class="mt-6">
            <Link
              :href="route('clients.create')"
              class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700"
            >
              <svg class="-ml-1 mr-2 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
              </svg>
              Nuevo Cliente
            </Link>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, reactive, watch } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import ClientCard from '@/Components/ClientCard.vue'
import ClientTable from '@/Components/ClientTable.vue'
import Pagination from '@/Components/Pagination.vue'

const props = defineProps({
  clients: {
    type: Object,
    required: true
  },
  filters: {
    type: Object,
    default: () => ({})
  },
  statuses: {
    type: Object,
    required: true
  },
  interestLevels: {
    type: Object,
    required: true
  }
})

// View mode
const viewMode = ref('cards')

// Filters
const filters = reactive({
  search: props.filters.search || '',
  status: props.filters.status || '',
  interest_level: props.filters.interest_level || '',
  date_from: props.filters.date_from || '',
  date_to: props.filters.date_to || '',
})

// Auto-apply filters when they change
watch(() => filters.search, (newValue) => {
  if (newValue.length >= 3 || newValue.length === 0) {
    applyFilters()
  }
}, { debounce: 500 })

// Auto-apply filters for status
watch(() => filters.status, () => {
  applyFilters()
})

// Auto-apply filters for interest level
watch(() => filters.interest_level, () => {
  applyFilters()
})

// Auto-apply filters for date range
watch(() => filters.date_from, () => {
  applyFilters()
})

watch(() => filters.date_to, () => {
  applyFilters()
})

const applyFilters = () => {
  const queryParams = {}
  
  Object.keys(filters).forEach(key => {
    if (filters[key]) {
      queryParams[key] = filters[key]
    }
  })

  router.get(route('clients.index'), queryParams, {
    preserveState: true,
    replace: true
  })
}

const clearFilters = () => {
  Object.keys(filters).forEach(key => {
    filters[key] = ''
  })
  router.get(route('clients.index'), {}, {
    preserveState: true,
    replace: true
  })
}

const viewClient = (client) => {
  router.get(route('clients.show', client.id))
}

const editClient = (client) => {
  router.get(route('clients.edit', client.id))
}

const deleteClient = (client) => {
  if (confirm(`¿Estás seguro de que deseas eliminar a ${client.name}?`)) {
    router.delete(route('clients.destroy', client.id), {
      onSuccess: () => {
        // Success message will be shown by flash message component
      }
    })
  }
}

const exportExcel = () => {
  window.location.href = route('clients.export.excel', filters)
}

const exportPdf = () => {
  window.location.href = route('clients.export.pdf', filters)
}
</script>