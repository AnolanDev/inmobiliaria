<template>
  <header class="fixed top-0 right-0 left-0 z-50 bg-white border-b border-gray-200 shadow-sm">
    <!-- Top Info Bar -->
    <div class="bg-gradient-to-r from-green-600 to-green-700 px-4 sm:px-6 lg:px-8 py-1.5">
      <div class="flex items-center justify-between text-white text-xs">
        <div class="flex items-center space-x-4">
          <div class="flex items-center space-x-1">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <span>{{ currentDateTime }}</span>
          </div>
          <div class="hidden sm:flex items-center space-x-1">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
            </svg>
            <span>GMT-5 (COT)</span>
          </div>
        </div>
        <div class="flex items-center space-x-4">
          <div class="hidden md:flex items-center space-x-1">
            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
            </svg>
            <span v-if="exchangeRate.loading">Cargando...</span>
            <span v-else-if="exchangeRate.rate">USD: ${{ exchangeRate.rate }} COP</span>
            <span v-else>USD: No disponible</span>
          </div>
          <div class="flex items-center space-x-1">
            <div class="w-2 h-2 bg-green-300 rounded-full animate-pulse"></div>
            <span class="hidden sm:inline">Sistema Activo</span>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Main Header -->
    <div class="flex h-14 items-center px-4 sm:px-6 lg:px-8">
      <!-- Logo -->
      <div class="flex items-center flex-shrink-0">
        <img src="/ts/logo.png" alt="Tierra Soñada" class="h-12 w-auto" />
      </div>

      <!-- Mobile menu button -->
      <button
        @click="$emit('toggleMobileMenu')"
        class="inline-flex items-center justify-center p-3 text-gray-600 bg-white border border-gray-200 rounded-xl shadow-md hover:shadow-lg hover:text-gray-700 hover:border-gray-300 focus:outline-none focus:ring-4 focus:ring-gray-500/20 transition-all duration-300 ease-in-out transform hover:scale-105 active:scale-95 lg:hidden ml-4"
      >
        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
      </button>

      <!-- Spacer to push right content to the right -->
      <div class="flex-1"></div>

      <!-- Right side items -->
      <div class="flex items-center space-x-4">
        <!-- Activity Notifications -->
        <div class="relative">
          <ActivityNotifications :activities="activities" />
        </div>
        
        <!-- Separator -->
        <div class="h-6 w-px bg-gray-200"></div>
        
        <!-- User dropdown -->
        <div class="relative">
          <UserDropdown :user="user" :is-mobile="false" />
        </div>
      </div>
    </div>
  </header>
</template>

<script setup>
import { computed, ref, onMounted, onUnmounted } from 'vue'
import { usePage } from '@inertiajs/vue3'
import ActivityNotifications from '@/Components/Activities/ActivityNotifications.vue'
import UserDropdown from '@/Components/Navigation/UserDropdown.vue'

defineEmits(['toggleMobileMenu'])

const props = defineProps({
  activities: {
    type: Array,
    default: () => []
  }
})

// Get current user from Inertia
const page = usePage()
const user = computed(() => page.props.auth?.user)

// Date and Time
const currentDateTime = ref('')
const exchangeRate = ref({
  rate: null,
  loading: true
})

const updateDateTime = () => {
  const now = new Date()
  const options = {
    weekday: 'short',
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
    hour12: false,
    timeZone: 'America/Bogota'
  }
  currentDateTime.value = now.toLocaleDateString('es-CO', options)
}

const fetchExchangeRate = async () => {
  try {
    exchangeRate.value.loading = true
    // Using a free API for USD to COP exchange rate
    const response = await fetch('https://api.exchangerate-api.com/v4/latest/USD')
    const data = await response.json()
    
    if (data.rates && data.rates.COP) {
      exchangeRate.value.rate = Math.round(data.rates.COP)
    }
  } catch (error) {
    console.log('No se pudo obtener el tipo de cambio:', error)
  } finally {
    exchangeRate.value.loading = false
  }
}

let timeInterval

onMounted(() => {
  updateDateTime()
  fetchExchangeRate()
  
  // Update time every minute
  timeInterval = setInterval(updateDateTime, 60000)
  
  // Update exchange rate every 30 minutes
  setInterval(fetchExchangeRate, 1800000)
})

onUnmounted(() => {
  if (timeInterval) {
    clearInterval(timeInterval)
  }
})
</script>