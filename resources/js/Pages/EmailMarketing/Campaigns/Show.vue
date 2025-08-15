<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <Link
                        :href="route('email-campaigns.index')"
                        class="p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                    </Link>
                    
                    <div>
                        <h2 class="text-2xl font-bold text-gray-900">{{ campaign.name }}</h2>
                        <div class="flex items-center gap-3 mt-1">
                            <span
                                :class="[
                                    'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                    getStatusClass(campaign.status)
                                ]"
                            >
                                {{ statuses[campaign.status] }}
                            </span>
                            <span class="text-sm text-gray-600">
                                {{ types[campaign.type] }}
                            </span>
                            <span v-if="campaign.is_ab_test" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                A/B Test
                            </span>
                        </div>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <!-- Action buttons based on status -->
                    <template v-if="campaign.status === 'draft'">
                        <button
                            @click="sendCampaign"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                            </svg>
                            Enviar Campaña
                        </button>
                    </template>

                    <template v-if="campaign.status === 'sending'">
                        <button
                            @click="pauseCampaign"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-orange-600 text-white text-sm font-medium rounded-lg hover:bg-orange-700 transition-colors"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Pausar
                        </button>
                    </template>

                    <template v-if="campaign.status === 'paused'">
                        <button
                            @click="resumeCampaign"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h.01M19 10a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            Reanudar
                        </button>
                    </template>

                    <Link
                        :href="route('email-campaigns.edit', campaign.id)"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2.828 2.828 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Editar
                    </Link>
                </div>
            </div>
        </template>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Campaign Overview -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Información general</h3>
                        
                        <div class="space-y-4">
                            <div v-if="campaign.description">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Descripción</label>
                                <p class="text-sm text-gray-900">{{ campaign.description }}</p>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Template</label>
                                    <Link 
                                        :href="route('email-templates.show', campaign.email_template.id)"
                                        class="text-sm text-green-600 hover:text-green-700 underline"
                                    >
                                        {{ campaign.email_template.name }}
                                    </Link>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Creador</label>
                                    <p class="text-sm text-gray-900">{{ campaign.creator.name }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Fecha de creación</label>
                                    <p class="text-sm text-gray-900">{{ formatDate(campaign.created_at) }}</p>
                                </div>

                                <div v-if="campaign.scheduled_at">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Programada para</label>
                                    <p class="text-sm text-gray-900">{{ formatDateTime(campaign.scheduled_at) }}</p>
                                </div>

                                <div v-if="campaign.sent_at">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Enviada</label>
                                    <p class="text-sm text-gray-900">{{ formatDateTime(campaign.sent_at) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Metrics -->
                    <div v-if="campaign.status === 'sent' || campaign.emails_sent > 0" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-6">Métricas de rendimiento</h3>
                        
                        <!-- Overview Stats -->
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                            <div class="text-center">
                                <div class="text-3xl font-bold text-gray-900">{{ formatNumber(campaign.emails_sent || 0) }}</div>
                                <div class="text-sm text-gray-500">Enviados</div>
                            </div>
                            <div class="text-center">
                                <div class="text-3xl font-bold text-green-600">{{ campaign.open_rate || 0 }}%</div>
                                <div class="text-sm text-gray-500">Apertura</div>
                                <div class="text-xs text-gray-400 mt-1">{{ formatNumber(campaign.opens_count || 0) }} aperturas</div>
                            </div>
                            <div class="text-center">
                                <div class="text-3xl font-bold text-green-600">{{ campaign.click_rate || 0 }}%</div>
                                <div class="text-sm text-gray-500">Clics</div>
                                <div class="text-xs text-gray-400 mt-1">{{ formatNumber(campaign.clicks_count || 0) }} clics</div>
                            </div>
                            <div class="text-center">
                                <div class="text-3xl font-bold text-red-600">{{ campaign.bounce_rate || 0 }}%</div>
                                <div class="text-sm text-gray-500">Rebotes</div>
                                <div class="text-xs text-gray-400 mt-1">{{ formatNumber(campaign.bounces_count || 0) }} rebotes</div>
                            </div>
                        </div>

                        <!-- Progress Bar -->
                        <div v-if="campaign.status === 'sending'" class="mb-6">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm font-medium text-gray-700">Progreso del envío</span>
                                <span class="text-sm text-gray-500">{{ Math.round((campaign.emails_sent / campaign.estimated_recipients) * 100) }}%</span>
                            </div>
                            <div class="w-full bg-gray-200 rounded-full h-2">
                                <div 
                                    class="bg-green-600 h-2 rounded-full transition-all duration-300" 
                                    :style="{ width: Math.round((campaign.emails_sent / campaign.estimated_recipients) * 100) + '%' }"
                                ></div>
                            </div>
                        </div>

                        <!-- A/B Test Results -->
                        <div v-if="campaign.is_ab_test && campaign.status === 'sent'" class="bg-gray-50 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900 mb-3">Resultados A/B Testing</h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="bg-white rounded-lg p-4">
                                    <div class="text-sm font-medium text-gray-700 mb-2">Versión A (Original)</div>
                                    <div class="text-lg font-bold text-gray-900">{{ campaign.ab_version_a_open_rate || 0 }}%</div>
                                    <div class="text-xs text-gray-500">{{ campaign.ab_test_percentage }}% del envío</div>
                                </div>
                                <div class="bg-white rounded-lg p-4">
                                    <div class="text-sm font-medium text-gray-700 mb-2">Versión B ({{ campaign.ab_test_subject_b }})</div>
                                    <div class="text-lg font-bold text-gray-900">{{ campaign.ab_version_b_open_rate || 0 }}%</div>
                                    <div class="text-xs text-gray-500">{{ 100 - campaign.ab_test_percentage }}% del envío</div>
                                </div>
                            </div>
                            <div v-if="campaign.ab_winner" class="mt-3 text-sm">
                                <span class="font-medium text-green-700">Ganador: Versión {{ campaign.ab_winner.toUpperCase() }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Segmentation Details -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Segmentación aplicada</h3>
                        
                        <div class="space-y-4">
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div v-if="campaign.segment_criteria.lead_status && campaign.segment_criteria.lead_status.length > 0">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Estados de leads</label>
                                    <div class="flex flex-wrap gap-1">
                                        <span 
                                            v-for="status in campaign.segment_criteria.lead_status" 
                                            :key="status"
                                            class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-800"
                                        >
                                            {{ getLeadStatusLabel(status) }}
                                        </span>
                                    </div>
                                </div>

                                <div v-if="campaign.segment_criteria.lead_source && campaign.segment_criteria.lead_source.length > 0">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Fuentes de leads</label>
                                    <div class="flex flex-wrap gap-1">
                                        <span 
                                            v-for="source in campaign.segment_criteria.lead_source" 
                                            :key="source"
                                            class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-800"
                                        >
                                            {{ getLeadSourceLabel(source) }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div v-if="campaign.segment_criteria.budget_min">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Presupuesto mínimo</label>
                                    <p class="text-sm text-gray-900">{{ formatCurrency(campaign.segment_criteria.budget_min) }}</p>
                                </div>

                                <div v-if="campaign.segment_criteria.budget_max">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Presupuesto máximo</label>
                                    <p class="text-sm text-gray-900">{{ formatCurrency(campaign.segment_criteria.budget_max) }}</p>
                                </div>
                            </div>

                            <div v-if="campaign.segment_criteria.interests">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Intereses</label>
                                <p class="text-sm text-gray-900">{{ campaign.segment_criteria.interests }}</p>
                            </div>

                            <div class="bg-green-50 rounded-lg p-4">
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                    <span class="text-sm font-medium text-green-900">
                                        {{ formatNumber(campaign.estimated_recipients) }} destinatarios estimados
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Email Preview -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Vista previa del email</h3>
                        
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Asunto</label>
                                <div class="p-3 bg-gray-50 rounded-lg text-sm">
                                    {{ campaign.email_template.subject }}
                                </div>
                                <div v-if="campaign.is_ab_test && campaign.ab_test_subject_b" class="mt-2">
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Asunto alternativo (Versión B)</label>
                                    <div class="p-3 bg-yellow-50 rounded-lg text-sm">
                                        {{ campaign.ab_test_subject_b }}
                                    </div>
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Contenido</label>
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <div 
                                        v-html="stripScripts(campaign.email_template.html_content)"
                                        class="w-full h-96 border-0 overflow-auto bg-white"
                                        style="border: 1px solid #e5e7eb; border-radius: 0.5rem;"
                                    ></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Quick Actions -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Acciones rápidas</h3>
                        
                        <div class="space-y-3">
                            <Link
                                :href="route('email-campaigns.recipients', campaign.id)"
                                class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 bg-green-50 text-green-700 text-sm font-medium rounded-lg hover:bg-green-100 transition-colors"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                Ver Destinatarios
                            </Link>

                            <button
                                @click="duplicateCampaign"
                                class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 bg-gray-50 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-100 transition-colors"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                </svg>
                                Duplicar Campaña
                            </button>

                            <button
                                v-if="['draft', 'scheduled', 'paused'].includes(campaign.status)"
                                @click="deleteCampaign"
                                class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 bg-red-50 text-red-700 text-sm font-medium rounded-lg hover:bg-red-100 transition-colors"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                                Eliminar
                            </button>
                        </div>
                    </div>

                    <!-- Recent Activity -->
                    <div v-if="recentActivity && recentActivity.length > 0" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Actividad reciente</h3>
                        
                        <div class="space-y-3">
                            <div
                                v-for="activity in recentActivity.slice(0, 5)"
                                :key="activity.id"
                                class="flex items-start gap-3 text-sm"
                            >
                                <div class="w-2 h-2 bg-green-600 rounded-full mt-2 flex-shrink-0"></div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-gray-900">{{ activity.description }}</p>
                                    <p class="text-gray-500 text-xs">{{ formatDateTime(activity.created_at) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Campaign Timeline -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Timeline</h3>
                        
                        <div class="space-y-4">
                            <div class="flex items-start gap-3">
                                <div class="w-3 h-3 bg-green-600 rounded-full mt-1"></div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Campaña creada</p>
                                    <p class="text-xs text-gray-500">{{ formatDateTime(campaign.created_at) }}</p>
                                </div>
                            </div>

                            <div v-if="campaign.scheduled_at" class="flex items-start gap-3">
                                <div class="w-3 h-3 bg-yellow-600 rounded-full mt-1"></div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Programada</p>
                                    <p class="text-xs text-gray-500">{{ formatDateTime(campaign.scheduled_at) }}</p>
                                </div>
                            </div>

                            <div v-if="campaign.sent_at" class="flex items-start gap-3">
                                <div class="w-3 h-3 bg-green-600 rounded-full mt-1"></div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Enviada</p>
                                    <p class="text-xs text-gray-500">{{ formatDateTime(campaign.sent_at) }}</p>
                                </div>
                            </div>

                            <div v-if="campaign.completed_at" class="flex items-start gap-3">
                                <div class="w-3 h-3 bg-gray-600 rounded-full mt-1"></div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">Completada</p>
                                    <p class="text-xs text-gray-500">{{ formatDateTime(campaign.completed_at) }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue'
import { router, Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

// Props
const props = defineProps({
    campaign: Object,
    statuses: Object,
    types: Object,
    leadStatuses: Array,
    leadSources: Array,
    recentActivity: Array
})

// Methods
const stripScripts = (html) => {
    if (!html) return ''
    
    // Create a temporary div to parse HTML safely
    const tempDiv = document.createElement('div')
    tempDiv.innerHTML = html
    
    // Remove all script tags
    const scripts = tempDiv.querySelectorAll('script')
    scripts.forEach(script => script.remove())
    
    // Remove all event handlers
    const allElements = tempDiv.querySelectorAll('*')
    allElements.forEach(element => {
        // Remove all attributes that start with 'on'
        Array.from(element.attributes).forEach(attr => {
            if (attr.name.toLowerCase().startsWith('on')) {
                element.removeAttribute(attr.name)
            }
        })
        
        // Remove javascript: URLs from href and src attributes
        if (element.getAttribute('href')) {
            const href = element.getAttribute('href')
            if (href.toLowerCase().includes('javascript:')) {
                element.removeAttribute('href')
            }
        }
        if (element.getAttribute('src')) {
            const src = element.getAttribute('src')
            if (src.toLowerCase().includes('javascript:')) {
                element.removeAttribute('src')
            }
        }
    })
    
    // Remove style tags that might contain CSS with expressions
    const styles = tempDiv.querySelectorAll('style')
    styles.forEach(style => {
        const content = style.textContent || ''
        if (content.includes('expression(') || content.includes('javascript:')) {
            style.remove()
        }
    })
    
    return tempDiv.innerHTML
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

const sendCampaign = () => {
    if (confirm('¿Estás seguro de que quieres enviar esta campaña?')) {
        router.post(route('email-campaigns.send', props.campaign.id))
    }
}

const pauseCampaign = () => {
    if (confirm('¿Estás seguro de que quieres pausar esta campaña?')) {
        router.post(route('email-campaigns.pause', props.campaign.id))
    }
}

const resumeCampaign = () => {
    if (confirm('¿Estás seguro de que quieres reanudar esta campaña?')) {
        router.post(route('email-campaigns.resume', props.campaign.id))
    }
}

const duplicateCampaign = () => {
    const newName = prompt('Nombre para la campaña duplicada:', props.campaign.name + ' (Copia)')
    if (newName) {
        router.post(route('email-campaigns.duplicate', props.campaign.id), {
            name: newName
        })
    }
}

const deleteCampaign = () => {
    if (confirm('¿Estás seguro de que quieres eliminar esta campaña? Esta acción no se puede deshacer.')) {
        router.delete(route('email-campaigns.destroy', props.campaign.id))
    }
}

const getLeadStatusLabel = (value) => {
    const status = props.leadStatuses.find(s => s.value === value)
    return status ? status.label : value
}

const getLeadSourceLabel = (value) => {
    const source = props.leadSources.find(s => s.value === value)
    return source ? source.label : value
}

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'long',
        day: 'numeric'
    })
}

const formatDateTime = (date) => {
    return new Date(date).toLocaleString('es-ES', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    })
}

const formatNumber = (number) => {
    return new Intl.NumberFormat('es-ES').format(number)
}

const formatCurrency = (amount) => {
    return new Intl.NumberFormat('es-ES', {
        style: 'currency',
        currency: 'EUR'
    }).format(amount)
}
</script>