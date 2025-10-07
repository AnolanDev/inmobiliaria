<template>
  <Head title="Proyectos" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Proyectos
        </h2>
        <Link
          :href="route('projects.create')"
          class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring focus:ring-green-300 disabled:opacity-25 transition ease-in-out duration-150"
        >
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
          </svg>
          Nuevo Proyecto
        </Link>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Filters and Search -->
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-6">
          <div class="p-6 border-b border-gray-200">
            <!-- Primera fila: Búsqueda y botones de vista -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4">
              <!-- Search -->
              <div class="relative flex-1 max-w-md">
                <input
                  v-model="search"
                  type="text"
                  placeholder="Buscar proyectos..."
                  class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500"
                  @input="performSearch"
                />
                <svg class="absolute left-3 top-2.5 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
              </div>

              <!-- View Toggle -->
              <div class="flex items-center space-x-2 flex-shrink-0">
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

            <!-- Segunda fila: Filtros -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-3">
              <!-- Type Filter -->
              <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Tipo</label>
                <select
                  v-model="selectedType"
                  @change="applyFilters"
                  class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-green-500 focus:border-green-500"
                >
                  <option value="">Todos</option>
                  <option v-for="(label, value) in types" :key="value" :value="value">
                    {{ label }}
                  </option>
                </select>
              </div>

              <!-- Status Filter -->
              <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Estado</label>
                <select
                  v-model="selectedStatus"
                  @change="applyFilters"
                  class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-green-500 focus:border-green-500"
                >
                  <option value="">Todos</option>
                  <option v-for="(label, value) in statuses" :key="value" :value="value">
                    {{ label }}
                  </option>
                </select>
              </div>

              <!-- State Filter -->
              <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Departamento</label>
                <select
                  v-model="selectedState"
                  @change="applyFilters"
                  class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-green-500 focus:border-green-500"
                >
                  <option value="">Todos</option>
                  <option v-for="state in states" :key="state" :value="state">
                    {{ state }}
                  </option>
                </select>
              </div>

              <!-- City Filter -->
              <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Ciudad</label>
                <select
                  v-model="selectedCity"
                  @change="applyFilters"
                  class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-green-500 focus:border-green-500"
                >
                  <option value="">Todas</option>
                  <option v-for="city in cities" :key="city" :value="city">
                    {{ city }}
                  </option>
                </select>
              </div>

              <!-- Clear Filters -->
              <div class="flex items-end">
                <button
                  v-if="hasFilters"
                  @click="clearFilters"
                  class="w-full px-3 py-2 text-sm text-gray-500 hover:text-gray-700 hover:bg-gray-50 border border-gray-300 rounded-md transition-colors"
                >
                  Limpiar filtros
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Projects Display -->
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <!-- Sort View (Drag & Drop) -->
          <div v-if="currentView === 'sort'">
            <DraggableProjectTable 
              :projects="projects.data" 
              @order-updated="handleOrderUpdated"
            />
          </div>

          <!-- Table View -->
          <div v-if="currentView === 'table'" class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Proyecto
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
                <tr v-for="project in projects.data" :key="project.id" class="hover:bg-gray-50">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div class="flex-shrink-0 h-16 w-16">
                        <img 
                          :src="project.cover_image_url" 
                          :alt="project.name"
                          class="h-16 w-16 rounded-lg object-cover"
                        />
                      </div>
                      <div class="ml-4">
                        <div class="text-sm font-medium text-gray-900">
                          {{ project.name }}
                        </div>
                        <div class="text-sm text-gray-500" v-if="project.description">
                          {{ truncateText(project.description, 50) }}
                        </div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span :class="['inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium', getTypeColor(project.type)]">
                      {{ project.type }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span :class="['inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium', getStatusColor(project.status)]">
                      {{ project.status }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ project.properties_count || 0 }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                    <Link
                      :href="route('projects.show', project.id)"
                      class="text-green-600 hover:text-green-900"
                    >
                      Ver
                    </Link>
                    <Link
                      :href="route('projects.edit', project.id)"
                      class="text-indigo-600 hover:text-indigo-900"
                    >
                      Editar
                    </Link>
                    <button
                      @click="confirmDelete(project)"
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
                v-for="project in projects.data"
                :key="project.id"
                :href="route('projects.show', project.id)"
                class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-lg hover:scale-[1.02] transition-all duration-300 cursor-pointer group"
              >
                <!-- Image -->
                <div class="aspect-video relative overflow-hidden">
                  <img
                    :src="project.cover_image_url"
                    :alt="project.name"
                    class="w-full h-full object-cover"
                  />
                  <div class="absolute top-4 right-4 flex space-x-2">
                    <span :class="['inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium', getTypeColor(project.type)]">
                      {{ project.type }}
                    </span>
                  </div>
                  <div class="absolute bottom-4 left-4">
                    <span :class="['inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium', getStatusColor(project.status)]">
                      {{ project.status }}
                    </span>
                  </div>
                </div>

                <!-- Content -->
                <div class="p-6">
                  <h3 class="text-lg font-semibold text-gray-900 mb-2">
                    {{ project.name }}
                  </h3>
                  <p v-if="project.description" class="text-gray-600 text-sm mb-4">
                    {{ truncateText(project.description, 100) }}
                  </p>
                  
                  <!-- Stats -->
                  <!-- <div class="flex items-center text-sm text-gray-500 mb-4">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-2m-2 0H7m10 0v-2c0-.553-.447-1-1-1s-1 .447-1 1v2m1-10V9a2 2 0 00-2-2M9 7h3M9 11h3M9 15h3"/>
                    </svg>
                    {{ project.properties_count || 0 }} propiedades
                  </div> -->

                  <!-- Actions -->
                  <div class="flex space-x-2">
                    <span class="flex-1 inline-flex justify-center items-center px-3 py-2 text-sm font-medium text-gray-600 group-hover:text-green-600 transition-colors">
                      Ver detalles
                    </span>
                    <Link
                      :href="route('projects.edit', project.id)"
                      @click.stop
                      class="inline-flex items-center p-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                      </svg>
                    </Link>
                    <button
                      @click.stop="confirmDelete(project)"
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
          <div v-if="projects.data.length === 0" class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No hay proyectos</h3>
            <p class="mt-1 text-sm text-gray-500">Comienza creando un nuevo proyecto.</p>
            <div class="mt-6">
              <Link
                :href="route('projects.create')"
                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
              >
                <svg class="-ml-1 mr-2 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
                </svg>
                Nuevo Proyecto
              </Link>
            </div>
          </div>
        </div>

        <!-- Pagination -->
        <div v-if="projects.links && projects.links.length > 3" class="mt-6 flex justify-center">
          <nav class="flex items-center space-x-2">
            <component
              v-for="(link, index) in projects.links"
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
            <h3 class="text-lg font-medium text-gray-900">Eliminar proyecto</h3>
            <p class="mt-2 text-sm text-gray-500">
              ¿Estás seguro de que deseas eliminar "{{ projectToDelete?.name }}"? Esta acción no se puede deshacer y se eliminarán todos los archivos asociados.
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
            @click="deleteProject"
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
import ToggleView from '@/Components/ToggleView.vue'
import Modal from '@/Components/Modal.vue'
import DraggableProjectTable from '@/Components/Projects/DraggableProjectTable.vue'
import { debounce } from 'lodash'

const props = defineProps({
  projects: Object,
  filters: Object,
  types: Object,
  statuses: Object,
  states: {
    type: Array,
    default: () => []
  },
  cities: {
    type: Array,
    default: () => []
  }
})

// State
const currentView = ref('cards')
const search = ref(props.filters.search || '')
const selectedType = ref(props.filters.type || '')
const selectedStatus = ref(props.filters.status || '')
const selectedState = ref(props.filters.state || '')
const selectedCity = ref(props.filters.city || '')
const showDeleteModal = ref(false)
const projectToDelete = ref(null)

// Computed
const hasFilters = computed(() => {
  return search.value || selectedType.value || selectedStatus.value || selectedState.value || selectedCity.value
})

// Methods
const performSearch = debounce(() => {
  applyFilters()
}, 300)

const applyFilters = () => {
  router.get(route('projects.index'), {
    search: search.value,
    type: selectedType.value,
    status: selectedStatus.value,
    state: selectedState.value,
    city: selectedCity.value
  }, {
    preserveState: true,
    replace: true
  })
}

const clearFilters = () => {
  search.value = ''
  selectedType.value = ''
  selectedStatus.value = ''
  selectedState.value = ''
  selectedCity.value = ''
  router.get(route('projects.index'))
}

const getTypeColor = (type) => {
  const colors = {
    'Campestres': 'bg-green-100 text-green-800',
    'Urbanos': 'bg-green-100 text-green-800',
    'Turísticos': 'bg-purple-100 text-purple-800'
  }
  return colors[type] || 'bg-gray-100 text-gray-800'
}

const getStatusColor = (status) => {
  const colors = {
    'Vendido': 'bg-red-100 text-red-800',
    'Disponible': 'bg-green-100 text-green-800',
    'Reservado': 'bg-yellow-100 text-yellow-800'
  }
  return colors[status] || 'bg-gray-100 text-gray-800'
}

const truncateText = (text, length) => {
  if (!text) return ''
  return text.length > length ? text.substring(0, length) + '...' : text
}

const confirmDelete = (project) => {
  projectToDelete.value = project
  showDeleteModal.value = true
}

const deleteProject = () => {
  if (projectToDelete.value) {
    router.delete(route('projects.destroy', projectToDelete.value.id), {
      onSuccess: () => {
        showDeleteModal.value = false
        projectToDelete.value = null
      }
    })
  }
}

const handleOrderUpdated = () => {
  // Refresh the page to show updated order
  router.reload({ only: ['projects'] })
}
</script>