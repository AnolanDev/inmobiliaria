<template>
  <Head title="Campañas de Marketing" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Campañas de Marketing
        </h2>
        <Link
          :href="route('campaigns.create')"
          class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring focus:ring-green-300 disabled:opacity-25 transition ease-in-out duration-150"
        >
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
          </svg>
          Nueva Campaña
        </Link>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Filters -->
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-6">
          <div class="p-6 border-b border-gray-200">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
              <div class="flex flex-col md:flex-row md:items-center space-y-4 md:space-y-0 md:space-x-4">
                <!-- Search -->
                <div class="relative">
                  <input
                    v-model="search"
                    type="text"
                    placeholder="Buscar campañas..."
                    class="w-full md:w-64 pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:ring-green-500 focus:border-blue-500"
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
                  class="border border-gray-300 rounded-md px-3 py-2 focus:ring-green-500 focus:border-blue-500"
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
                  class="border border-gray-300 rounded-md px-3 py-2 focus:ring-green-500 focus:border-blue-500"
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
            </div>
          </div>
        </div>

        <!-- Campaigns Grid -->
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <div v-if="campaigns.data.length > 0" class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              <div
                v-for="campaign in campaigns.data"
                :key="campaign.id"
                class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-lg hover:scale-[1.02] transition-all duration-300"
              >
                <!-- Header -->
                <div class="p-6 border-b border-gray-100">
                  <div class="flex items-start justify-between">
                    <div class="flex-1">
                      <h3 class="text-lg font-semibold text-gray-900 mb-2">
                        {{ campaign.name }}
                      </h3>
                      <p v-if="campaign.description" class="text-gray-600 text-sm mb-3">
                        {{ truncateText(campaign.description, 100) }}
                      </p>
                    </div>
                    <div class="ml-4 flex flex-col items-end space-y-2">
                      <span :class="getStatusColor(campaign.status)" class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium">
                        {{ campaign.formatted_status }}
                      </span>
                      <span :class="getTypeColor(campaign.type)" class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium">
                        {{ campaign.formatted_type }}
                      </span>
                    </div>
                  </div>
                </div>

                <!-- Metrics -->
                <div class="p-6 bg-gray-50 border-b border-gray-100">
                  <div class="grid grid-cols-2 gap-4">
                    <div class="text-center">
                      <div class="text-2xl font-bold text-blue-600">{{ campaign.leads_count || 0 }}</div>
                      <div class="text-xs text-gray-500">Leads</div>
                    </div>
                    <div class="text-center">
                      <div class="text-2xl font-bold text-green-600">{{ campaign.conversion_rate || 0 }}%</div>
                      <div class="text-xs text-gray-500">Conversión</div>
                    </div>
                  </div>
                  
                  <div v-if="campaign.budget" class="mt-4">
                    <div class="flex justify-between items-center mb-1">
                      <span class="text-sm text-gray-600">Presupuesto</span>
                      <span class="text-sm font-medium">${{ formatCurrency(campaign.spent) }} / ${{ formatCurrency(campaign.budget) }}</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                      <div 
                        class="bg-green-600 h-2 rounded-full transition-all duration-300"
                        :style="{ width: getBudgetPercentage(campaign) + '%' }"
                      ></div>
                    </div>
                  </div>
                </div>

                <!-- Actions -->
                <div class="p-4 bg-gray-50">
                  <div class="flex space-x-2">
                    <Link
                      :href="route('campaigns.show', campaign.id)"
                      class="flex-1 inline-flex justify-center items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                    >
                      Ver
                    </Link>
                    <Link
                      :href="route('campaigns.edit', campaign.id)"
                      class="flex-1 inline-flex justify-center items-center px-3 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                    >
                      Editar
                    </Link>
                    <button
                      @click="confirmDelete(campaign)"
                      class="inline-flex items-center p-2 border border-red-300 shadow-sm text-sm font-medium rounded-md text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                      </svg>
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Empty State -->
          <div v-else class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No hay campañas</h3>
            <p class="mt-1 text-sm text-gray-500">Comienza creando una nueva campaña de marketing.</p>
            <div class="mt-6">
              <Link
                :href="route('campaigns.create')"
                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
              >
                <svg class="-ml-1 mr-2 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
                </svg>
                Nueva Campaña
              </Link>
            </div>
          </div>
        </div>

        <!-- Pagination -->
        <div v-if="campaigns.links && campaigns.links.length > 3" class="mt-6 flex justify-center">
          <nav class="flex items-center space-x-2">
            <component
              v-for="(link, index) in campaigns.links"
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
            <h3 class="text-lg font-medium text-gray-900">Eliminar campaña</h3>
            <p class="mt-2 text-sm text-gray-500">
              ¿Estás seguro de que deseas eliminar "{{ campaignToDelete?.name }}"? Esta acción no se puede deshacer.
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
            @click="deleteCampaign"
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
import { ref, computed } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Modal from '@/Components/Modal.vue'
import { debounce } from 'lodash'

const props = defineProps({
  campaigns: Object,
  filters: Object,
  types: Object,
  statuses: Object
})

// State
const search = ref(props.filters.search || '')
const selectedType = ref(props.filters.type || '')
const selectedStatus = ref(props.filters.status || '')
const showDeleteModal = ref(false)
const campaignToDelete = ref(null)

// Computed
const hasFilters = computed(() => {
  return search.value || selectedType.value || selectedStatus.value
})

// Methods
const performSearch = debounce(() => {
  applyFilters()
}, 300)

const applyFilters = () => {
  router.get(route('campaigns.index'), {
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
  router.get(route('campaigns.index'))
}

const getStatusColor = (status) => {
  const colors = {
    'draft': 'bg-gray-100 text-gray-800',
    'active': 'bg-green-100 text-green-800',
    'paused': 'bg-yellow-100 text-yellow-800',
    'completed': 'bg-green-100 text-green-800'
  }
  return colors[status] || 'bg-gray-100 text-gray-800'
}

const getTypeColor = (type) => {
  const colors = {
    'email': 'bg-green-100 text-green-800',
    'sms': 'bg-green-100 text-green-800',
    'social': 'bg-purple-100 text-purple-800',
    'digital_ads': 'bg-red-100 text-red-800',
    'print': 'bg-gray-100 text-gray-800',
    'event': 'bg-yellow-100 text-yellow-800'
  }
  return colors[type] || 'bg-gray-100 text-gray-800'
}

const truncateText = (text, length) => {
  if (!text) return ''
  return text.length > length ? text.substring(0, length) + '...' : text
}

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('es-CO').format(amount)
}

const getBudgetPercentage = (campaign) => {
  if (!campaign.budget || campaign.budget === 0) return 0
  return Math.min((campaign.spent / campaign.budget) * 100, 100)
}

const confirmDelete = (campaign) => {
  campaignToDelete.value = campaign
  showDeleteModal.value = true
}

const deleteCampaign = () => {
  if (campaignToDelete.value) {
    router.delete(route('campaigns.destroy', campaignToDelete.value.id), {
      onSuccess: () => {
        showDeleteModal.value = false
        campaignToDelete.value = null
      }
    })
  }
}
</script>