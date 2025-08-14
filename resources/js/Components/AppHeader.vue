<template>
  <header class="bg-white border-b border-gray-200 lg:pl-64">
    <div class="flex h-16 items-center justify-between px-4 sm:px-6 lg:px-8">
      <!-- Mobile menu button -->
      <button
        @click="$emit('toggleMobileMenu')"
        class="inline-flex items-center justify-center p-2 rounded-md text-gray-500 hover:text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 lg:hidden"
      >
        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
        </svg>
      </button>

      <!-- Right side items -->
      <div class="flex items-center space-x-4">
        <!-- Activity Notifications -->
        <ActivityNotifications :activities="activities" />
        
        <!-- User dropdown -->
        <UserDropdown :user="user" />
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