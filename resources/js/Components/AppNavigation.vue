<template>
    <!-- Mobile Menu Overlay -->
    <div 
        v-if="isMobileMenuOpen" 
        class="fixed inset-0 z-50 lg:hidden"
        @click="closeMobileMenu"
    >
        <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm" aria-hidden="true"></div>
    </div>

    <!-- Mobile Sidebar -->
    <div 
        :class="[
            'fixed inset-y-0 left-0 z-50 w-72 transform bg-white shadow-xl transition-transform duration-300 ease-in-out lg:hidden',
            isMobileMenuOpen ? 'translate-x-0' : '-translate-x-full'
        ]"
    >
        <div class="flex h-full flex-col">
            <!-- Mobile Header -->
            <div class="flex h-16 items-center justify-between px-6 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M10.3 8.2l-0.9 0.9c-0.1 0.1-0.1 0.4 0 0.5l5.1 5.1c0.1 0.1 0.4 0.1 0.5 0l0.9-0.9c0.1-0.1 0.1-0.4 0-0.5l-5.1-5.1c-0.1-0.1-0.4-0.1-0.5 0z"/>
                            <path d="M8.7 8.7l0.9-0.9c0.1-0.1 0.4-0.1 0.5 0l5.1 5.1c0.1 0.1 0.1 0.4 0 0.5l-0.9 0.9c-0.1 0.1-0.4 0.1-0.5 0l-5.1-5.1c-0.1-0.1-0.1-0.4 0-0.5z"/>
                            <path d="M12 2C6.477 2 2 6.477 2 12s4.477 10 10 10 10-4.477 10-10S17.523 2 12 2z"/>
                        </svg>
                    </div>
                    <h2 class="text-lg font-bold text-gray-900">InmoApp</h2>
                </div>
                <button
                    @click="closeMobileMenu"
                    class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-colors"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Mobile Navigation -->
            <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
                <NavLink 
                    v-for="item in navigationItems" 
                    :key="item.name"
                    :href="item.href"
                    :active="item.current"
                    :icon="item.icon"
                    :name="item.name"
                    @click="closeMobileMenu"
                />
            </nav>

            <!-- Mobile User Section -->
            <div class="border-t border-gray-100 p-4">
                <UserDropdown :user="user" :is-mobile="true" />
            </div>
        </div>
    </div>

    <!-- Desktop Sidebar -->
    <div class="hidden lg:fixed lg:inset-y-0 lg:left-0 lg:z-40 lg:w-64 lg:flex lg:flex-col">
        <div class="flex flex-col flex-1 bg-white border-r border-gray-200">
            <!-- Desktop Header -->
            <div class="flex h-16 items-center px-6 border-b border-gray-100">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M10.3 8.2l-0.9 0.9c-0.1 0.1-0.1 0.4 0 0.5l5.1 5.1c0.1 0.1 0.4 0.1 0.5 0l0.9-0.9c0.1-0.1 0.1-0.4 0-0.5l-5.1-5.1c-0.1-0.1-0.4-0.1-0.5 0z"/>
                            <path d="M8.7 8.7l0.9-0.9c0.1-0.1 0.4-0.1 0.5 0l5.1 5.1c0.1 0.1 0.1 0.4 0 0.5l-0.9 0.9c-0.1 0.1-0.4 0.1-0.5 0l-5.1-5.1c-0.1-0.1-0.1-0.4 0-0.5z"/>
                            <path d="M12 2C6.477 2 2 6.477 2 12s4.477 10 10 10 10-4.477 10-10S17.523 2 12 2z"/>
                        </svg>
                    </div>
                    <h1 class="text-lg font-bold text-gray-900">InmoApp</h1>
                </div>
            </div>

            <!-- Desktop Navigation -->
            <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
                <NavLink 
                    v-for="item in navigationItems" 
                    :key="item.name"
                    :href="item.href"
                    :active="item.current"
                    :icon="item.icon"
                    :name="item.name"
                />
            </nav>

            <!-- Desktop User Section -->
            <div class="border-t border-gray-100 p-4">
                <UserDropdown :user="user" />
            </div>
        </div>
    </div>

    <!-- Mobile Menu Button -->
    <div class="lg:hidden">
        <button
            @click="openMobileMenu"
            class="fixed top-4 left-4 z-30 p-2 text-gray-600 bg-white border border-gray-200 rounded-lg shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { usePage } from '@inertiajs/vue3'
import NavLink from './Navigation/NavLink.vue'
import UserDropdown from './Navigation/UserDropdown.vue'

// Mobile menu state
const isMobileMenuOpen = ref(false)

// Get current user from Inertia
const page = usePage()
const user = computed(() => page.props.auth?.user)

// Get current route for active state
const currentRoute = computed(() => page.url)

// Navigation items
const navigationItems = computed(() => [
    {
        name: 'Dashboard',
        href: route('dashboard'),
        current: currentRoute.value === '/dashboard',
        icon: 'dashboard'
    },
    {
        name: 'Proyectos',
        href: route('projects.index'),
        current: currentRoute.value.startsWith('/projects'),
        icon: 'projects'
    },
    {
        name: 'Propiedades',
        href: route('properties.index'),
        current: currentRoute.value.startsWith('/properties'),
        icon: 'properties'
    },
    {
        name: 'Agentes',
        href: route('agents.index'),
        current: currentRoute.value.startsWith('/agents'),
        icon: 'agents'
    },
    {
        name: 'Clientes',
        href: route('clients.index'),
        current: currentRoute.value.startsWith('/clients'),
        icon: 'clients'
    },
    {
        name: 'Visitas',
        href: '#', // route('visits.index') when implemented
        current: currentRoute.value.startsWith('/visits'),
        icon: 'visits'
    },
    {
        name: 'Estadísticas',
        href: '#', // route('statistics.index') when implemented
        current: currentRoute.value.startsWith('/statistics'),
        icon: 'statistics'
    },
    {
        name: 'Configuración',
        href: '#', // route('settings.index') when implemented
        current: currentRoute.value.startsWith('/settings'),
        icon: 'settings'
    }
])

// Mobile menu functions
const openMobileMenu = () => {
    isMobileMenuOpen.value = true
}

const closeMobileMenu = () => {
    isMobileMenuOpen.value = false
}
</script>

<style scoped>
/* Custom styles if needed */
.backdrop-blur-sm {
    backdrop-filter: blur(4px);
}
</style>