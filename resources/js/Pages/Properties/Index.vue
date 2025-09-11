<template>
  <Head title="Propiedades" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <div>
          <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Propiedades
          </h2>
          <p class="text-gray-500 text-sm mt-1">
            Gestiona tu cartera inmobiliaria
          </p>
        </div>
        <Link
          :href="route('properties.create')"
          class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none focus:border-green-900 focus:ring focus:ring-green-300 disabled:opacity-25 transition ease-in-out duration-150"
        >
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
          </svg>
          Nueva Propiedad
        </Link>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Filters -->
        <div class="bg-white shadow-sm rounded-lg overflow-hidden mb-6">
          <div class="px-4 py-3 border-b border-gray-200">
            <div class="flex flex-col sm:flex-row sm:items-center gap-4">
              <!-- Search -->
              <div class="flex-1 min-w-0">
                <div class="relative">
                  <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                  </div>
                  <input
                    v-model="filters.search"
                    @input="debouncedFilter"
                    type="text"
                    placeholder="Buscar propiedades..."
                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-green-500 focus:border-green-500 sm:text-sm"
                  />
                </div>
              </div>

              <!-- Project Filter -->
              <div class="w-full sm:w-48">
                <select
                  v-model="filters.project_id"
                  @change="applyFilters"
                  class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm rounded-md"
                >
                  <option value="">Todos los proyectos</option>
                  <option v-for="project in projects" :key="project.id" :value="project.id">
                    {{ project.name }}
                  </option>
                </select>
              </div>

              <!-- View Toggle -->
              <div class="flex rounded-md shadow-sm">
                <button
                  @click="viewMode = 'grid'"
                  :class="[
                    'relative inline-flex items-center px-4 py-2 rounded-l-md border text-sm font-medium focus:z-10 focus:outline-none focus:ring-1 focus:ring-green-500 focus:border-green-500',
                    viewMode === 'grid'
                      ? 'bg-green-600 border-green-600 text-white'
                      : 'bg-white border-gray-300 text-gray-700 hover:bg-gray-50'
                  ]"
                >
                  <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
                  </svg>
                </button>
                <button
                  @click="viewMode = 'list'"
                  :class="[
                    'relative -ml-px inline-flex items-center px-4 py-2 rounded-r-md border text-sm font-medium focus:z-10 focus:outline-none focus:ring-1 focus:ring-green-500 focus:border-green-500',
                    viewMode === 'list'
                      ? 'bg-green-600 border-green-600 text-white'
                      : 'bg-white border-gray-300 text-gray-700 hover:bg-gray-50'
                  ]"
                >
                  <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                  </svg>
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Results Summary -->
        <div class="mb-6">
          <p class="text-sm text-gray-600">
            <span class="font-medium">{{ properties.total }}</span> 
            {{ properties.total === 1 ? 'propiedad encontrada' : 'propiedades encontradas' }}
          </p>
        </div>

        <!-- Properties Grid View -->
        <div v-if="viewMode === 'grid'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div
            v-for="property in properties.data"
            :key="property.id"
            class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-shadow duration-200 cursor-pointer"
            @click="goToProperty(property.id)"
          >
            <!-- Property Image -->
            <div class="aspect-video relative">
              <img
                :src="property.cover_image_url"
                :alt="property.title"
                class="w-full h-full object-cover"
              />
              
              <!-- Badges -->
              <div class="absolute top-3 left-3 space-y-1">
                <span class="inline-block bg-green-600 text-white px-2 py-1 rounded-full text-xs font-medium">
                  {{ property.type === 'sale' ? 'Venta' : 'Alquiler' }}
                </span>
                <span 
                  :class="['block px-2 py-1 rounded-full text-xs font-medium', getStatusColor(property.status)]"
                  :style="getStatusStyle(property.status)"
                >
                  {{ getStatusName(property.status) }}
                </span>
              </div>

              <!-- Price -->
              <div class="absolute bottom-3 right-3">
                <div class="bg-black bg-opacity-75 text-white px-3 py-1 rounded-lg">
                  <span class="font-bold">
                    ${{ Number(property.price).toLocaleString() }}
                  </span>
                  <span v-if="property.type === 'rent'" class="text-xs">/mes</span>
                </div>
              </div>

              <!-- Gallery indicator -->
              <div v-if="property.gallery_urls && property.gallery_urls.length > 0" class="absolute top-3 right-3">
                <div class="bg-black bg-opacity-50 text-white px-2 py-1 rounded text-xs">
                  <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                  </svg>
                  {{ property.gallery_urls.length }}
                </div>
              </div>
            </div>

            <!-- Property Info -->
            <div class="p-6">
              <div class="flex items-start justify-between mb-2">
                <h3 class="text-lg font-semibold text-gray-900 line-clamp-2">
                  {{ property.title }}
                </h3>
              </div>

              <p class="text-sm text-gray-500 mb-3">
                {{ getCategoryName(property.category) }} en {{ property.city }}
              </p>

              <p class="text-sm text-gray-600 mb-4 line-clamp-2">
                {{ property.description }}
              </p>

              <!-- Features -->
              <div class="flex items-center space-x-4 text-sm text-gray-500 mb-4">
                <div v-if="property.bedrooms > 0" class="flex items-center">
                  <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"/>
                  </svg>
                  {{ property.bedrooms }} hab.
                </div>
                <div v-if="property.bathrooms > 0" class="flex items-center">
                  <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/>
                  </svg>
                  {{ property.bathrooms }} baños
                </div>
                <div class="flex items-center">
                  <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
                  </svg>
                  {{ property.area }} m²
                </div>
              </div>

              <!-- Project Info -->
              <div v-if="property.project" class="mb-3">
                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                  <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-2m-2 0H7m10 0v-2c0-.553-.447-1-1-1s-1 .447-1 1v2m1-10V9a2 2 0 00-2-2M9 7h3M9 11h3M9 15h3"/>
                  </svg>
                  {{ property.project.name }}
                </span>
              </div>

              <!-- Agent -->
              <div class="flex items-center justify-between">
                <div v-if="property.agent" class="flex items-center text-sm text-gray-600">
                  <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                  </svg>
                  {{ property.agent.name }}
                </div>
                <div class="flex items-center space-x-2">
                  <button
                    @click.stop="goToProperty(property.id)"
                    class="text-green-600 hover:text-green-800 text-sm font-medium"
                  >
                    Ver detalles →
                  </button>
                  <button
                    @click.stop="toggleStatus(property)"
                    :class="[
                      'inline-flex items-center px-2 py-1 text-xs font-medium rounded-full transition-colors duration-200',
                      getStatusToggleClass(property.status)
                    ]"
                  >
                    {{ getStatusToggleText(property.status) }}
                  </button>
                  <button
                    @click.stop="confirmDelete(property)"
                    class="text-red-600 hover:text-red-900 text-sm font-medium"
                  >
                    Eliminar
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Properties List View -->
        <div v-else class="bg-white shadow-sm rounded-lg overflow-hidden">
          <div class="min-w-full divide-y divide-gray-200">
            <div
              v-for="property in properties.data"
              :key="property.id"
              class="flex items-center p-6 hover:bg-gray-50 cursor-pointer transition-colors duration-200"
              @click="goToProperty(property.id)"
            >
              <!-- Property Image -->
              <div class="flex-shrink-0 w-24 h-16 mr-6">
                <img
                  :src="property.cover_image_url"
                  :alt="property.title"
                  class="w-full h-full object-cover rounded-md"
                />
              </div>

              <!-- Property Info -->
              <div class="flex-1 min-w-0">
                <div class="flex items-start justify-between">
                  <div class="flex-1">
                    <h3 class="text-lg font-semibold text-gray-900 truncate">
                      {{ property.title }}
                    </h3>
                    <p class="text-sm text-gray-500 mt-1">
                      {{ getCategoryName(property.category) }} • {{ property.city }}, {{ property.state }}
                    </p>
                    <p class="text-sm text-gray-600 mt-2 line-clamp-1">
                      {{ property.description }}
                    </p>
                    
                    <!-- Features -->
                    <div class="flex items-center space-x-4 text-sm text-gray-500 mt-3">
                      <div v-if="property.bedrooms > 0">{{ property.bedrooms }} hab.</div>
                      <div v-if="property.bathrooms > 0">{{ property.bathrooms }} baños</div>
                      <div>{{ property.area }} m²</div>
                      <div v-if="property.project" class="text-green-600">{{ property.project.name }}</div>
                    </div>
                  </div>

                  <!-- Price and Status -->
                  <div class="text-right ml-6">
                    <div class="text-xl font-bold text-gray-900 mb-2">
                      ${{ Number(property.price).toLocaleString() }}
                      <span v-if="property.type === 'rent'" class="text-sm font-normal text-gray-600">/mes</span>
                    </div>
                    
                    <div class="flex items-center space-x-2 mb-2">
                      <span class="inline-block bg-green-600 text-white px-2 py-1 rounded-full text-xs font-medium">
                        {{ property.type === 'sale' ? 'Venta' : 'Alquiler' }}
                      </span>
                      <span 
                        :class="['px-2 py-1 rounded-full text-xs font-medium', getStatusColor(property.status)]"
                        :style="getStatusStyle(property.status)"
                      >
                        {{ getStatusName(property.status) }}
                      </span>
                    </div>

                    <div v-if="property.agent" class="text-sm text-gray-600">
                      {{ property.agent.name }}
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-if="properties.data.length === 0" class="text-center py-12">
          <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-2m-2 0H7m10 0v-2c0-.553-.447-1-1-1s-1 .447-1 1v2m1-10V9a2 2 0 00-2-2M9 7h3M9 11h3M9 15h3"/>
          </svg>
          <h3 class="mt-2 text-sm font-medium text-gray-900">No hay propiedades</h3>
          <p class="mt-1 text-sm text-gray-500">Comienza agregando una nueva propiedad.</p>
          <div class="mt-6">
            <Link
              :href="route('properties.create')"
              class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
            >
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
              </svg>
              Nueva Propiedad
            </Link>
          </div>
        </div>

        <!-- Pagination -->
        <div v-if="properties.data.length > 0" class="mt-8">
          <nav class="flex items-center justify-between border-t border-gray-200 px-4 sm:px-0">
            <div class="flex w-0 flex-1">
              <Link
                v-if="properties.prev_page_url"
                :href="properties.prev_page_url"
                class="inline-flex items-center pt-4 pr-1 text-sm font-medium text-gray-500 hover:text-gray-700"
              >
                <svg class="mr-3 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
                Anterior
              </Link>
            </div>
            <div class="hidden md:flex">
              <span class="text-sm text-gray-700 pt-4">
                Mostrando {{ properties.from }} a {{ properties.to }} de {{ properties.total }} resultados
              </span>
            </div>
            <div class="flex w-0 flex-1 justify-end">
              <Link
                v-if="properties.next_page_url"
                :href="properties.next_page_url"
                class="inline-flex items-center pt-4 pl-1 text-sm font-medium text-gray-500 hover:text-gray-700"
              >
                Siguiente
                <svg class="ml-3 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
              </Link>
            </div>
          </nav>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div v-if="showDeleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
      <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3 text-center">
          <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
            <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
            </svg>
          </div>
          <h3 class="text-lg font-medium text-gray-900 mt-5">Eliminar Propiedad</h3>
          <div class="mt-2 px-7 py-3">
            <p class="text-sm text-gray-500">
              ¿Estás seguro de que quieres eliminar la propiedad <strong>{{ propertyToDelete?.title }}</strong>? Esta acción no se puede deshacer.
            </p>
          </div>
          <div class="items-center px-4 py-3">
            <button
              @click="deleteProperty"
              class="px-4 py-2 bg-red-500 text-white text-base font-medium rounded-md w-24 mr-2 hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-300"
            >
              Eliminar
            </button>
            <button
              @click="cancelDelete"
              class="px-4 py-2 bg-gray-500 text-white text-base font-medium rounded-md w-24 hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-gray-300"
            >
              Cancelar
            </button>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, reactive, watch } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { debounce } from 'lodash'

const props = defineProps({
  properties: {
    type: Object,
    required: true
  },
  projects: {
    type: Array,
    default: () => []
  },
  filters: {
    type: Object,
    default: () => ({})
  }
})

// Local state
const viewMode = ref('grid')
const showDeleteModal = ref(false)
const propertyToDelete = ref(null)

const filters = reactive({
  search: props.filters.search || '',
  project_id: props.filters.project_id || ''
})

// Methods
const getCategoryName = (category) => {
  const categories = {
    'house': 'Casa',
    'apartment': 'Apartamento',
    'office': 'Oficina',
    'land': 'Terreno',
    'commercial': 'Local Comercial'
  }
  return categories[category] || category
}

const getStatusName = (status) => {
  const statuses = {
    'available': 'Disponible',
    'sold': 'Vendida',
    'rented': 'Alquilada',
    'pending': 'Pendiente'
  }
  return statuses[status] || status
}

const getStatusColor = (status) => {
  const colors = {
    'available': '',       // Se maneja con estilos inline
    'sold': '',            // Se maneja con estilos inline
    'rented': '',          // Se maneja con estilos inline
    'pending': '',         // Se maneja con estilos inline
    'reserved': ''         // Se maneja con estilos inline
  }
  return colors[status] || 'bg-gray-100 text-gray-800'
}

const getStatusStyle = (status) => {
  const styles = {
    'available': { backgroundColor: '#10b981', color: 'white' },    // Verde vibrante
    'sold': { backgroundColor: '#ef4444', color: 'white' },         // Rojo intenso
    'rented': { backgroundColor: '#3b82f6', color: 'white' },       // Azul brillante
    'pending': { backgroundColor: '#eab308', color: 'white' },      // Amarillo vibrante
    'reserved': { backgroundColor: '#a855f7', color: 'white' }      // Púrpura intenso
  }
  return styles[status] || {}
}

const goToProperty = (propertyId) => {
  router.visit(route('properties.show', propertyId))
}

const applyFilters = () => {
  router.get(route('properties.index'), filters, {
    preserveState: true,
    replace: true
  })
}

// Property status management
const getStatusToggleClass = (status) => {
  const classes = {
    'available': 'text-red-700 bg-red-100 hover:bg-red-200 border border-red-300',
    'sold': 'text-green-700 bg-green-100 hover:bg-green-200 border border-green-300',
    'rented': 'text-green-700 bg-green-100 hover:bg-green-200 border border-green-300',
    'pending': 'text-green-700 bg-green-100 hover:bg-green-200 border border-green-300',
    'reserved': 'text-green-700 bg-green-100 hover:bg-green-200 border border-green-300'
  }
  return classes[status] || 'text-gray-700 bg-gray-100 hover:bg-gray-200 border border-gray-300'
}

const getStatusToggleText = (status) => {
  const texts = {
    'available': 'Marcar Vendida',
    'sold': 'Marcar Disponible',
    'rented': 'Marcar Disponible',
    'pending': 'Marcar Disponible',
    'reserved': 'Marcar Disponible'
  }
  return texts[status] || 'Cambiar Estado'
}

const toggleStatus = (property) => {
  const newStatus = property.status === 'available' ? 'sold' : 'available'
  
  // Enviar todos los datos requeridos con el nuevo status
  const formData = new FormData()
  formData.append('_method', 'PATCH')
  formData.append('title', property.title)
  formData.append('description', property.description)
  formData.append('price', property.price)
  formData.append('type', property.type)
  formData.append('category', property.category)
  formData.append('address', property.address)
  formData.append('city', property.city)
  formData.append('state', property.state)
  formData.append('bedrooms', property.bedrooms || 0)
  formData.append('bathrooms', property.bathrooms || 0)
  formData.append('area', property.area)
  formData.append('status', newStatus)
  
  if (property.project_id) {
    formData.append('project_id', property.project_id)
  }
  if (property.agent_id) {
    formData.append('agent_id', property.agent_id)
  }
  if (property.zip_code) {
    formData.append('zip_code', property.zip_code)
  }
  
  router.post(route('properties.update', property.id), formData, {
    preserveScroll: true,
    onSuccess: () => {
      // The page will automatically refresh with updated data
    },
    onError: (errors) => {
      console.log('Error updating status:', errors)
    }
  })
}

const confirmDelete = (property) => {
  propertyToDelete.value = property
  showDeleteModal.value = true
}

const cancelDelete = () => {
  propertyToDelete.value = null
  showDeleteModal.value = false
}

const deleteProperty = () => {
  if (propertyToDelete.value) {
    router.delete(route('properties.destroy', propertyToDelete.value.id), {
      onSuccess: () => {
        cancelDelete()
      }
    })
  }
}

// Debounced search
const debouncedFilter = debounce(applyFilters, 300)

// Watch for view mode preference persistence
watch(viewMode, (newMode) => {
  localStorage.setItem('properties-view-mode', newMode)
})

// Initialize view mode from localStorage
if (localStorage.getItem('properties-view-mode')) {
  viewMode.value = localStorage.getItem('properties-view-mode')
}
</script>