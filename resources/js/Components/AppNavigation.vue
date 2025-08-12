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
                <!-- Dashboard -->
                <NavLink 
                    name="Dashboard"
                    :href="route('dashboard')"
                    :active="currentRoute === '/dashboard'"
                    icon="dashboard"
                    @click="closeMobileMenu"
                />
                
                <!-- Separator -->
                <div class="border-t border-gray-200 my-3"></div>
                
                <!-- Inmobiliaria Section -->
                <div v-if="hasAnyPropertyPermission" class="space-y-1">
                    <div class="px-3 py-1">
                        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Gestión Inmobiliaria
                        </h3>
                    </div>
                    
                    <NavLink 
                        v-if="hasProjectsPermission"
                        name="Proyectos"
                        :href="route('projects.index')"
                        :active="currentRoute.startsWith('/projects')"
                        icon="projects"
                        @click="closeMobileMenu"
                    />
                    
                    <NavLink 
                        v-if="hasPropertiesPermission"
                        name="Propiedades"
                        :href="route('properties.index')"
                        :active="currentRoute.startsWith('/properties')"
                        icon="properties"
                        @click="closeMobileMenu"
                    />
                    
                    <div class="border-t border-gray-200 my-3"></div>
                </div>
                
                <!-- People Section -->
                <div v-if="hasAnyPeoplePermission" class="space-y-1">
                    <div class="px-3 py-1">
                        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Gestión de Personas
                        </h3>
                    </div>
                    
                    <NavLink 
                        v-if="hasClientsPermission"
                        name="Clientes"
                        :href="route('clients.index')"
                        :active="currentRoute.startsWith('/clients')"
                        icon="clients"
                        @click="closeMobileMenu"
                    />
                    
                    <NavLink 
                        v-if="hasAgentsPermission"
                        name="Agentes"
                        :href="route('agents.index')"
                        :active="currentRoute.startsWith('/agents')"
                        icon="agents"
                        @click="closeMobileMenu"
                    />
                    
                    <div class="border-t border-gray-200 my-3"></div>
                </div>
                
                <!-- Activities -->
                <NavLink 
                    v-if="hasVisitsPermission"
                    name="Visitas"
                    :href="route('visits.index')"
                    :active="currentRoute.startsWith('/visits')"
                    icon="visits"
                    @click="closeMobileMenu"
                />
                
                <!-- Admin Section -->
                <div v-if="hasAnyAdminPermission" class="space-y-1">
                    <div class="border-t border-gray-200 my-3"></div>
                    <div class="px-3 py-1">
                        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Administración
                        </h3>
                    </div>
                    
                    <NavLink 
                        v-if="hasUsersPermission"
                        name="Usuarios"
                        :href="route('users.index')"
                        :active="currentRoute.startsWith('/users')"
                        icon="users"
                        @click="closeMobileMenu"
                    />
                    
                    <NavLink 
                        v-if="hasRolesPermission"
                        name="Roles y Permisos"
                        :href="route('roles.index')"
                        :active="currentRoute.startsWith('/roles')"
                        icon="roles"
                        @click="closeMobileMenu"
                    />
                </div>
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
                <!-- Dashboard -->
                <NavLink 
                    name="Dashboard"
                    :href="route('dashboard')"
                    :active="currentRoute === '/dashboard'"
                    icon="dashboard"
                />
                
                <!-- Separator -->
                <div class="border-t border-gray-200 my-3"></div>
                
                <!-- Inmobiliaria Section -->
                <div v-if="hasAnyPropertyPermission" class="space-y-1">
                    <div class="px-3 py-1">
                        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Gestión Inmobiliaria
                        </h3>
                    </div>
                    
                    <NavLink 
                        v-if="hasProjectsPermission"
                        name="Proyectos"
                        :href="route('projects.index')"
                        :active="currentRoute.startsWith('/projects')"
                        icon="projects"
                    />
                    
                    <NavLink 
                        v-if="hasPropertiesPermission"
                        name="Propiedades"
                        :href="route('properties.index')"
                        :active="currentRoute.startsWith('/properties')"
                        icon="properties"
                    />
                    
                    <div class="border-t border-gray-200 my-3"></div>
                </div>
                
                <!-- People Section -->
                <div v-if="hasAnyPeoplePermission" class="space-y-1">
                    <div class="px-3 py-1">
                        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Gestión de Personas
                        </h3>
                    </div>
                    
                    <NavLink 
                        v-if="hasClientsPermission"
                        name="Clientes"
                        :href="route('clients.index')"
                        :active="currentRoute.startsWith('/clients')"
                        icon="clients"
                    />
                    
                    <NavLink 
                        v-if="hasAgentsPermission"
                        name="Agentes"
                        :href="route('agents.index')"
                        :active="currentRoute.startsWith('/agents')"
                        icon="agents"
                    />
                    
                    <div class="border-t border-gray-200 my-3"></div>
                </div>
                
                <!-- Activities -->
                <NavLink 
                    v-if="hasVisitsPermission"
                    name="Visitas"
                    :href="route('visits.index')"
                    :active="currentRoute.startsWith('/visits')"
                    icon="visits"
                />
                
                <!-- Admin Section -->
                <div v-if="hasAnyAdminPermission" class="space-y-1">
                    <div class="border-t border-gray-200 my-3"></div>
                    <div class="px-3 py-1">
                        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            Administración
                        </h3>
                    </div>
                    
                    <NavLink 
                        v-if="hasUsersPermission"
                        name="Usuarios"
                        :href="route('users.index')"
                        :active="currentRoute.startsWith('/users')"
                        icon="users"
                    />
                    
                    <NavLink 
                        v-if="hasRolesPermission"
                        name="Roles y Permisos"
                        :href="route('roles.index')"
                        :active="currentRoute.startsWith('/roles')"
                        icon="roles"
                    />
                </div>
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

// Permission checks
const hasProjectsPermission = computed(() => user.value?.permissions?.some(p => p.slug === 'projects-view') || user.value?.is_super_admin)
const hasPropertiesPermission = computed(() => user.value?.permissions?.some(p => p.slug === 'properties-view') || user.value?.is_super_admin)
const hasClientsPermission = computed(() => user.value?.permissions?.some(p => p.slug === 'clients-view') || user.value?.is_super_admin)
const hasAgentsPermission = computed(() => user.value?.permissions?.some(p => p.slug === 'agents-view') || user.value?.is_super_admin)
const hasVisitsPermission = computed(() => user.value?.permissions?.some(p => p.slug === 'visits-view') || user.value?.is_super_admin)
const hasUsersPermission = computed(() => user.value?.permissions?.some(p => p.slug === 'users-view') || user.value?.is_super_admin)
const hasRolesPermission = computed(() => user.value?.permissions?.some(p => p.slug === 'roles-view') || user.value?.is_super_admin)

// Section visibility
const hasAnyPropertyPermission = computed(() => hasProjectsPermission.value || hasPropertiesPermission.value)
const hasAnyPeoplePermission = computed(() => hasClientsPermission.value || hasAgentsPermission.value)
const hasAnyAdminPermission = computed(() => hasUsersPermission.value || hasRolesPermission.value)

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