<template>
    <div class="min-h-screen bg-gray-50">
        <!-- Navigation Component -->
        <AppNavigation ref="navigationRef" />

        <!-- App Header -->
        <AppHeader @toggleMobileMenu="handleToggleMobileMenu" :activities="activities" />

        <!-- Main Content -->
        <div class="lg:pl-64 pt-20">
            <!-- Page Header -->
            <header v-if="$slots.header" class="bg-white border-b border-gray-200">
                <div class="px-4 sm:px-6 lg:px-8 py-6">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main class="min-h-[calc(100vh-8rem)]">
                <slot />
            </main>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue'
import AppNavigation from '@/Components/AppNavigation.vue'
import AppHeader from '@/Components/AppHeader.vue'

// Props
defineProps({
    activities: {
        type: Array,
        default: () => []
    }
})

// Navigation reference
const navigationRef = ref(null)

// Mobile menu handler
const handleToggleMobileMenu = () => {
    if (navigationRef.value) {
        navigationRef.value.openMobileMenu()
    }
}
</script>

<style scoped>
/* Add Inter font if not already included */
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

body {
    font-family: 'Inter', ui-sans-serif, system-ui, sans-serif;
}

/* Ensure content doesn't get hidden behind fixed header */
:deep(.page-content) {
    margin-top: 0;
    padding-top: 1rem;
}

/* Ensure buttons and interactive elements are not hidden */
:deep(.fixed-header-offset) {
    margin-top: 4rem;
}

/* Ensure modals and dropdowns appear above header */
:deep(.modal), :deep(.dropdown-menu) {
    z-index: 60;
}
</style>