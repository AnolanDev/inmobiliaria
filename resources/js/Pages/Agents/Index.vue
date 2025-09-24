<template>
  <Head title="Agentes" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Agentes
        </h2>
        <Link
          :href="route('agents.create')"
          class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring focus:ring-green-300 disabled:opacity-25 transition ease-in-out duration-150"
        >
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
          </svg>
          Nuevo Agente
        </Link>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Filters and Search -->
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-6">
          <div class="p-6 border-b border-gray-200">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
              <div class="flex flex-col md:flex-row md:items-center space-y-4 md:space-y-0 md:space-x-4">
                <!-- Search -->
                <div class="relative">
                  <input
                    v-model="search"
                    type="text"
                    placeholder="Buscar agentes..."
                    class="w-full md:w-64 pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500"
                    @input="performSearch"
                  />
                  <svg class="absolute left-3 top-2.5 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                  </svg>
                </div>

                <!-- Type Filter -->
                <select
                  v-model="selectedType"
                  @change="applyFilters"
                  class="border border-gray-300 rounded-md px-3 py-2 focus:ring-green-500 focus:border-green-500"
                >
                  <option value="">Todos los tipos</option>
                  <option v-for="(label, value) in types" :key="value" :value="value">
                    {{ label }}
                  </option>
                </select>

                <!-- Status Filter -->
                <select
                  v-model="selectedStatus"
                  @change="applyFilters"
                  class="border border-gray-300 rounded-md px-3 py-2 focus:ring-green-500 focus:border-green-500"
                >
                  <option value="">Todos los estados</option>
                  <option v-for="(label, value) in statuses" :key="value" :value="value">
                    {{ label }}
                  </option>
                </select>

                <!-- Clear Filters -->
                <button
                  v-if="hasFilters"
                  @click="clearFilters"
                  class="text-sm text-gray-500 hover:text-gray-700"
                >
                  Limpiar filtros
                </button>
              </div>

              <!-- View Toggle -->
              <div class="flex items-center space-x-2">
                <button
                  @click="currentView = 'cards'"
                  :class="[
                    'px-3 py-2 text-sm font-medium rounded-md transition-colors',
                    currentView === 'cards'
                      ? 'bg-green-600 text-white'
                      : 'text-gray-500 hover:text-gray-700'
                  ]"
                >
                  Tarjetas
                </button>
                <button
                  @click="currentView = 'table'"
                  :class="[
                    'px-3 py-2 text-sm font-medium rounded-md transition-colors',
                    currentView === 'table'
                      ? 'bg-green-600 text-white'
                      : 'text-gray-500 hover:text-gray-700'
                  ]"
                >
                  Tabla
                </button>
                <button
                  @click="currentView = 'sort'"
                  :class="[
                    'px-3 py-2 text-sm font-medium rounded-md transition-colors',
                    currentView === 'sort'
                      ? 'bg-green-600 text-white'
                      : 'text-gray-500 hover:text-gray-700'
                  ]"
                >
                  Ordenar
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Agents Display -->
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <!-- Sort View (Drag & Drop) -->
          <div v-if="currentView === 'sort'">
            <DraggableAgentTable 
              :agents="agents.data" 
              @order-updated="handleOrderUpdated"
            />
          </div>

          <!-- Table View -->
          <div v-else-if="currentView === 'table'" class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Agente
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Tipo
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Estado
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Propiedades
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Acciones
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="agent in agents.data" :key="agent.id" class="hover:bg-gray-50">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div class="flex-shrink-0 h-16 w-16">
                        <img 
                          :src="agent.profile_picture_url" 
                          :alt="agent.name"
                          class="h-16 w-16 rounded-full object-cover"
                        />
                      </div>
                      <div class="ml-4">
                        <div class="text-sm font-medium text-gray-900">
                          {{ agent.name }}
                        </div>
                        <div class="text-sm text-gray-500" v-if="agent.email">
                          {{ agent.email }}
                        </div>
                        <div class="text-sm text-gray-500" v-if="agent.phone">
                          {{ agent.phone }}
                        </div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span :class="['inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium', getTypeColor(agent.type)]">
                      {{ agent.type }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span :class="['inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium', getStatusColor(agent.is_active)]">
                      {{ agent.is_active ? 'Activo' : 'Inactivo' }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ agent.properties_count || 0 }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                    <Link
                      :href="route('agents.show', agent.id)"
                      class="text-green-600 hover:text-green-900"
                    >
                      Ver
                    </Link>
                    <Link
                      :href="route('agents.edit', agent.id)"
                      class="text-indigo-600 hover:text-indigo-900"
                    >
                      Editar
                    </Link>
                    <button
                      @click="confirmDelete(agent)"
                      class="text-red-600 hover:text-red-900"
                    >
                      Eliminar
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Cards View -->
          <div v-else class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              <Link
                v-for="agent in agents.data"
                :key="agent.id"
                :href="route('agents.show', agent.id)"
                class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-lg hover:scale-[1.02] transition-all duration-300 cursor-pointer group"
              >
                <!-- Image -->
                <div class="aspect-square relative overflow-hidden">
                  <img
                    :src="agent.profile_picture_url"
                    :alt="agent.name"
                    class="w-full h-full object-cover"
                  />
                  <div class="absolute top-4 right-4 flex space-x-2">
                    <span :class="['inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium', getTypeColor(agent.type)]">
                      {{ agent.type }}
                    </span>
                  </div>
                  <div class="absolute bottom-4 left-4">
                    <span :class="['inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium', getStatusColor(agent.is_active)]">
                      {{ agent.is_active ? 'Activo' : 'Inactivo' }}
                    </span>
                  </div>
                </div>

                <!-- Content -->
                <div class="p-6">
                  <h3 class="text-lg font-semibold text-gray-900 mb-2">
                    {{ agent.name }}
                  </h3>
                  <p v-if="agent.bio" class="text-gray-600 text-sm mb-4">
                    {{ truncateText(agent.bio, 100) }}
                  </p>
                  
                  <!-- Contact Info -->
                  <div class="text-sm text-gray-500 mb-4 space-y-1">
                    <div v-if="agent.email" class="flex items-center">
                      <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                      </svg>
                      {{ agent.email }}
                    </div>
                    <div v-if="agent.phone" class="flex items-center">
                      <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                      </svg>
                      {{ agent.phone }}
                    </div>
                  </div>

                  <!-- Stats -->
                  <div class="flex items-center text-sm text-gray-500 mb-4">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-2m-2 0H7m10 0v-2c0-.553-.447-1-1-1s-1 .447-1 1v2m1-10V9a2 2 0 00-2-2M9 7h3M9 11h3M9 15h3"/>
                    </svg>
                    {{ agent.properties_count || 0 }} propiedades
                  </div>

                  <!-- Actions -->
                  <div class="flex space-x-2">
                    <span class="flex-1 inline-flex justify-center items-center px-3 py-2 text-sm font-medium text-gray-600 group-hover:text-green-600 transition-colors">
                      Ver detalles
                    </span>
                    <Link
                      :href="route('agents.edit', agent.id)"
                      @click.stop
                      class="inline-flex items-center p-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                      </svg>
                    </Link>
                    <button
                      @click.stop="confirmDelete(agent)"
                      class="inline-flex items-center p-2 border border-red-300 shadow-sm text-sm font-medium rounded-md text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                      </svg>
                    </button>
                  </div>
                </div>
              </Link>
            </div>
          </div>

          <!-- Empty State -->
          <div v-if="agents.data.length === 0" class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No hay agentes</h3>
            <p class="mt-1 text-sm text-gray-500">Comienza creando un nuevo agente.</p>
            <div class="mt-6">
              <Link
                :href="route('agents.create')"
                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
              >
                <svg class="-ml-1 mr-2 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
                </svg>
                Nuevo Agente
              </Link>
            </div>
          </div>
        </div>

        <!-- Pagination -->
        <div v-if="agents.links && agents.links.length > 3" class="mt-6 flex justify-center">
          <nav class="flex items-center space-x-2">
            <component
              v-for="(link, index) in agents.links"
              :key="index"
              :is="link.url ? 'Link' : 'span'"
              :href="link.url"
              v-html="link.label"
              :class="[
                'px-3 py-2 text-sm font-medium rounded-md',
                link.active
                  ? 'bg-green-600 text-white'
                  : link.url
                  ? 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-300'
                  : 'bg-gray-100 text-gray-400 cursor-not-allowed'
              ]"
            />
          </nav>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <Modal :show="showDeleteModal" @close="showDeleteModal = false">
      <div class="p-6">
        <div class="flex items-center">
          <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
            <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
            </svg>
          </div>
          <div class="ml-4">
            <h3 class="text-lg font-medium text-gray-900">Eliminar agente</h3>
            <p class="mt-2 text-sm text-gray-500">
              ¿Estás seguro de que deseas eliminar "{{ agentToDelete?.name }}"? Esta acción no se puede deshacer y se eliminarán todos los archivos asociados.
            </p>
          </div>
        </div>
        <div class="mt-6 flex justify-end space-x-3">
          <button
            @click="showDeleteModal = false"
            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
          >
            Cancelar
          </button>
          <button
            @click="deleteAgent"
            class="px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
          >
            Eliminar
          </button>
        </div>
      </div>
    </Modal>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Modal from '@/Components/Modal.vue'
import DraggableAgentTable from '@/Components/Agents/DraggableAgentTable.vue'
import { debounce } from 'lodash'

const props = defineProps({
  agents: Object,
  filters: Object,
  types: Object,
  statuses: Object
})

// State
const currentView = ref('cards')
const search = ref(props.filters.search || '')
const selectedType = ref(props.filters.type || '')
const selectedStatus = ref(props.filters.status || '')
const showDeleteModal = ref(false)
const agentToDelete = ref(null)

// Computed
const hasFilters = computed(() => {
  return search.value || selectedType.value || selectedStatus.value
})

// Methods
const performSearch = debounce(() => {
  applyFilters()
}, 300)

const applyFilters = () => {
  router.get(route('agents.index'), {
    search: search.value,
    type: selectedType.value,
    status: selectedStatus.value
  }, {
    preserveState: true,
    replace: true
  })
}

const clearFilters = () => {
  search.value = ''
  selectedType.value = ''
  selectedStatus.value = ''
  router.get(route('agents.index'))
}

const getTypeColor = (type) => {
  const colors = {
    'Interno': 'bg-blue-100 text-blue-800',
    'Externo': 'bg-purple-100 text-purple-800'
  }
  return colors[type] || 'bg-gray-100 text-gray-800'
}

const getStatusColor = (isActive) => {
  return isActive 
    ? 'bg-green-100 text-green-800'
    : 'bg-red-100 text-red-800'
}

const truncateText = (text, length) => {
  if (!text) return ''
  return text.length > length ? text.substring(0, length) + '...' : text
}

const confirmDelete = (agent) => {
  agentToDelete.value = agent
  showDeleteModal.value = true
}

const deleteAgent = () => {
  if (agentToDelete.value) {
    router.delete(route('agents.destroy', agentToDelete.value.id), {
      onSuccess: () => {
        showDeleteModal.value = false
        agentToDelete.value = null
      }
    })
  }
}

const handleOrderUpdated = () => {
  // Refresh the page to show updated order
  router.reload({ only: ['agents'] })
}
</script>