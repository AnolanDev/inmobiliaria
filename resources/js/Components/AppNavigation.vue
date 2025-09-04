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
                <Link :href="route('dashboard')" class="flex items-center gap-3 hover:opacity-80 transition-opacity">
                    <img src="/ts/logo.png" alt="Tierra Soñada" class="h-6 w-auto" />
                    <h2 class="text-lg font-bold text-gray-900">Tierra Soñada</h2>
                </Link>
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
            <nav class="flex-1 px-3 py-6 space-y-0.5 overflow-y-auto">
                    <!-- Dashboard -->
                <NavLink 
                    name="Dashboard"
                    :href="route('dashboard')"
                    :active="currentRoute === '/dashboard'"
                    icon="dashboard"
                    @click="closeMobileMenu"
                />
                
                <!-- Separator -->
                <div class="border-t border-gray-200 my-1"></div>
                
                <!-- Inmobiliaria Section -->
                <div v-if="hasAnyPropertyPermission" class="space-y-1">
                    <button 
                        @click="toggleSection('inmobiliaria')"
                        class="w-full flex items-center justify-between px-3 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider hover:text-gray-700 hover:bg-gray-50 rounded-lg transition-colors"
                    >
                        <span>Gestión Inmobiliaria</span>
                        <svg 
                            :class="[
                                'w-4 h-4 transition-transform duration-200',
                                collapsedSections.inmobiliaria ? 'rotate-0' : 'rotate-90'
                            ]" 
                            fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                    
                    <div v-show="!collapsedSections.inmobiliaria" class="pl-3 space-y-0.5">
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
                    </div>
                    
                    <div class="border-t border-gray-200 my-1"></div>
                </div>
                
                <!-- People Section -->
                <div v-if="hasAnyPeoplePermission" class="space-y-1">
                    <button 
                        @click="toggleSection('personas')"
                        class="w-full flex items-center justify-between px-3 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider hover:text-gray-700 hover:bg-gray-50 rounded-lg transition-colors"
                    >
                        <span>Gestión de Personas</span>
                        <svg 
                            :class="[
                                'w-4 h-4 transition-transform duration-200',
                                collapsedSections.personas ? 'rotate-0' : 'rotate-90'
                            ]" 
                            fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                    
                    <div v-show="!collapsedSections.personas" class="pl-3 space-y-0.5">
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
                    </div>
                    
                    <div class="border-t border-gray-200 my-1"></div>
                </div>
                
                <!-- Marketing Section -->
                <div v-if="hasAnyMarketingPermission" class="space-y-1">
                    <button 
                        @click="toggleSection('marketing')"
                        class="w-full flex items-center justify-between px-3 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider hover:text-gray-700 hover:bg-gray-50 rounded-lg transition-colors"
                    >
                        <span>Marketing</span>
                        <svg 
                            :class="[
                                'w-4 h-4 transition-transform duration-200',
                                collapsedSections.marketing ? 'rotate-0' : 'rotate-90'
                            ]" 
                            fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                    
                    <div v-show="!collapsedSections.marketing" class="pl-3 space-y-0.5">
                        <NavLink 
                            v-if="hasCampaignsPermission"
                            name="Campañas"
                            :href="route('campaigns.index')"
                            :active="currentRoute.startsWith('/campaigns')"
                            icon="campaigns"
                            @click="closeMobileMenu"
                        />
                        
                        <NavLink 
                            v-if="hasLeadsPermission"
                            name="Leads"
                            :href="route('leads.index')"
                            :active="currentRoute.startsWith('/leads')"
                            icon="leads"
                            @click="closeMobileMenu"
                        />
                        
                        <NavLink 
                            v-if="hasEmailMarketingPermission"
                            name="Email Templates"
                            :href="route('email-templates.index')"
                            :active="currentRoute.startsWith('/email-templates')"
                            icon="email-templates"
                            @click="closeMobileMenu"
                        />
                        
                        <NavLink 
                            v-if="hasEmailMarketingPermission"
                            name="Email Campaigns"
                            :href="route('email-campaigns.index')"
                            :active="currentRoute.startsWith('/email-campaigns')"
                            icon="email-campaigns"
                            @click="closeMobileMenu"
                        />
                        
                        <NavLink 
                            v-if="hasBlogsPermission"
                            name="Blogs"
                            :href="route('blogs.index')"
                            :active="currentRoute.startsWith('/blogs')"
                            icon="blogs"
                            @click="closeMobileMenu"
                        />
                    </div>
                    
                    <div class="border-t border-gray-200 my-1"></div>
                </div>
                
                <!-- Activities Section -->
                <div v-if="hasActivitiesPermission || hasVisitsPermission" class="space-y-1">
                    <button 
                        @click="toggleSection('actividades')"
                        class="w-full flex items-center justify-between px-3 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider hover:text-gray-700 hover:bg-gray-50 rounded-lg transition-colors"
                    >
                        <span>Actividades</span>
                        <svg 
                            :class="[
                                'w-4 h-4 transition-transform duration-200',
                                collapsedSections.actividades ? 'rotate-0' : 'rotate-90'
                            ]" 
                            fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                    
                    <div v-show="!collapsedSections.actividades" class="pl-3 space-y-0.5">
                        <NavLink 
                            v-if="hasActivitiesPermission"
                            name="Actividades"
                            :href="route('activities.index')"
                            :active="currentRoute.startsWith('/activities')"
                            icon="activities"
                            @click="closeMobileMenu"
                        />
                        
                        <NavLink 
                            v-if="hasVisitsPermission"
                            name="Visitas"
                            :href="route('visits.index')"
                            :active="currentRoute.startsWith('/visits')"
                            icon="visits"
                            @click="closeMobileMenu"
                        />
                    </div>
                    
                    <div class="border-t border-gray-200 my-1"></div>
                </div>
                
                <!-- Admin Section -->
                <div v-if="hasAnyAdminPermission" class="space-y-1">
                    <div class="border-t border-gray-200 my-1"></div>
                    <button 
                        @click="toggleSection('administracion')"
                        class="w-full flex items-center justify-between px-3 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider hover:text-gray-700 hover:bg-gray-50 rounded-lg transition-colors"
                    >
                        <span>Administración</span>
                        <svg 
                            :class="[
                                'w-4 h-4 transition-transform duration-200',
                                collapsedSections.administracion ? 'rotate-0' : 'rotate-90'
                            ]" 
                            fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                    
                    <div v-show="!collapsedSections.administracion" class="pl-3 space-y-0.5">
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
                        
                        <NavLink 
                            v-if="hasEmailMarketingConfigPermission"
                            name="Configuración Email"
                            :href="route('email-marketing.config')"
                            :active="currentRoute.startsWith('/email-marketing/config')"
                            icon="email-config"
                            @click="closeMobileMenu"
                        />
                    </div>
                </div>
            </nav>
            
            <!-- Mobile User Section -->
            <div class="border-t border-gray-100 p-4">
                <UserDropdown :user="user" :is-mobile="true" />
            </div>
        </div>
    </div>

    <!-- Desktop Sidebar -->
    <div class="hidden lg:fixed lg:top-[5rem] lg:bottom-0 lg:left-0 lg:z-40 lg:w-64 lg:flex lg:flex-col">
        <div class="flex flex-col flex-1 bg-white border-r border-gray-200">
            <!-- Desktop Navigation -->
            <nav class="flex-1 px-3 py-3 space-y-0.5 overflow-y-auto">
                    <!-- Dashboard -->
                <NavLink 
                    name="Dashboard"
                    :href="route('dashboard')"
                    :active="currentRoute === '/dashboard'"
                    icon="dashboard"
                />
                
                <!-- Separator -->
                <div class="border-t border-gray-200 my-1"></div>
                
                <!-- Inmobiliaria Section -->
                <div v-if="hasAnyPropertyPermission" class="space-y-1">
                    <button 
                        @click="toggleSection('inmobiliaria')"
                        class="w-full flex items-center justify-between px-3 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider hover:text-gray-700 hover:bg-gray-50 rounded-lg transition-colors"
                    >
                        <span>Gestión Inmobiliaria</span>
                        <svg 
                            :class="[
                                'w-4 h-4 transition-transform duration-200',
                                collapsedSections.inmobiliaria ? 'rotate-0' : 'rotate-90'
                            ]" 
                            fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                    
                    <div v-show="!collapsedSections.inmobiliaria" class="pl-3 space-y-0.5">
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
                    </div>
                    
                    <div class="border-t border-gray-200 my-1"></div>
                </div>
                
                <!-- People Section -->
                <div v-if="hasAnyPeoplePermission" class="space-y-1">
                    <button 
                        @click="toggleSection('personas')"
                        class="w-full flex items-center justify-between px-3 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider hover:text-gray-700 hover:bg-gray-50 rounded-lg transition-colors"
                    >
                        <span>Gestión de Personas</span>
                        <svg 
                            :class="[
                                'w-4 h-4 transition-transform duration-200',
                                collapsedSections.personas ? 'rotate-0' : 'rotate-90'
                            ]" 
                            fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                    
                    <div v-show="!collapsedSections.personas" class="pl-3 space-y-0.5">
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
                    </div>
                    
                    <div class="border-t border-gray-200 my-1"></div>
                </div>
                
                <!-- Marketing Section -->
                <div v-if="hasAnyMarketingPermission" class="space-y-1">
                    <button 
                        @click="toggleSection('marketing')"
                        class="w-full flex items-center justify-between px-3 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider hover:text-gray-700 hover:bg-gray-50 rounded-lg transition-colors"
                    >
                        <span>Marketing</span>
                        <svg 
                            :class="[
                                'w-4 h-4 transition-transform duration-200',
                                collapsedSections.marketing ? 'rotate-0' : 'rotate-90'
                            ]" 
                            fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                    
                    <div v-show="!collapsedSections.marketing" class="pl-3 space-y-0.5">
                        <NavLink 
                            v-if="hasCampaignsPermission"
                            name="Campañas"
                            :href="route('campaigns.index')"
                            :active="currentRoute.startsWith('/campaigns')"
                            icon="campaigns"
                        />
                        
                        <NavLink 
                            v-if="hasLeadsPermission"
                            name="Leads"
                            :href="route('leads.index')"
                            :active="currentRoute.startsWith('/leads')"
                            icon="leads"
                        />
                        
                        <NavLink 
                            v-if="hasEmailMarketingPermission"
                            name="Email Templates"
                            :href="route('email-templates.index')"
                            :active="currentRoute.startsWith('/email-templates')"
                            icon="email-templates"
                        />
                        
                        <NavLink 
                            v-if="hasEmailMarketingPermission"
                            name="Email Campaigns"
                            :href="route('email-campaigns.index')"
                            :active="currentRoute.startsWith('/email-campaigns')"
                            icon="email-campaigns"
                        />
                        
                        <NavLink 
                            v-if="hasBlogsPermission"
                            name="Blogs"
                            :href="route('blogs.index')"
                            :active="currentRoute.startsWith('/blogs')"
                            icon="blogs"
                        />
                    </div>
                    
                    <div class="border-t border-gray-200 my-1"></div>
                </div>
                
                <!-- Activities Section -->
                <div v-if="hasActivitiesPermission || hasVisitsPermission" class="space-y-1">
                    <button 
                        @click="toggleSection('actividades')"
                        class="w-full flex items-center justify-between px-3 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider hover:text-gray-700 hover:bg-gray-50 rounded-lg transition-colors"
                    >
                        <span>Actividades</span>
                        <svg 
                            :class="[
                                'w-4 h-4 transition-transform duration-200',
                                collapsedSections.actividades ? 'rotate-0' : 'rotate-90'
                            ]" 
                            fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                    
                    <div v-show="!collapsedSections.actividades" class="pl-3 space-y-0.5">
                        <NavLink 
                            v-if="hasActivitiesPermission"
                            name="Actividades"
                            :href="route('activities.index')"
                            :active="currentRoute.startsWith('/activities')"
                            icon="activities"
                        />
                        
                        <NavLink 
                            v-if="hasVisitsPermission"
                            name="Visitas"
                            :href="route('visits.index')"
                            :active="currentRoute.startsWith('/visits')"
                            icon="visits"
                        />
                    </div>
                    
                    <div class="border-t border-gray-200 my-1"></div>
                </div>
                
                <!-- Admin Section -->
                <div v-if="hasAnyAdminPermission" class="space-y-1">
                    <div class="border-t border-gray-200 my-1"></div>
                    <button 
                        @click="toggleSection('administracion')"
                        class="w-full flex items-center justify-between px-3 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider hover:text-gray-700 hover:bg-gray-50 rounded-lg transition-colors"
                    >
                        <span>Administración</span>
                        <svg 
                            :class="[
                                'w-4 h-4 transition-transform duration-200',
                                collapsedSections.administracion ? 'rotate-0' : 'rotate-90'
                            ]" 
                            fill="none" stroke="currentColor" viewBox="0 0 24 24"
                        >
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                    
                    <div v-show="!collapsedSections.administracion" class="pl-3 space-y-0.5">
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
                        
                        <NavLink 
                            v-if="hasEmailMarketingConfigPermission"
                            name="Configuración Email"
                            :href="route('email-marketing.config')"
                            :active="currentRoute.startsWith('/email-marketing/config')"
                            icon="email-config"
                        />
                    </div>
                </div>
            </nav>
        </div>
    </div>

    <!-- Mobile Menu Button -->
    <div class="lg:hidden">
        <button
            @click="openMobileMenu"
            class="fixed top-4 left-4 z-30 p-2 text-gray-600 bg-white border border-gray-200 rounded-lg shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-green-500" style="--tw-ring-color: #00bf63;"
        >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
            </svg>
        </button>
    </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { usePage, Link } from '@inertiajs/vue3'
import NavLink from './Navigation/NavLink.vue'
import UserDropdown from './Navigation/UserDropdown.vue'

// Mobile menu state
const isMobileMenuOpen = ref(false)

// Get the current active section based on route
const getActiveSection = (route) => {
    if (route.startsWith('/projects') || route.startsWith('/properties')) {
        return 'inmobiliaria'
    }
    if (route.startsWith('/clients') || route.startsWith('/agents')) {
        return 'personas'
    }
    if (route.startsWith('/campaigns') || route.startsWith('/leads') || 
        route.startsWith('/email-templates') || route.startsWith('/email-campaigns') || 
        route.startsWith('/blogs')) {
        return 'marketing'
    }
    if (route.startsWith('/activities') || route.startsWith('/visits')) {
        return 'actividades'
    }
    if (route.startsWith('/users') || route.startsWith('/roles') || 
        route.startsWith('/email-marketing/config')) {
        return 'administracion'
    }
    return null
}

// Accordion state for navigation sections - initialize with all collapsed
const collapsedSections = ref({
    inmobiliaria: true,
    personas: true, 
    marketing: true,
    actividades: true,
    administracion: true
})

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
const hasCampaignsPermission = computed(() => user.value?.permissions?.some(p => p.slug === 'campaigns-view') || user.value?.is_super_admin)
const hasLeadsPermission = computed(() => user.value?.permissions?.some(p => p.slug === 'leads-view') || user.value?.is_super_admin)
const hasActivitiesPermission = computed(() => user.value?.permissions?.some(p => p.slug === 'activities-view') || user.value?.is_super_admin)
const hasEmailMarketingPermission = computed(() => user.value?.permissions?.some(p => p.slug === 'email-marketing-view') || user.value?.is_super_admin)
const hasEmailMarketingConfigPermission = computed(() => user.value?.permissions?.some(p => p.slug === 'email-marketing-config') || user.value?.is_super_admin)
const hasBlogsPermission = computed(() => user.value?.permissions?.some(p => p.slug === 'blogs-view') || user.value?.is_super_admin)
const hasUsersPermission = computed(() => user.value?.permissions?.some(p => p.slug === 'users-view') || user.value?.is_super_admin)
const hasRolesPermission = computed(() => user.value?.permissions?.some(p => p.slug === 'roles-view') || user.value?.is_super_admin)

// Section visibility
const hasAnyPropertyPermission = computed(() => hasProjectsPermission.value || hasPropertiesPermission.value)
const hasAnyPeoplePermission = computed(() => hasClientsPermission.value || hasAgentsPermission.value)
const hasAnyMarketingPermission = computed(() => hasCampaignsPermission.value || hasLeadsPermission.value || hasEmailMarketingPermission.value || hasBlogsPermission.value)
const hasAnyAdminPermission = computed(() => hasUsersPermission.value || hasRolesPermission.value || hasEmailMarketingConfigPermission.value)

// Mobile menu functions
const openMobileMenu = () => {
    isMobileMenuOpen.value = true
}

const closeMobileMenu = () => {
    isMobileMenuOpen.value = false
}

// Toggle accordion sections
const toggleSection = (section) => {
    collapsedSections.value[section] = !collapsedSections.value[section]
}

// Watch for route changes and update active section
watch(currentRoute, (newRoute) => {
    const activeSection = getActiveSection(newRoute)
    collapsedSections.value = {
        inmobiliaria: activeSection !== 'inmobiliaria',
        personas: activeSection !== 'personas', 
        marketing: activeSection !== 'marketing',
        actividades: activeSection !== 'actividades',
        administracion: activeSection !== 'administracion'
    }
}, { immediate: true })

// Expose methods to parent components
defineExpose({
    openMobileMenu,
    closeMobileMenu
})
</script>

<style scoped>
/* Custom styles if needed */
.backdrop-blur-sm {
    backdrop-filter: blur(4px);
}
</style>