<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Email Campaigns</h2>
                    <p class="text-sm text-gray-600 mt-1">
                        Gestiona y envía campañas de email marketing
                    </p>
                </div>
                
                <div class="flex items-center gap-3">
                    <Link
                        :href="route('email-campaigns.create')"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors duration-200"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Nueva Campaña
                    </Link>
                </div>
            </div>
        </template>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-8">
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total</p>
                            <p class="text-2xl font-bold text-gray-900">{{ stats.total }}</p>
                        </div>
                        <div class="p-3 bg-green-50 rounded-lg">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Borradores</p>
                            <p class="text-2xl font-bold text-yellow-600">{{ stats.draft }}</p>
                        </div>
                        <div class="p-3 bg-yellow-50 rounded-lg">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Programadas</p>
                            <p class="text-2xl font-bold text-green-600">{{ stats.scheduled }}</p>
                        </div>
                        <div class="p-3 bg-green-50 rounded-lg">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Enviando</p>
                            <p class="text-2xl font-bold text-orange-600">{{ stats.sending }}</p>
                        </div>
                        <div class="p-3 bg-orange-50 rounded-lg">
                            <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Enviadas</p>
                            <p class="text-2xl font-bold text-green-600">{{ stats.sent }}</p>
                        </div>
                        <div class="p-3 bg-green-50 rounded-lg">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Filters -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-6">
                <div class="flex flex-col sm:flex-row gap-4">
                    <div class="flex-1">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Buscar</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input
                                v-model="searchQuery"
                                type="text"
                                placeholder="Buscar por nombre o descripción..."
                                class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm"
                                @input="debouncedSearch"
                            />
                        </div>
                    </div>

                    <div class="w-full sm:w-48">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tipo</label>
                        <select
                            v-model="selectedType"
                            class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm"
                            @change="applyFilters"
                        >
                            <option value="">Todos los tipos</option>
                            <option v-for="(label, value) in types" :key="value" :value="value">
                                {{ label }}
                            </option>
                        </select>
                    </div>

                    <div class="w-full sm:w-48">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                        <select
                            v-model="selectedStatus"
                            class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm"
                            @change="applyFilters"
                        >
                            <option value="">Todos los estados</option>
                            <option v-for="(label, value) in statuses" :key="value" :value="value">
                                {{ label }}
                            </option>
                        </select>
                    </div>

                    <div class="w-full sm:w-48">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Filtros</label>
                        <select
                            v-model="selectedFilter"
                            class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm"
                            @change="applyFilters"
                        >
                            <option value="">Todos</option>
                            <option value="ab_test">Solo A/B Testing</option>
                            <option value="drip">Solo Drip Campaigns</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Campaigns List -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900">Campañas</h3>
                </div>

                <div v-if="campaigns.data.length === 0" class="p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 01.865-.501 48.172 48.172 0 003.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0012 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018z" />
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">No hay campañas</h3>
                    <p class="mt-2 text-sm text-gray-500">Comienza creando tu primera campaña de email.</p>
                    <div class="mt-6">
                        <Link
                            :href="route('email-campaigns.create')"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Crear Campaña
                        </Link>
                    </div>
                </div>

                <div v-else class="divide-y divide-gray-200">
                    <div
                        v-for="campaign in campaigns.data"
                        :key="campaign.id"
                        class="p-6 hover:bg-gray-50 transition-colors duration-200"
                    >
                        <div class="flex items-start justify-between">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-3 mb-2">
                                    <Link
                                        :href="route('email-campaigns.show', campaign.id)"
                                        class="text-lg font-semibold text-gray-900 hover:text-green-600 transition-colors"
                                    >
                                        {{ campaign.name }}
                                    </Link>
                                    
                                    <span
                                        :class="[
                                            'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                            getStatusClass(campaign.status)
                                        ]"
                                    >
                                        {{ statuses[campaign.status] }}
                                    </span>
                                    
                                    <span
                                        :class="[
                                            'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                            'bg-green-100 text-green-800'
                                        ]"
                                    >
                                        {{ types[campaign.type] }}
                                    </span>
                                    
                                    <span
                                        v-if="campaign.is_ab_test"
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800"
                                    >
                                        A/B Test
                                    </span>
                                    
                                    <span
                                        v-if="campaign.is_drip_campaign"
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800"
                                    >
                                        Drip
                                    </span>
                                </div>
                                
                                <p v-if="campaign.description" class="text-sm text-gray-600 mb-2">{{ campaign.description }}</p>
                                
                                <div class="flex items-center gap-6 text-sm text-gray-500 mb-3">
                                    <span class="inline-flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                                        </svg>
                                        {{ campaign.email_template?.name }}
                                    </span>
                                    
                                    <span class="inline-flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        {{ formatNumber(campaign.estimated_recipients) }} destinatarios
                                    </span>
                                    
                                    <span class="inline-flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        {{ campaign.creator?.name }}
                                    </span>
                                    
                                    <span class="inline-flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ formatDate(campaign.created_at) }}
                                    </span>
                                </div>

                                <!-- Metrics -->
                                <div v-if="campaign.status === 'sent' && campaign.emails_sent > 0" class="grid grid-cols-4 gap-4 mt-3">
                                    <div class="text-center">
                                        <div class="text-lg font-semibold text-gray-900">{{ formatNumber(campaign.emails_sent) }}</div>
                                        <div class="text-xs text-gray-500">Enviados</div>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-lg font-semibold text-green-600">{{ campaign.open_rate }}%</div>
                                        <div class="text-xs text-gray-500">Apertura</div>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-lg font-semibold text-green-600">{{ campaign.click_rate }}%</div>
                                        <div class="text-xs text-gray-500">Clics</div>
                                    </div>
                                    <div class="text-center">
                                        <div class="text-lg font-semibold text-red-600">{{ campaign.bounce_rate }}%</div>
                                        <div class="text-xs text-gray-500">Rebotes</div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex items-center gap-2 ml-4">
                                <!-- Action buttons based on status -->
                                <template v-if="campaign.status === 'draft'">
                                    <button
                                        @click="sendCampaign(campaign)"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 text-sm text-white bg-green-600 hover:bg-green-700 rounded-lg transition-colors"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                        </svg>
                                        Enviar
                                    </button>
                                </template>

                                <template v-if="campaign.status === 'sending'">
                                    <button
                                        @click="pauseCampaign(campaign)"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 text-sm text-white bg-orange-600 hover:bg-orange-700 rounded-lg transition-colors"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Pausar
                                    </button>
                                </template>

                                <template v-if="campaign.status === 'paused'">
                                    <button
                                        @click="resumeCampaign(campaign)"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 text-sm text-white bg-green-600 hover:bg-green-700 rounded-lg transition-colors"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h.01M19 10a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Reanudar
                                    </button>
                                </template>
                                
                                <Link
                                    :href="route('email-campaigns.edit', campaign.id)"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 text-sm text-gray-600 hover:text-green-600 hover:bg-green-50 rounded-lg transition-colors"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2.828 2.828 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    Editar
                                </Link>
                                
                                <div class="relative" ref="dropdownRef">
                                    <button
                                        @click="toggleDropdown(campaign.id)"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-50 rounded-lg transition-colors"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                        </svg>
                                    </button>
                                    
                                    <div
                                        v-if="activeDropdown === campaign.id"
                                        class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-10"
                                    >
                                        <button
                                            @click="duplicateCampaign(campaign)"
                                            class="block w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-50"
                                        >
                                            Duplicar
                                        </button>
                                        
                                        <Link
                                            :href="route('email-campaigns.recipients', campaign.id)"
                                            class="block w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-50"
                                        >
                                            Ver Destinatarios
                                        </Link>
                                        
                                        <button
                                            v-if="['draft', 'scheduled', 'paused'].includes(campaign.status)"
                                            @click="deleteCampaign(campaign)"
                                            class="block w-full px-4 py-2 text-left text-sm text-red-600 hover:bg-red-50"
                                        >
                                            Eliminar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pagination -->
                <div v-if="campaigns.data.length > 0" class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                    <Pagination :links="campaigns.links" />
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { router, Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Pagination from '@/Components/Pagination.vue'
import { debounce } from 'lodash'

// Props
const props = defineProps({
    campaigns: Object,
    filters: Object,
    types: Object,
    statuses: Object,
    stats: Object
})

// State
const searchQuery = ref(props.filters.search || '')
const selectedType = ref(props.filters.type || '')
const selectedStatus = ref(props.filters.status || '')
const selectedFilter = ref('')
const activeDropdown = ref(null)

// Methods
const debouncedSearch = debounce(() => {
    applyFilters()
}, 500)

const applyFilters = () => {
    const params = {}
    
    if (searchQuery.value) params.search = searchQuery.value
    if (selectedType.value) params.type = selectedType.value
    if (selectedStatus.value) params.status = selectedStatus.value
    if (selectedFilter.value === 'ab_test') params.ab_test_only = true
    if (selectedFilter.value === 'drip') params.drip_only = true
    
    router.get(route('email-campaigns.index'), params, {
        preserveState: true,
        preserveScroll: true
    })
}

const toggleDropdown = (campaignId) => {
    activeDropdown.value = activeDropdown.value === campaignId ? null : campaignId
}

const closeDropdowns = () => {
    activeDropdown.value = null
}

const getStatusClass = (status) => {
    const classes = {
        'draft': 'bg-yellow-100 text-yellow-800',
        'scheduled': 'bg-green-100 text-green-800',
        'sending': 'bg-orange-100 text-orange-800',
        'sent': 'bg-green-100 text-green-800',
        'paused': 'bg-gray-100 text-gray-800',
        'cancelled': 'bg-red-100 text-red-800'
    }
    return classes[status] || 'bg-gray-100 text-gray-800'
}

const sendCampaign = (campaign) => {
    if (confirm('¿Estás seguro de que quieres enviar esta campaña?')) {
        router.post(route('email-campaigns.send', campaign.id))
    }
    closeDropdowns()
}

const pauseCampaign = (campaign) => {
    if (confirm('¿Estás seguro de que quieres pausar esta campaña?')) {
        router.post(route('email-campaigns.pause', campaign.id))
    }
    closeDropdowns()
}

const resumeCampaign = (campaign) => {
    if (confirm('¿Estás seguro de que quieres reanudar esta campaña?')) {
        router.post(route('email-campaigns.resume', campaign.id))
    }
    closeDropdowns()
}

const duplicateCampaign = (campaign) => {
    const newName = prompt('Nombre para la campaña duplicada:', campaign.name + ' (Copia)')
    if (newName) {
        router.post(route('email-campaigns.duplicate', campaign.id), {
            name: newName
        })
    }
    closeDropdowns()
}

const deleteCampaign = (campaign) => {
    if (confirm('¿Estás seguro de que quieres eliminar esta campaña? Esta acción no se puede deshacer.')) {
        router.delete(route('email-campaigns.destroy', campaign.id))
    }
    closeDropdowns()
}

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    })
}

const formatNumber = (number) => {
    return new Intl.NumberFormat('es-ES').format(number)
}

// Event listeners
onMounted(() => {
    document.addEventListener('click', closeDropdowns)
})

onUnmounted(() => {
    document.removeEventListener('click', closeDropdowns)
})
</script>