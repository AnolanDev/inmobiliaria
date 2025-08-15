<template>
  <header class="fixed top-0 right-0 left-0 z-50 bg-white border-b border-gray-200 shadow-sm">
    <div class="flex h-16 items-center px-4 sm:px-6 lg:px-8">
      <!-- Logo (always visible on the left) -->
      <div class="flex items-center gap-3 flex-shrink-0">
        <div class="w-10 h-10 rounded-xl flex items-center justify-center shadow-lg transform hover:scale-105 transition-transform duration-200" style="background: linear-gradient(135deg, #00bf63, #009951);">
          <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
          </svg>
        </div>
        <h1 class="text-xl font-bold text-gray-900 hidden sm:block">InmoApp</h1>
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
import { computed } from 'vue'
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
</script>