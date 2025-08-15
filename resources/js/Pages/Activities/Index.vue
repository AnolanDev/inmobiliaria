<template>
  <Head title="Actividades" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex justify-between items-center">
        <div>
          <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Gestión de Actividades
          </h2>
          <p class="text-sm text-gray-600 mt-1">
            Gestiona tareas, llamadas, reuniones y seguimiento de leads
          </p>
        </div>
        <Link
          :href="route('activities.create')"
          class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700"
        >
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
          </svg>
          Nueva Actividad
        </Link>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
          <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-6">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center">
                    <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                  </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">Pendientes</dt>
                    <dd class="text-2xl font-bold text-gray-900">{{ stats.total_pending }}</dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-6">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                    <svg class="w-4 h-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                  </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">Vencidas</dt>
                    <dd class="text-2xl font-bold text-gray-900">{{ stats.overdue }}</dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-6">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 0V5a2 2 0 012-2h4a2 2 0 012 2v2m-6 0h8"/>
                    </svg>
                  </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">Hoy</dt>
                    <dd class="text-2xl font-bold text-gray-900">{{ stats.due_today }}</dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-6">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                  </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">Completadas Hoy</dt>
                    <dd class="text-2xl font-bold text-gray-900">{{ stats.completed_today }}</dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>
        </div>

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
                    placeholder="Buscar actividades..."
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

                <!-- Priority Filter -->
                <select
                  v-model="selectedPriority"
                  @change="applyFilters"
                  class="border border-gray-300 rounded-md px-3 py-2 focus:ring-green-500 focus:border-green-500"
                >
                  <option value="">Todas las prioridades</option>
                  <option v-for="(label, value) in priorities" :key="value" :value="value">
                    {{ label }}
                  </option>
                </select>

                <!-- User Filter -->
                <select
                  v-model="selectedUser"
                  @change="applyFilters"
                  class="border border-gray-300 rounded-md px-3 py-2 focus:ring-green-500 focus:border-green-500"
                >
                  <option value="">Todos los usuarios</option>
                  <option v-for="user in users" :key="user.id" :value="user.id">
                    {{ user.name }}
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

              <!-- Quick Filters -->
              <div class="flex space-x-2">
                <button
                  @click="applyQuickFilter('my_activities')"
                  :class="[
                    'px-3 py-2 rounded-md text-sm font-medium',
                    filters.my_activities
                      ? 'bg-green-100 text-green-800'
                      : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                  ]"
                >
                  Mis actividades
                </button>
                <button
                  @click="applyQuickFilter('overdue')"
                  :class="[
                    'px-3 py-2 rounded-md text-sm font-medium',
                    filters.overdue
                      ? 'bg-red-100 text-red-800'
                      : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                  ]"
                >
                  Vencidas
                </button>
                <button
                  @click="applyQuickFilter('today')"
                  :class="[
                    'px-3 py-2 rounded-md text-sm font-medium',
                    filters.today
                      ? 'bg-green-100 text-green-800'
                      : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
                  ]"
                >
                  Hoy
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Activities Table -->
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <div v-if="activities.data.length > 0" class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actividad</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Prioridad</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Asignado</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Relacionado</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="activity in activities.data" :key="activity.id" class="hover:bg-gray-50">
                  <td class="px-6 py-4">
                    <div class="flex items-center">
                      <div class="flex-shrink-0 h-8 w-8">
                        <div 
                          :class="getActivityIconColor(activity.type)" 
                          class="h-8 w-8 rounded-full flex items-center justify-center"
                          :style="getActivityIconStyle(activity.type)"
                        >
                          <component :is="getActivityIcon(activity.type)" class="h-4 w-4" />
                        </div>
                      </div>
                      <div class="ml-4">
                        <div class="text-sm font-medium text-gray-900">{{ activity.subject }}</div>
                        <div v-if="activity.description" class="text-sm text-gray-500">
                          {{ truncateText(activity.description, 50) }}
                        </div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span 
                      :class="getTypeColor(activity.type)" 
                      class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                      :style="getTypeStyle(activity.type)"
                    >
                      {{ activity.formatted_type }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span 
                      :class="getStatusColor(activity.status)" 
                      class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                      :style="getStatusStyleActivity(activity.status)"
                    >
                      {{ activity.formatted_status }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span 
                      :class="getPriorityColor(activity.priority)" 
                      class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                      :style="getPriorityStyle(activity.priority)"
                    >
                      {{ activity.formatted_priority }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ activity.assigned_user?.name || activity.user?.name }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    <div v-if="activity.scheduled_at">
                      {{ formatDate(activity.scheduled_at) }}
                      <div v-if="activity.is_overdue" class="text-xs text-red-600">Vencida</div>
                      <div v-else-if="activity.is_due_today" class="text-xs text-yellow-600">Hoy</div>
                    </div>
                    <div v-else class="text-gray-400">Sin programar</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    <div v-if="activity.related">
                      <span class="text-xs text-gray-400">{{ getRelatedTypeName(activity.related_type) }}</span>
                      <div class="text-sm text-gray-900">{{ getRelatedName(activity.related) }}</div>
                    </div>
                    <div v-else>-</div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                    <Link :href="route('activities.show', activity.id)" class="text-blue-600 hover:text-blue-900">Ver</Link>
                    <Link :href="route('activities.edit', activity.id)" class="text-indigo-600 hover:text-indigo-900">Editar</Link>
                    <button 
                      v-if="activity.status === 'pending'"
                      @click="markCompleted(activity)"
                      class="text-green-600 hover:text-green-900"
                    >
                      Completar
                    </button>
                    <button @click="confirmDelete(activity)" class="text-red-600 hover:text-red-900">Eliminar</button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Empty State -->
          <div v-else class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z"/>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No hay actividades</h3>
            <p class="mt-1 text-sm text-gray-500">Comienza creando tu primera actividad.</p>
            <div class="mt-6">
              <Link :href="route('activities.create')" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                <svg class="-ml-1 mr-2 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
                </svg>
                Nueva Actividad
              </Link>
            </div>
          </div>
        </div>

        <!-- Pagination -->
        <div v-if="activities.links && activities.links.length > 3" class="mt-6 flex justify-center">
          <nav class="flex items-center space-x-2">
            <component
              v-for="(link, index) in activities.links"
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
            <h3 class="text-lg font-medium text-gray-900">Eliminar actividad</h3>
            <p class="mt-2 text-sm text-gray-500">
              ¿Estás seguro de que deseas eliminar "{{ activityToDelete?.subject }}"? Esta acción no se puede deshacer.
            </p>
          </div>
        </div>
        <div class="mt-6 flex justify-end space-x-3">
          <button @click="showDeleteModal = false" class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50">
            Cancelar
          </button>
          <button @click="deleteActivity" class="px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md hover:bg-red-700">
            Eliminar
          </button>
        </div>
      </div>
    </Modal>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed, h } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Modal from '@/Components/Modal.vue'
import { debounce } from 'lodash'

const props = defineProps({
  activities: Object,
  filters: Object,
  types: Object,
  statuses: Object,
  priorities: Object,
  users: Array,
  stats: Object
})

// State
const search = ref(props.filters.search || '')
const selectedType = ref(props.filters.type || '')
const selectedStatus = ref(props.filters.status || '')
const selectedPriority = ref(props.filters.priority || '')
const selectedUser = ref(props.filters.assigned_to || '')
const showDeleteModal = ref(false)
const activityToDelete = ref(null)

// Computed
const hasFilters = computed(() => {
  return search.value || selectedType.value || selectedStatus.value || 
         selectedPriority.value || selectedUser.value
})

// Methods
const performSearch = debounce(() => {
  applyFilters()
}, 300)

const applyFilters = () => {
  router.get(route('activities.index'), {
    search: search.value,
    type: selectedType.value,
    status: selectedStatus.value,
    priority: selectedPriority.value,
    assigned_to: selectedUser.value
  }, {
    preserveState: true,
    replace: true
  })
}

const applyQuickFilter = (filter) => {
  const params = { [filter]: props.filters[filter] ? null : '1' }
  router.get(route('activities.index'), params, {
    preserveState: true,
    replace: true
  })
}

const clearFilters = () => {
  search.value = ''
  selectedType.value = ''
  selectedStatus.value = ''
  selectedPriority.value = ''
  selectedUser.value = ''
  router.get(route('activities.index'))
}

const getActivityIcon = (type) => {
  const icons = {
    call: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
      h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z' })
    ]),
    email: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
      h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z' })
    ]),
    meeting: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
      h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z' })
    ]),
    task: () => h('svg', { fill: 'none', stroke: 'currentColor', viewBox: '0 0 24 24' }, [
      h('path', { 'stroke-linecap': 'round', 'stroke-linejoin': 'round', 'stroke-width': '2', d: 'M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4' })
    ])
  }
  return icons[type] || icons.task
}

const getActivityIconColor = (type) => {
  const colors = {
    call: '',
    email: '',
    meeting: '',
    note: '',
    task: '',
    sms: '',
    whatsapp: '',
    visit: ''
  }
  
  return colors[type] || 'bg-gray-500 text-white'
}

const getActivityIconStyle = (type) => {
  const styles = {
    call: { backgroundColor: '#10b981', color: 'white' },        // Verde vibrante
    email: { backgroundColor: '#3b82f6', color: 'white' },       // Azul brillante
    meeting: { backgroundColor: '#a855f7', color: 'white' },     // Púrpura intenso
    note: { backgroundColor: '#eab308', color: 'white' },        // Amarillo vibrante
    task: { backgroundColor: '#6b7280', color: 'white' },        // Gris
    sms: { backgroundColor: '#f97316', color: 'white' },         // Naranja
    whatsapp: { backgroundColor: '#10b981', color: 'white' },    // Verde vibrante
    visit: { backgroundColor: '#6366f1', color: 'white' }        // Índigo
  }
  
  return styles[type] || { backgroundColor: '#6b7280', color: 'white' }
}

const getTypeColor = (type) => {
  const colors = {
    call: '',
    email: '',
    meeting: '',
    note: '',
    task: '',
    sms: '',
    whatsapp: '',
    visit: ''
  }
  return colors[type] || 'bg-gray-500 text-white'
}

const getTypeStyle = (type) => {
  const styles = {
    call: { backgroundColor: '#10b981', color: 'white' },        // Verde vibrante
    email: { backgroundColor: '#3b82f6', color: 'white' },       // Azul brillante
    meeting: { backgroundColor: '#a855f7', color: 'white' },     // Púrpura intenso
    note: { backgroundColor: '#eab308', color: 'white' },        // Amarillo vibrante
    task: { backgroundColor: '#6b7280', color: 'white' },        // Gris
    sms: { backgroundColor: '#f97316', color: 'white' },         // Naranja
    whatsapp: { backgroundColor: '#10b981', color: 'white' },    // Verde vibrante
    visit: { backgroundColor: '#6366f1', color: 'white' }        // Índigo
  }
  return styles[type] || { backgroundColor: '#6b7280', color: 'white' }
}

const getStatusColor = (status) => {
  const colors = {
    pending: '',
    completed: '',
    cancelled: ''
  }
  return colors[status] || 'bg-gray-500 text-white'
}

const getStatusStyleActivity = (status) => {
  const styles = {
    pending: { backgroundColor: '#eab308', color: 'white' },     // Amarillo vibrante
    completed: { backgroundColor: '#10b981', color: 'white' },   // Verde vibrante
    cancelled: { backgroundColor: '#ef4444', color: 'white' }    // Rojo intenso
  }
  return styles[status] || { backgroundColor: '#6b7280', color: 'white' }
}

const getPriorityColor = (priority) => {
  const colors = {
    low: '',
    medium: '',
    high: '',
    urgent: ''
  }
  return colors[priority] || 'bg-gray-500 text-white'
}

const getPriorityStyle = (priority) => {
  const styles = {
    low: { backgroundColor: '#10b981', color: 'white' },         // Verde vibrante
    medium: { backgroundColor: '#eab308', color: 'white' },      // Amarillo vibrante
    high: { backgroundColor: '#f97316', color: 'white' },        // Naranja
    urgent: { backgroundColor: '#ef4444', color: 'white' }       // Rojo intenso
  }
  return styles[priority] || { backgroundColor: '#6b7280', color: 'white' }
}

const getRelatedTypeName = (type) => {
  if (type?.includes('Lead')) return 'Lead'
  if (type?.includes('Client')) return 'Cliente'
  if (type?.includes('Property')) return 'Propiedad'
  return 'Otro'
}

const getRelatedName = (related) => {
  if (related?.full_name) return related.full_name
  if (related?.first_name && related?.last_name) return `${related.first_name} ${related.last_name}`
  if (related?.name) return related.name
  if (related?.title) return related.title
  return 'Sin nombre'
}

const truncateText = (text, length) => {
  if (!text) return ''
  return text.length > length ? text.substring(0, length) + '...' : text
}

const formatDate = (date) => {
  return new Intl.DateTimeFormat('es-CO', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  }).format(new Date(date))
}

const markCompleted = (activity) => {
  router.post(route('activities.complete', activity.id))
}

const confirmDelete = (activity) => {
  activityToDelete.value = activity
  showDeleteModal.value = true
}

const deleteActivity = () => {
  if (activityToDelete.value) {
    router.delete(route('activities.destroy', activityToDelete.value.id), {
      onSuccess: () => {
        showDeleteModal.value = false
        activityToDelete.value = null
      }
    })
  }
}
</script>