<template>
    <div class="relative">
        <!-- User Button -->
        <button
            @click="toggleDropdown"
            :class="[
                'w-full flex items-center gap-3 px-3 py-2.5 text-sm text-left text-gray-700 rounded-xl transition-all duration-200 group',
                isOpen ? 'bg-blue-50 border-l-4 border-blue-600' : 'hover:bg-gray-100 hover:border-l-4 hover:border-blue-200 border-l-4 border-transparent'
            ]"
        >
            <div class="w-8 h-8 bg-gradient-to-br from-blue-600 to-blue-700 rounded-xl flex items-center justify-center flex-shrink-0 transition-transform duration-200 group-hover:scale-110 shadow-md">
                <span class="text-white font-bold text-sm">
                    {{ user?.name?.charAt(0)?.toUpperCase() || 'U' }}
                </span>
            </div>
            <div class="flex-1 min-w-0">
                <p class="font-semibold text-gray-900 truncate">{{ user?.name || 'Usuario' }}</p>
                <p class="text-xs text-gray-500 truncate">{{ user?.email || 'usuario@email.com' }}</p>
            </div>
            <svg 
                :class="[
                    'w-4 h-4 transition-all duration-200', 
                    isOpen ? 'rotate-180 text-blue-600' : 'text-gray-500 group-hover:text-blue-600'
                ]"
                fill="none" stroke="currentColor" viewBox="0 0 24 24"
            >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m19 9-7 7-7-7" />
            </svg>
        </button>

        <!-- Dropdown Menu -->
        <div 
            v-if="isOpen"
            :class="[
                'absolute bottom-full left-0 right-0 mb-2 bg-white border border-gray-200 rounded-xl shadow-lg z-50',
                isMobile ? 'relative bottom-auto mb-0 mt-2' : ''
            ]"
        >
            <div class="py-2">
                <!-- Profile Link -->
                <Link
                    :href="route('profile.edit')"
                    class="flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-700 transition-all duration-200 group"
                    @click="closeDropdown"
                >
                    <svg class="w-4 h-4 text-gray-500 group-hover:text-blue-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" 
                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                    </svg>
                    Mi Perfil
                </Link>

                <!-- Theme Toggle (Optional) -->
                <button
                    @click="toggleTheme"
                    class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-gray-700 hover:bg-yellow-50 hover:text-yellow-700 transition-all duration-200 group"
                    disabled
                >
                    <svg class="w-4 h-4 text-gray-500 group-hover:text-yellow-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" 
                              d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                    <span class="flex-1 text-left">Modo Oscuro</span>
                    <span class="text-xs text-gray-400 bg-gray-100 px-2 py-0.5 rounded-full">Próximamente</span>
                </button>

                <div class="border-t border-gray-100 mt-2 pt-2">
                    <!-- Logout -->
                    <Link
                        :href="route('logout')"
                        method="post"
                        as="button"
                        class="w-full flex items-center gap-3 px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 hover:text-red-700 transition-all duration-200 group"
                        @click="closeDropdown"
                    >
                        <svg class="w-4 h-4 group-hover:text-red-700 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" 
                                  d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        Cerrar Sesión
                    </Link>
                </div>
            </div>
        </div>
    </div>

    <!-- Overlay for closing dropdown -->
    <div 
        v-if="isOpen && !isMobile" 
        class="fixed inset-0 z-40" 
        @click="closeDropdown"
    ></div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { Link } from '@inertiajs/vue3'

const props = defineProps({
    user: {
        type: Object,
        default: () => ({})
    },
    isMobile: {
        type: Boolean,
        default: false
    }
})

const isOpen = ref(false)

const toggleDropdown = () => {
    isOpen.value = !isOpen.value
}

const closeDropdown = () => {
    isOpen.value = false
}

const toggleTheme = () => {
    // Theme toggle functionality will be implemented later
    console.log('Theme toggle clicked - Coming soon!')
}

// Close dropdown on escape key
const handleEscape = (e) => {
    if (e.key === 'Escape') {
        closeDropdown()
    }
}

onMounted(() => {
    document.addEventListener('keydown', handleEscape)
})

onUnmounted(() => {
    document.removeEventListener('keydown', handleEscape)
})
</script>