<template>
  <div class="bg-white rounded-lg shadow-sm border border-gray-200">
    <!-- Calendar Header -->
    <div class="p-4 border-b border-gray-200">
      <div class="flex items-center justify-between">
        <h2 class="text-lg font-semibold text-gray-900">
          {{ monthNames[currentMonth] }} {{ currentYear }}
        </h2>
        <div class="flex items-center gap-2">
          <button
            @click="previousMonth"
            class="p-2 hover:bg-gray-100 rounded-lg transition-colors"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
          </button>
          <button
            @click="goToToday"
            class="px-3 py-1 text-sm bg-blue-100 text-blue-700 rounded-md hover:bg-blue-200 transition-colors"
          >
            Hoy
          </button>
          <button
            @click="nextMonth"
            class="p-2 hover:bg-gray-100 rounded-lg transition-colors"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
            </svg>
          </button>
        </div>
      </div>
    </div>

    <!-- Calendar Grid -->
    <div class="p-4">
      <!-- Days of the week header -->
      <div class="grid grid-cols-7 gap-1 mb-2">
        <div
          v-for="day in dayNames"
          :key="day"
          class="p-2 text-center text-xs font-medium text-gray-500 uppercase"
        >
          {{ day }}
        </div>
      </div>

      <!-- Calendar days -->
      <div class="grid grid-cols-7 gap-1">
        <div
          v-for="day in calendarDays"
          :key="day.key"
          :class="[
            'min-h-[100px] p-1 border border-gray-100 rounded-lg',
            day.isCurrentMonth ? 'bg-white' : 'bg-gray-50',
            day.isToday ? 'ring-2 ring-blue-500' : '',
            'hover:bg-gray-50 transition-colors cursor-pointer'
          ]"
          @click="selectDay(day)"
        >
          <!-- Day number -->
          <div class="flex justify-between items-start mb-1">
            <span
              :class="[
                'text-sm font-medium',
                day.isCurrentMonth ? 'text-gray-900' : 'text-gray-400',
                day.isToday ? 'text-blue-600 font-bold' : ''
              ]"
            >
              {{ day.date.getDate() }}
            </span>
            
            <!-- Add visit button -->
            <button
              v-if="day.isCurrentMonth"
              @click.stop="addVisit(day.date)"
              class="opacity-0 group-hover:opacity-100 text-gray-400 hover:text-blue-600 transition-all"
              title="Nueva visita"
            >
              <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
              </svg>
            </button>
          </div>

          <!-- Visits for this day -->
          <div class="space-y-1">
            <div
              v-for="visit in day.visits"
              :key="visit.id"
              :class="[
                'text-xs p-1 rounded truncate cursor-pointer',
                getVisitClasses(visit)
              ]"
              @click.stop="$emit('view', visit)"
              :title="getVisitTooltip(visit)"
            >
              <div class="flex items-center gap-1">
                <div
                  :class="[
                    'w-2 h-2 rounded-full flex-shrink-0',
                    getVisitDotColor(visit)
                  ]"
                ></div>
                <span class="truncate">
                  {{ formatVisitTime(visit.scheduled_at) }} - {{ visit.client?.name || 'Sin cliente' }}
                </span>
              </div>
            </div>
          </div>

          <!-- More visits indicator -->
          <div
            v-if="day.visits.length > 3"
            class="text-xs text-gray-500 text-center mt-1"
          >
            +{{ day.visits.length - 3 }} más
          </div>
        </div>
      </div>
    </div>

    <!-- Visit Details Modal -->
    <div
      v-if="showVisitModal"
      class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50"
      @click="closeModal"
    >
      <div
        class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white"
        @click.stop
      >
        <div class="flex justify-between items-start mb-4">
          <h3 class="text-lg font-bold text-gray-900">
            Visitas - {{ formatDate(selectedDate) }}
          </h3>
          <button
            @click="closeModal"
            class="text-gray-400 hover:text-gray-600"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>

        <div class="space-y-3 max-h-96 overflow-y-auto">
          <div
            v-for="visit in selectedDayVisits"
            :key="visit.id"
            :class="[
              'p-3 rounded-lg border cursor-pointer hover:shadow-md transition-all',
              getVisitClasses(visit, true)
            ]"
            @click="$emit('view', visit)"
          >
            <div class="flex items-center justify-between mb-2">
              <span class="font-medium text-gray-900">
                {{ formatVisitTime(visit.scheduled_at) }}
              </span>
              <span
                :class="[
                  'px-2 py-1 rounded-full text-xs font-medium',
                  visit.status_color || 'bg-gray-100 text-gray-800'
                ]"
              >
                {{ getStatusLabel(visit.status) }}
              </span>
            </div>
            
            <div class="text-sm text-gray-600">
              <p><strong>Cliente:</strong> {{ visit.client?.name || 'Sin cliente' }}</p>
              <p><strong>{{ visit.is_project_visit ? 'Proyecto' : 'Propiedad' }}:</strong> {{ visit.visit_subject }}</p>
              <p><strong>Agente:</strong> {{ visit.agent?.name || 'Sin agente' }}</p>
            </div>
          </div>
        </div>

        <div class="flex justify-end gap-2 mt-4">
          <button
            @click="addVisit(selectedDate)"
            class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition-colors"
          >
            Nueva Visita
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
  visits: {
    type: Array,
    required: true
  }
})

defineEmits(['view', 'edit', 'delete', 'complete', 'cancel', 'no-show'])

// Calendar state
const currentDate = ref(new Date())
const currentMonth = computed(() => currentDate.value.getMonth())
const currentYear = computed(() => currentDate.value.getFullYear())
const showVisitModal = ref(false)
const selectedDate = ref(null)

// Calendar data
const monthNames = [
  'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio',
  'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
]

const dayNames = ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb']

// Calendar navigation
const previousMonth = () => {
  currentDate.value = new Date(currentYear.value, currentMonth.value - 1, 1)
}

const nextMonth = () => {
  currentDate.value = new Date(currentYear.value, currentMonth.value + 1, 1)
}

const goToToday = () => {
  currentDate.value = new Date()
}

// Calendar grid computation
const calendarDays = computed(() => {
  const year = currentYear.value
  const month = currentMonth.value
  const firstDay = new Date(year, month, 1)
  const lastDay = new Date(year, month + 1, 0)
  const startDate = new Date(firstDay)
  startDate.setDate(startDate.getDate() - firstDay.getDay())
  
  const days = []
  const today = new Date()
  today.setHours(0, 0, 0, 0)

  for (let i = 0; i < 42; i++) { // 6 weeks * 7 days
    const date = new Date(startDate)
    date.setDate(startDate.getDate() + i)
    
    const dayVisits = props.visits.filter(visit => {
      const visitDate = new Date(visit.scheduled_at)
      return visitDate.toDateString() === date.toDateString()
    }).slice(0, 4) // Show max 4 visits per day

    days.push({
      key: `${date.getFullYear()}-${date.getMonth()}-${date.getDate()}`,
      date: new Date(date),
      isCurrentMonth: date.getMonth() === month,
      isToday: date.getTime() === today.getTime(),
      visits: dayVisits
    })
  }

  return days
})

// Selected day visits
const selectedDayVisits = computed(() => {
  if (!selectedDate.value) return []
  
  return props.visits.filter(visit => {
    const visitDate = new Date(visit.scheduled_at)
    return visitDate.toDateString() === selectedDate.value.toDateString()
  }).sort((a, b) => new Date(a.scheduled_at) - new Date(b.scheduled_at))
})

// Modal functions
const selectDay = (day) => {
  if (day.visits.length > 0) {
    selectedDate.value = day.date
    showVisitModal.value = true
  }
}

const closeModal = () => {
  showVisitModal.value = false
  selectedDate.value = null
}

// Visit styling
const getVisitClasses = (visit, isModal = false) => {
  const baseClasses = isModal 
    ? 'border-l-4 bg-gray-50' 
    : 'text-white font-medium'

  switch (visit.status) {
    case 'scheduled':
      return isModal 
        ? `${baseClasses} border-blue-500 hover:bg-blue-50`
        : `${baseClasses} bg-blue-500 hover:bg-blue-600`
    case 'completed':
      return isModal 
        ? `${baseClasses} border-green-500 hover:bg-green-50`
        : `${baseClasses} bg-green-500 hover:bg-green-600`
    case 'cancelled':
      return isModal 
        ? `${baseClasses} border-red-500 hover:bg-red-50`
        : `${baseClasses} bg-red-500 hover:bg-red-600`
    case 'no_show':
      return isModal 
        ? `${baseClasses} border-gray-500 hover:bg-gray-100`
        : `${baseClasses} bg-gray-500 hover:bg-gray-600`
    default:
      return isModal 
        ? `${baseClasses} border-gray-500 hover:bg-gray-100`
        : `${baseClasses} bg-gray-500 hover:bg-gray-600`
  }
}

const getVisitDotColor = (visit) => {
  switch (visit.status) {
    case 'scheduled': return 'bg-blue-500'
    case 'completed': return 'bg-green-500'
    case 'cancelled': return 'bg-red-500'
    case 'no_show': return 'bg-gray-500'
    default: return 'bg-gray-500'
  }
}

const getStatusLabel = (status) => {
  const labels = {
    scheduled: 'Programada',
    completed: 'Completada',
    cancelled: 'Cancelada',
    no_show: 'No Asistió'
  }
  return labels[status] || status
}

// Formatting functions
const formatVisitTime = (dateTime) => {
  return new Date(dateTime).toLocaleTimeString('es-ES', {
    hour: '2-digit',
    minute: '2-digit',
    hour12: true
  })
}

const formatDate = (date) => {
  return date.toLocaleDateString('es-ES', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

const getVisitTooltip = (visit) => {
  return `${formatVisitTime(visit.scheduled_at)} - ${visit.client?.name || 'Sin cliente'} - ${visit.visit_subject}`
}

// Add visit function
const addVisit = (date) => {
  const isoDate = date.toISOString().split('T')[0]
  const currentTime = new Date()
  const scheduledAt = `${isoDate}T${currentTime.getHours().toString().padStart(2, '0')}:${currentTime.getMinutes().toString().padStart(2, '0')}`
  
  router.get(route('visits.create'), {
    scheduled_at: scheduledAt
  })
}
</script>