<template>
  <Head title="Agentes" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <div>
          <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Agentes
          </h2>
          <p class="text-gray-500 text-sm mt-1">
            Gestiona tu equipo de agentes inmobiliarios
          </p>
        </div>
        <Link
          :href="route('agents.create')"
          class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none focus:border-green-900 focus:ring focus:ring-green-300 disabled:opacity-25 transition ease-in-out duration-150"
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
                    placeholder="Buscar agentes..."
                    class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-green-500 focus:border-green-500 sm:text-sm"
                  />
                </div>
              </div>

              <!-- Type Filter -->
              <div class="w-full sm:w-40">
                <select
                  v-model="filters.type"
                  @change="applyFilters"
                  class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm rounded-md"
                >
                  <option value="">Todos los tipos</option>
                  <option value="Interno">Interno</option>
                  <option value="Externo">Externo</option>
                </select>
              </div>

              <!-- Status Filter -->
              <div class="w-full sm:w-40">
                <select
                  v-model="filters.is_active"
                  @change="applyFilters"
                  class="block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-green-500 focus:border-green-500 sm:text-sm rounded-md"
                >
                  <option value="">Todos los estados</option>
                  <option value="1">Activos</option>
                  <option value="0">Inactivos</option>
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
                  @click="viewMode = 'table'"
                  :class="[
                    'relative -ml-px inline-flex items-center px-4 py-2 rounded-r-md border text-sm font-medium focus:z-10 focus:outline-none focus:ring-1 focus:ring-green-500 focus:border-green-500',
                    viewMode === 'table'
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
            <span class="font-medium">{{ agents.total }}</span> 
            {{ agents.total === 1 ? 'agente encontrado' : 'agentes encontrados' }}
          </p>
        </div>

        <!-- Agents Grid View -->
        <div v-if="viewMode === 'grid'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
          <div
            v-for="agent in agents.data"
            :key="agent.id"
            class="bg-white rounded-lg shadow-sm overflow-hidden hover:shadow-md transition-shadow duration-200 cursor-pointer"
            @click="goToAgent(agent.id)"
          >
            <!-- Profile Picture -->
            <div class="aspect-square relative">
              <img
                :src="agent.profile_picture_url"
                :alt="agent.name"
                class="w-full h-full object-cover"
              />
              
              <!-- Status Badge -->
              <div class="absolute top-3 right-3">
                <span :class="[
                  'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                  agent.is_active 
                    ? 'bg-green-100 text-green-800' 
                    : 'bg-red-100 text-red-800'
                ]">
                  {{ agent.is_active ? 'Activo' : 'Inactivo' }}
                </span>
              </div>

              <!-- Type Badge -->
              <div class="absolute top-3 left-3">
                <span :class="[
                  'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                  agent.type === 'Interno' 
                    ? 'bg-blue-100 text-blue-800' 
                    : 'bg-purple-100 text-purple-800'
                ]">
                  {{ agent.type }}
                </span>
              </div>

              <!-- Properties Count -->
              <div v-if="agent.properties && agent.properties.length > 0" class="absolute bottom-3 right-3">
                <div class="bg-black bg-opacity-50 text-white px-2 py-1 rounded text-xs">
                  <svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-2m-2 0H7m10 0v-2c0-.553-.447-1-1-1s-1 .447-1 1v2m1-10V9a2 2 0 00-2-2M9 7h3M9 11h3M9 15h3"/>
                  </svg>
                  {{ agent.properties.length }}
                </div>
              </div>
            </div>

            <!-- Agent Info -->
            <div class="p-4">
              <div class="flex items-start justify-between mb-2">
                <h3 class="text-lg font-semibold text-gray-900 line-clamp-1">
                  {{ agent.name }}
                </h3>
              </div>

              <div class="space-y-2">
                <div class="flex items-center text-sm text-gray-600">
                  <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                  </svg>
                  <span class="truncate">{{ agent.email }}</span>
                </div>

                <div class="flex items-center text-sm text-gray-600">
                  <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                  </svg>
                  <span>{{ agent.phone }}</span>
                </div>
              </div>

              <div class="mt-4">
                <button
                  @click.stop="goToAgent(agent.id)"
                  class="w-full inline-flex justify-center items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-green-700 bg-green-100 hover:bg-green-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                >
                  Ver detalles
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Agents Table View -->
        <div v-else class="bg-white shadow-sm rounded-lg overflow-hidden">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
              <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Agente
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Contacto
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Tipo
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Propiedades
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Estado
                </th>
                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                  Acciones
                </th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <tr
                v-for="agent in agents.data"
                :key="agent.id"
                class="hover:bg-gray-50 cursor-pointer"
                @click="goToAgent(agent.id)"
              >
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div class="flex-shrink-0 h-10 w-10">
                      <img
                        class="h-10 w-10 rounded-full object-cover"
                        :src="agent.profile_picture_url"
                        :alt="agent.name"
                      />
                    </div>
                    <div class="ml-4">
                      <div class="text-sm font-medium text-gray-900">
                        {{ agent.name }}
                      </div>
                    </div>
                  </div>
                </td>

                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="text-sm text-gray-900">{{ agent.email }}</div>
                  <div class="text-sm text-gray-500">{{ agent.phone }}</div>
                </td>

                <td class="px-6 py-4 whitespace-nowrap">
                  <span :class="[
                    'inline-flex px-2 py-1 text-xs font-semibold rounded-full',
                    agent.type === 'Interno' 
                      ? 'bg-blue-100 text-blue-800' 
                      : 'bg-purple-100 text-purple-800'
                  ]">
                    {{ agent.type }}
                  </span>
                </td>

                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                  {{ agent.properties ? agent.properties.length : 0 }}
                </td>

                <td class="px-6 py-4 whitespace-nowrap">
                  <span :class="[
                    'inline-flex px-2 py-1 text-xs font-semibold rounded-full',
                    agent.is_active 
                      ? 'bg-green-100 text-green-800' 
                      : 'bg-red-100 text-red-800'
                  ]">
                    {{ agent.is_active ? 'Activo' : 'Inactivo' }}
                  </span>
                </td>

                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                  <div class="flex items-center justify-end space-x-2">
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
                      @click="toggleStatus(agent)"
                      :class="[
                        'inline-flex items-center px-2 py-1 text-xs font-medium rounded-full transition-colors duration-200',
                        agent.is_active
                          ? 'text-red-700 bg-red-100 hover:bg-red-200 border border-red-300'
                          : 'text-green-700 bg-green-100 hover:bg-green-200 border border-green-300'
                      ]"
                    >
                      {{ agent.is_active ? 'Desactivar' : 'Activar' }}
                    </button>
                    <button
                      @click="confirmDelete(agent)"
                      class="text-red-600 hover:text-red-900"
                    >
                      Eliminar
                    </button>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Empty State -->
        <div v-if="agents.data.length === 0" class="text-center py-12">
          <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
          </svg>
          <h3 class="mt-2 text-sm font-medium text-gray-900">No hay agentes</h3>
          <p class="mt-1 text-sm text-gray-500">Comienza agregando un nuevo agente a tu equipo.</p>
          <div class="mt-6">
            <Link
              :href="route('agents.create')"
              class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
            >
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
              </svg>
              Nuevo Agente
            </Link>
          </div>
        </div>

        <!-- Pagination -->
        <div v-if="agents.data.length > 0" class="mt-8">
          <nav class="flex items-center justify-between border-t border-gray-200 px-4 sm:px-0">
            <div class="flex w-0 flex-1">
              <Link
                v-if="agents.prev_page_url"
                :href="agents.prev_page_url"
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
                Mostrando {{ agents.from }} a {{ agents.to }} de {{ agents.total }} resultados
              </span>
            </div>
            <div class="flex w-0 flex-1 justify-end">
              <Link
                v-if="agents.next_page_url"
                :href="agents.next_page_url"
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
          <h3 class="text-lg font-medium text-gray-900 mt-5">Eliminar Agente</h3>
          <div class="mt-2 px-7 py-3">
            <p class="text-sm text-gray-500">
              ¿Estás seguro de que quieres eliminar al agente <strong>{{ agentToDelete?.name }}</strong>? Esta acción no se puede deshacer.
            </p>
          </div>
          <div class="items-center px-4 py-3">
            <button
              @click="deleteAgent"
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
  agents: {
    type: Object,
    required: true
  },
  filters: {
    type: Object,
    default: () => ({})
  }
})

// Local state
const viewMode = ref('grid')
const showDeleteModal = ref(false)
const agentToDelete = ref(null)

const filters = reactive({
  search: props.filters.search || '',
  type: props.filters.type || '',
  is_active: props.filters.is_active || ''
})

// Methods
const goToAgent = (agentId) => {
  router.visit(route('agents.show', agentId))
}

const applyFilters = () => {
  router.get(route('agents.index'), filters, {
    preserveState: true,
    replace: true
  })
}

const toggleStatus = (agent) => {
  router.patch(route('agents.toggle-status', agent.id), {}, {
    preserveScroll: true,
    onSuccess: () => {
      // The page will automatically refresh with updated data
    }
  })
}

const confirmDelete = (agent) => {
  agentToDelete.value = agent
  showDeleteModal.value = true
}

const cancelDelete = () => {
  agentToDelete.value = null
  showDeleteModal.value = false
}

const deleteAgent = () => {
  if (agentToDelete.value) {
    router.delete(route('agents.destroy', agentToDelete.value.id), {
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
  localStorage.setItem('agents-view-mode', newMode)
})

// Initialize view mode from localStorage
if (localStorage.getItem('agents-view-mode')) {
  viewMode.value = localStorage.getItem('agents-view-mode')
}
</script>