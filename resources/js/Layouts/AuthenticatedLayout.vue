<template>
    <div class="min-h-screen bg-gray-50">
        <!-- Navigation Component -->
        <AppNavigation />

        <!-- Main Content -->
        <div class="lg:pl-64">
            <!-- Page Header -->
            <header v-if="$slots.header" class="bg-white border-b border-gray-200">
                <div class="px-4 sm:px-6 lg:px-8 py-6">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main class="min-h-[calc(100vh-4rem)]">
                <slot />
            </main>
        </div>
    </div>
</template>

<script setup>
import AppNavigation from '@/Components/AppNavigation.vue'
</script>

<style scoped>
/* Add Inter font if not already included */
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

body {
    font-family: 'Inter', ui-sans-serif, system-ui, sans-serif;
}
</style>