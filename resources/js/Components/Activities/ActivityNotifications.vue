<template>
  <div class="relative">
    <button
      @click="showDropdown = !showDropdown"
      class="relative p-2 text-gray-500 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 rounded-md"
    >
      <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM10.07 14C9.07 14 8.33 13.26 8.33 12.26V7.74C8.33 6.74 9.07 6 10.07 6h7.86c1 0 1.74.74 1.74 1.74v4.52c0 1-.74 1.74-1.74 1.74h-7.86zM15 12H7M15 9H7"/>
      </svg>
      
      <!-- Badge for pending activities -->
      <span 
        v-if="pendingCount > 0" 
        class="absolute -top-1 -right-1 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-red-100 bg-red-600 rounded-full"
      >
        {{ pendingCount > 99 ? '99+' : pendingCount }}
      </span>
    </button>

    <!-- Dropdown -->
    <div 
      v-if="showDropdown"
      @click.away="showDropdown = false"
      class="absolute right-0 z-50 mt-2 w-80 bg-white rounded-md shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
    >
      <div class="py-1">
        <div class="px-4 py-2 border-b border-gray-200">
          <div class="flex items-center justify-between">
            <h3 class="text-sm font-medium text-gray-900">Actividades</h3>
            <Link :href="route('activities.index')" class="text-xs text-green-600 hover:text-green-900">
              Ver todas
            </Link>
          </div>
        </div>

        <!-- Overdue Activities -->
        <div v-if="overdueActivities.length > 0" class="px-4 py-2 bg-red-50 border-b border-red-100">
          <h4 class="text-xs font-medium text-red-800 mb-2">Vencidas ({{ overdueActivities.length }})</h4>
          <div class="space-y-1">
            <div 
              v-for="activity in overdueActivities.slice(0, 3)" 
              :key="activity.id"
              class="text-xs"
            >
              <Link :href="route('activities.show', activity.id)" class="text-red-700 hover:text-red-900 font-medium">
                {{ activity.subject }}
              </Link>
              <div class="text-red-600">{{ formatDate(activity.scheduled_at) }}</div>
            </div>
            <div v-if="overdueActivities.length > 3" class="text-xs text-red-600">
              +{{ overdueActivities.length - 3 }} más
            </div>
          </div>
        </div>

        <!-- Today's Activities -->
        <div v-if="todayActivities.length > 0" class="px-4 py-2 border-b border-gray-200">
          <h4 class="text-xs font-medium text-gray-700 mb-2">Para Hoy ({{ todayActivities.length }})</h4>
          <div class="space-y-1">
            <div 
              v-for="activity in todayActivities.slice(0, 3)" 
              :key="activity.id"
              class="text-xs"
            >
              <Link :href="route('activities.show', activity.id)" class="text-gray-900 hover:text-green-600 font-medium">
                {{ activity.subject }}
              </Link>
              <div class="text-gray-600">{{ formatTime(activity.scheduled_at) }}</div>
            </div>
            <div v-if="todayActivities.length > 3" class="text-xs text-gray-600">
              +{{ todayActivities.length - 3 }} más
            </div>
          </div>
        </div>

        <!-- Upcoming Activities -->
        <div v-if="upcomingActivities.length > 0" class="px-4 py-2 border-b border-gray-200">
          <h4 class="text-xs font-medium text-gray-700 mb-2">Próximas ({{ upcomingActivities.length }})</h4>
          <div class="space-y-1">
            <div 
              v-for="activity in upcomingActivities.slice(0, 3)" 
              :key="activity.id"
              class="text-xs"
            >
              <Link :href="route('activities.show', activity.id)" class="text-gray-900 hover:text-green-600 font-medium">
                {{ activity.subject }}
              </Link>
              <div class="text-gray-600">{{ formatDate(activity.scheduled_at) }}</div>
            </div>
            <div v-if="upcomingActivities.length > 3" class="text-xs text-gray-600">
              +{{ upcomingActivities.length - 3 }} más
            </div>
          </div>
        </div>

        <!-- Empty State -->
        <div v-if="totalActivities === 0" class="px-4 py-6 text-center">
          <svg class="mx-auto h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
          </svg>
          <p class="mt-2 text-xs text-gray-500">Sin actividades pendientes</p>
        </div>

        <!-- Quick Actions -->
        <div class="px-4 py-2 bg-gray-50 border-t border-gray-200">
          <Link 
            :href="route('activities.create')"
            class="inline-flex items-center w-full px-3 py-2 text-xs font-medium text-green-700 bg-green-100 rounded-md hover:bg-green-200"
          >
            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
            </svg>
            Nueva Actividad
          </Link>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { Link } from '@inertiajs/vue3'

const props = defineProps({
  activities: {
    type: Array,
    default: () => []
  }
})

const showDropdown = ref(false)

// Computed properties for different activity categories
const overdueActivities = computed(() => {
  return props.activities.filter(activity => 
    activity.status === 'pending' && 
    activity.is_overdue
  )
})

const todayActivities = computed(() => {
  return props.activities.filter(activity => 
    activity.status === 'pending' && 
    activity.is_due_today && 
    !activity.is_overdue
  )
})

const upcomingActivities = computed(() => {
  return props.activities.filter(activity => 
    activity.status === 'pending' && 
    !activity.is_due_today && 
    !activity.is_overdue
  )
})

const pendingCount = computed(() => {
  return overdueActivities.value.length + todayActivities.value.length
})

const totalActivities = computed(() => {
  return props.activities.filter(activity => activity.status === 'pending').length
})

// Methods
const formatDate = (date) => {
  return new Intl.DateTimeFormat('es-CO', {
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  }).format(new Date(date))
}

const formatTime = (date) => {
  return new Intl.DateTimeFormat('es-CO', {
    hour: '2-digit',
    minute: '2-digit'
  }).format(new Date(date))
}

// Handle click outside
const handleClickOutside = (event) => {
  if (!event.target.closest('.relative')) {
    showDropdown.value = false
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside)
})
</script>