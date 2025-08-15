<template>
  <Head title="Dashboard - Actividades" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex justify-between items-center">
        <div>
          <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dashboard de Actividades
          </h2>
          <p class="text-sm text-gray-600 mt-1">
            Resumen de tus actividades pendientes y completadas
          </p>
        </div>
        <div class="flex space-x-3">
          <Link
            :href="route('activities.create')"
            class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Nueva Actividad
          </Link>
          <Link
            :href="route('activities.index')"
            class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
          >
            Ver Todas
          </Link>
        </div>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
          <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-6">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 0V5a2 2 0 012-2h4a2 2 0 012 2v2m-6 0h8"/>
                    </svg>
                  </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">Para Hoy</dt>
                    <dd class="flex items-baseline">
                      <div class="text-3xl font-bold text-gray-900">{{ stats.pending_today }}</div>
                      <div class="ml-2 text-sm text-gray-600">actividades</div>
                    </dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-6">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                    </svg>
                  </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">Vencidas</dt>
                    <dd class="flex items-baseline">
                      <div class="text-3xl font-bold text-gray-900">{{ stats.overdue }}</div>
                      <div class="ml-2 text-sm text-gray-600">actividades</div>
                    </dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-6">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                  </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">Completadas Hoy</dt>
                    <dd class="flex items-baseline">
                      <div class="text-3xl font-bold text-gray-900">{{ stats.completed_today }}</div>
                      <div class="ml-2 text-sm text-gray-600">actividades</div>
                    </dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>

          <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-6">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                    </svg>
                  </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                  <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">Esta Semana</dt>
                    <dd class="flex items-baseline">
                      <div class="text-3xl font-bold text-gray-900">{{ stats.total_this_week }}</div>
                      <div class="ml-2 text-sm text-gray-600">actividades</div>
                    </dd>
                  </dl>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <!-- Upcoming Activities -->
          <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
              <div class="flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900">Próximas Actividades</h3>
                <Link 
                  :href="route('activities.index', { my_activities: 1 })"
                  class="text-sm text-green-600 hover:text-green-900"
                >
                  Ver todas
                </Link>
              </div>
            </div>
            <div class="p-6">
              <div v-if="upcomingActivities.length > 0" class="space-y-4">
                <div 
                  v-for="activity in upcomingActivities" 
                  :key="activity.id"
                  class="flex items-start space-x-4 p-4 rounded-lg border border-gray-100 hover:bg-gray-50 transition-colors"
                >
                  <div class="flex-shrink-0">
                    <div :class="getActivityIconColor(activity.type)" class="h-10 w-10 rounded-full flex items-center justify-center">
                      <component :is="getActivityIcon(activity.type)" class="h-5 w-5" />
                    </div>
                  </div>
                  <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between">
                      <Link
                        :href="route('activities.show', activity.id)"
                        class="text-sm font-medium text-gray-900 hover:text-green-600"
                      >
                        {{ activity.subject }}
                      </Link>
                      <span :class="getPriorityColor(activity.priority)" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium">
                        {{ activity.formatted_priority }}
                      </span>
                    </div>
                    <div class="mt-1 flex items-center space-x-2 text-sm text-gray-500">
                      <span :class="getTypeColor(activity.type)" class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium">
                        {{ activity.formatted_type }}
                      </span>
                      <span>•</span>
                      <span v-if="activity.scheduled_at">{{ formatDate(activity.scheduled_at) }}</span>
                      <span v-else>Sin programar</span>
                    </div>
                    <div v-if="activity.related" class="mt-1 text-xs text-gray-400">
                      {{ getRelatedTypeName(activity.related_type) }}: {{ getRelatedName(activity.related) }}
                    </div>
                  </div>
                  <div class="flex items-center space-x-2">
                    <button
                      @click="quickComplete(activity)"
                      class="text-green-600 hover:text-green-900"
                      title="Marcar como completada"
                    >
                      <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                      </svg>
                    </button>
                  </div>
                </div>
              </div>
              <div v-else class="text-center py-8">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No hay actividades próximas</h3>
                <p class="mt-1 text-sm text-gray-500">¡Estás al día con tus tareas!</p>
              </div>
            </div>
          </div>

          <!-- Overdue Activities -->
          <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
              <div class="flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900">Actividades Vencidas</h3>
                <Link 
                  :href="route('activities.index', { overdue: 1 })"
                  class="text-sm text-red-600 hover:text-red-900"
                >
                  Ver todas
                </Link>
              </div>
            </div>
            <div class="p-6">
              <div v-if="overdueActivities.length > 0" class="space-y-4">
                <div 
                  v-for="activity in overdueActivities" 
                  :key="activity.id"
                  class="flex items-start space-x-4 p-4 rounded-lg border border-red-100 bg-red-50 hover:bg-red-100 transition-colors"
                >
                  <div class="flex-shrink-0">
                    <div class="h-10 w-10 bg-red-100 rounded-full flex items-center justify-center">
                      <svg class="h-5 w-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                      </svg>
                    </div>
                  </div>
                  <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between">
                      <Link
                        :href="route('activities.show', activity.id)"
                        class="text-sm font-medium text-red-900 hover:text-red-700"
                      >
                        {{ activity.subject }}
                      </Link>
                      <span :class="getPriorityColor(activity.priority)" class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium">
                        {{ activity.formatted_priority }}
                      </span>
                    </div>
                    <div class="mt-1 flex items-center space-x-2 text-sm text-red-700">
                      <span :class="getTypeColor(activity.type)" class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium">
                        {{ activity.formatted_type }}
                      </span>
                      <span>•</span>
                      <span>Vencida: {{ formatDate(activity.scheduled_at) }}</span>
                    </div>
                    <div v-if="activity.related" class="mt-1 text-xs text-red-600">
                      {{ getRelatedTypeName(activity.related_type) }}: {{ getRelatedName(activity.related) }}
                    </div>
                  </div>
                  <div class="flex items-center space-x-2">
                    <button
                      @click="quickComplete(activity)"
                      class="text-green-600 hover:text-green-900"
                      title="Marcar como completada"
                    >
                      <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                      </svg>
                    </button>
                  </div>
                </div>
              </div>
              <div v-else class="text-center py-8">
                <svg class="mx-auto h-12 w-12 text-green-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Sin actividades vencidas</h3>
                <p class="mt-1 text-sm text-gray-500">¡Excelente gestión del tiempo!</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Quick Actions -->
        <div class="mt-8 bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">Acciones Rápidas</h3>
          </div>
          <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
              <Link
                :href="route('activities.create', { type: 'call' })"
                class="flex items-center p-4 border border-gray-200 rounded-lg hover:border-green-300 hover:bg-green-50 transition-colors"
              >
                <div class="flex-shrink-0">
                  <div class="h-10 w-10 bg-green-100 rounded-full flex items-center justify-center">
                    <svg class="h-5 w-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                    </svg>
                  </div>
                </div>
                <div class="ml-4">
                  <p class="text-sm font-medium text-gray-900">Programar Llamada</p>
                  <p class="text-sm text-gray-500">Agendar una llamada con un lead o cliente</p>
                </div>
              </Link>

              <Link
                :href="route('activities.create', { type: 'meeting' })"
                class="flex items-center p-4 border border-gray-200 rounded-lg hover:border-purple-300 hover:bg-purple-50 transition-colors"
              >
                <div class="flex-shrink-0">
                  <div class="h-10 w-10 bg-purple-100 rounded-full flex items-center justify-center">
                    <svg class="h-5 w-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                  </div>
                </div>
                <div class="ml-4">
                  <p class="text-sm font-medium text-gray-900">Agendar Reunión</p>
                  <p class="text-sm text-gray-500">Programar una reunión con el equipo</p>
                </div>
              </Link>

              <Link
                :href="route('activities.create', { type: 'task' })"
                class="flex items-center p-4 border border-gray-200 rounded-lg hover:border-gray-400 hover:bg-gray-50 transition-colors"
              >
                <div class="flex-shrink-0">
                  <div class="h-10 w-10 bg-gray-100 rounded-full flex items-center justify-center">
                    <svg class="h-5 w-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                  </div>
                </div>
                <div class="ml-4">
                  <p class="text-sm font-medium text-gray-900">Crear Tarea</p>
                  <p class="text-sm text-gray-500">Añadir una nueva tarea pendiente</p>
                </div>
              </Link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { h } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
  stats: Object,
  upcomingActivities: Array,
  overdueActivities: Array
})

// Methods
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
    call: 'bg-green-100 text-green-600',
    email: 'bg-green-100 text-green-600',
    meeting: 'bg-purple-100 text-purple-600',
    note: 'bg-yellow-100 text-yellow-600',
    task: 'bg-gray-100 text-gray-600'
  }
  return colors[type] || 'bg-gray-100 text-gray-600'
}

const getTypeColor = (type) => {
  const colors = {
    call: 'bg-green-100 text-green-800',
    email: 'bg-green-100 text-green-800',
    meeting: 'bg-purple-100 text-purple-800',
    note: 'bg-yellow-100 text-yellow-800',
    task: 'bg-gray-100 text-gray-800'
  }
  return colors[type] || 'bg-gray-100 text-gray-800'
}

const getPriorityColor = (priority) => {
  const colors = {
    low: 'bg-green-100 text-green-800',
    medium: 'bg-yellow-100 text-yellow-800',
    high: 'bg-orange-100 text-orange-800',
    urgent: 'bg-red-100 text-red-800'
  }
  return colors[priority] || 'bg-gray-100 text-gray-800'
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

const formatDate = (date) => {
  return new Intl.DateTimeFormat('es-CO', {
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  }).format(new Date(date))
}

const quickComplete = (activity) => {
  router.post(route('activities.complete', activity.id), {}, {
    preserveScroll: true
  })
}
</script>