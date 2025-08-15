<template>
  <Head title="Leads" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Gestión de Leads
        </h2>
        <Link
          :href="route('leads.create')"
          class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700"
        >
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
          </svg>
          Nuevo Lead
        </Link>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Filters -->
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-6">
          <div class="p-6">
            <div class="flex flex-wrap gap-4">
              <!-- Search -->
              <div class="relative">
                <input
                  v-model="search"
                  type="text"
                  placeholder="Buscar leads..."
                  class="w-64 pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500"
                  @input="performSearch"
                />
                <svg class="absolute left-3 top-2.5 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
              </div>

              <!-- Status Filter -->
              <select v-model="selectedStatus" @change="applyFilters" class="border border-gray-300 rounded-md px-3 py-2">
                <option value="">Todos los estados</option>
                <option v-for="(label, value) in statuses" :key="value" :value="value">{{ label }}</option>
              </select>

              <!-- Source Filter -->
              <select v-model="selectedSource" @change="applyFilters" class="border border-gray-300 rounded-md px-3 py-2">
                <option value="">Todas las fuentes</option>
                <option v-for="(label, value) in sources" :key="value" :value="value">{{ label }}</option>
              </select>

              <!-- Priority Filter -->
              <select v-model="selectedPriority" @change="applyFilters" class="border border-gray-300 rounded-md px-3 py-2">
                <option value="">Todas las prioridades</option>
                <option v-for="(label, value) in priorities" :key="value" :value="value">{{ label }}</option>
              </select>

              <button v-if="hasFilters" @click="clearFilters" class="text-sm text-gray-500 hover:text-gray-700">
                Limpiar filtros
              </button>
            </div>
          </div>
        </div>

        <!-- Leads Table -->
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <div v-if="leads.data.length > 0" class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lead</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fuente</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prioridad</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Agente</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Campaña</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="lead in leads.data" :key="lead.id" class="hover:bg-gray-50">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div>
                      <div class="text-sm font-medium text-gray-900">{{ lead.full_name }}</div>
                      <div class="text-sm text-gray-500">{{ lead.email }}</div>
                      <div v-if="lead.phone" class="text-sm text-gray-500">{{ lead.phone }}</div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span 
                      :class="getStatusColor(lead.status)" 
                      class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                      :style="getStatusStyle(lead.status)"
                    >
                      {{ lead.formatted_status }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span 
                      :class="getSourceColor(lead.source)" 
                      class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                      :style="getSourceStyle(lead.source)"
                    >
                      {{ lead.formatted_source }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span 
                      :class="getPriorityColor(lead.priority)" 
                      class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                      :style="getPriorityStyle(lead.priority)"
                    >
                      {{ lead.formatted_priority }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ lead.assigned_agent?.name || 'Sin asignar' }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    <Link v-if="lead.campaign" :href="route('campaigns.show', lead.campaign.id)" class="text-green-600 hover:text-green-900">
                      {{ lead.campaign.name }}
                    </Link>
                    <span v-else class="text-gray-500">Sin campaña</span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                    <div class="flex items-center space-x-2">
                      <Link :href="route('leads.show', lead.id)" class="text-green-600 hover:text-green-900">Ver</Link>
                      <Link :href="route('leads.edit', lead.id)" class="text-indigo-600 hover:text-indigo-900">Editar</Link>
                      
                      <!-- Quick Status Actions -->
                      <div class="relative">
                        <button @click="toggleStatusMenu(lead.id)" class="text-green-600 hover:text-green-900">
                          Estado
                          <svg class="w-3 h-3 inline ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                          </svg>
                        </button>
                        
                        <div v-if="openStatusMenu === lead.id" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-10 border">
                          <div class="py-1">
                            <button 
                              v-if="lead.status !== 'contacted'"
                              @click="updateLeadStatus(lead, 'contacted')"
                              class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                            >
                              Marcar como Contactado
                            </button>
                            <button 
                              v-if="lead.status === 'contacted' && lead.status !== 'qualified'"
                              @click="updateLeadStatus(lead, 'qualified')"
                              class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                            >
                              Marcar como Calificado
                            </button>
                            <button 
                              v-if="lead.status !== 'lost' && lead.status !== 'converted'"
                              @click="updateLeadStatus(lead, 'lost')"
                              class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                            >
                              Marcar como Perdido
                            </button>
                            <button 
                              v-if="lead.status === 'qualified' && lead.status !== 'converted'"
                              @click="updateLeadStatus(lead, 'converted')"
                              class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"
                            >
                              Marcar como Convertido
                            </button>
                          </div>
                        </div>
                      </div>
                      
                      <button @click="confirmDelete(lead)" class="text-red-600 hover:text-red-900">Eliminar</button>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Empty State -->
          <div v-else class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No hay leads</h3>
            <p class="mt-1 text-sm text-gray-500">Comienza agregando un nuevo lead.</p>
            <div class="mt-6">
              <Link :href="route('leads.create')" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
                </svg>
                Nuevo Lead
              </Link>
            </div>
          </div>
        </div>

        <!-- Pagination -->
        <div v-if="leads.links && leads.links.length > 3" class="mt-6 flex justify-center">
          <nav class="flex items-center space-x-2">
            <component
              v-for="(link, index) in leads.links"
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

    <!-- Delete Modal -->
    <Modal :show="showDeleteModal" @close="showDeleteModal = false">
      <div class="p-6">
        <div class="flex items-center">
          <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
            <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
            </svg>
          </div>
          <div class="ml-4">
            <h3 class="text-lg font-medium text-gray-900">Eliminar lead</h3>
            <p class="mt-2 text-sm text-gray-500">
              ¿Estás seguro de que deseas eliminar este lead? Esta acción no se puede deshacer.
            </p>
          </div>
        </div>
        <div class="mt-6 flex justify-end space-x-3">
          <button @click="showDeleteModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
            Cancelar
          </button>
          <button @click="deleteLead" class="px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md hover:bg-red-700">
            Eliminar
          </button>
        </div>
      </div>
    </Modal>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Modal from '@/Components/Modal.vue'
import { debounce } from 'lodash'

const props = defineProps({
  leads: Object,
  filters: Object,
  statuses: Object,
  sources: Object,
  priorities: Object,
  campaigns: Object,
  agents: Object
})

// State
const search = ref(props.filters.search || '')
const selectedStatus = ref(props.filters.status || '')
const selectedSource = ref(props.filters.source || '')
const selectedPriority = ref(props.filters.priority || '')
const showDeleteModal = ref(false)
const leadToDelete = ref(null)
const openStatusMenu = ref(null)

// Methods for quick status actions
const toggleStatusMenu = (leadId) => {
  openStatusMenu.value = openStatusMenu.value === leadId ? null : leadId
}

const updateLeadStatus = (lead, status) => {
  router.patch(route('leads.update-status', lead.id), {
    status: status
  }, {
    preserveScroll: true,
    onSuccess: () => {
      openStatusMenu.value = null
    }
  })
}

// Computed
const hasFilters = computed(() => {
  return search.value || selectedStatus.value || selectedSource.value || selectedPriority.value
})

// Methods
const performSearch = debounce(() => {
  applyFilters()
}, 300)

const applyFilters = () => {
  router.get(route('leads.index'), {
    search: search.value,
    status: selectedStatus.value,
    source: selectedSource.value,
    priority: selectedPriority.value
  }, {
    preserveState: true,
    replace: true
  })
}

const clearFilters = () => {
  search.value = ''
  selectedStatus.value = ''
  selectedSource.value = ''
  selectedPriority.value = ''
  router.get(route('leads.index'))
}

const getStatusColor = (status) => {
  const colors = {
    'new': '',
    'contacted': '',
    'qualified': '',
    'converted': '',
    'lost': ''
  }
  return colors[status] || 'bg-gray-100 text-gray-800'
}

const getStatusStyle = (status) => {
  const styles = {
    'new': { backgroundColor: '#10b981', color: 'white' },       // Verde vibrante
    'contacted': { backgroundColor: '#eab308', color: 'white' },  // Amarillo vibrante
    'qualified': { backgroundColor: '#10b981', color: 'white' },  // Verde vibrante
    'converted': { backgroundColor: '#a855f7', color: 'white' },  // Púrpura intenso
    'lost': { backgroundColor: '#ef4444', color: 'white' }        // Rojo intenso
  }
  return styles[status] || { backgroundColor: '#6b7280', color: 'white' }
}

const getSourceColor = (source) => {
  const colors = {
    'website': '',
    'social': '',
    'campaign': '',
    'referral': '',
    'phone': '',
    'walk_in': ''
  }
  return colors[source] || 'bg-gray-100 text-gray-800'
}

const getSourceStyle = (source) => {
  const styles = {
    'website': { backgroundColor: '#10b981', color: 'white' },   // Verde vibrante
    'social': { backgroundColor: '#a855f7', color: 'white' },    // Púrpura intenso
    'campaign': { backgroundColor: '#10b981', color: 'white' },  // Verde vibrante
    'referral': { backgroundColor: '#eab308', color: 'white' },  // Amarillo vibrante
    'phone': { backgroundColor: '#6366f1', color: 'white' },     // Índigo
    'walk_in': { backgroundColor: '#6b7280', color: 'white' }     // Gris
  }
  return styles[source] || { backgroundColor: '#6b7280', color: 'white' }
}

const getPriorityColor = (priority) => {
  const colors = {
    'low': '',
    'medium': '',
    'high': ''
  }
  return colors[priority] || 'bg-gray-100 text-gray-800'
}

const getPriorityStyle = (priority) => {
  const styles = {
    'low': { backgroundColor: '#10b981', color: 'white' },     // Verde vibrante
    'medium': { backgroundColor: '#eab308', color: 'white' },  // Amarillo vibrante
    'high': { backgroundColor: '#ef4444', color: 'white' }     // Rojo intenso
  }
  return styles[priority] || { backgroundColor: '#6b7280', color: 'white' }
}

const confirmDelete = (lead) => {
  leadToDelete.value = lead
  showDeleteModal.value = true
}

const deleteLead = () => {
  if (leadToDelete.value) {
    router.delete(route('leads.destroy', leadToDelete.value.id), {
      onSuccess: () => {
        showDeleteModal.value = false
        leadToDelete.value = null
      }
    })
  }
}
</script>