<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-4">
                <Link
                    :href="route('email-campaigns.show', campaign.id)"
                    class="p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </Link>
                
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Editar Campaña</h2>
                    <div class="flex items-center gap-3 mt-1">
                        <span class="text-sm text-gray-600">{{ campaign.name }}</span>
                        <span
                            :class="[
                                'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                getStatusClass(campaign.status)
                            ]"
                        >
                            {{ statuses[campaign.status] }}
                        </span>
                    </div>
                </div>
            </div>
        </template>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Status Warning -->
            <div v-if="!canEdit" class="bg-orange-50 border border-orange-200 rounded-lg p-4 mb-6">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 15.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                    <div>
                        <p class="text-sm font-medium text-orange-800">Edición limitada</p>
                        <p class="text-sm text-orange-700">Solo se pueden editar ciertos campos cuando la campaña está {{ statuses[campaign.status].toLowerCase() }}.</p>
                    </div>
                </div>
            </div>

            <form @submit.prevent="submit" class="space-y-8">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Main Content -->
                    <div class="lg:col-span-2 space-y-6">
                        <!-- Basic Information -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-6">Información básica</h3>
                            
                            <div class="space-y-4">
                                <div>
                                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                                        Nombre de la campaña *
                                    </label>
                                    <input
                                        id="name"
                                        v-model="form.name"
                                        type="text"
                                        required
                                        :disabled="!canEditBasicInfo"
                                        class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm disabled:bg-gray-50 disabled:text-gray-500"
                                        placeholder="Ej: Newsletter Enero 2025"
                                    />
                                    <div v-if="errors.name" class="mt-1 text-sm text-red-600">{{ errors.name }}</div>
                                </div>

                                <div>
                                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                                        Descripción
                                    </label>
                                    <textarea
                                        id="description"
                                        v-model="form.description"
                                        rows="3"
                                        :disabled="!canEditBasicInfo"
                                        class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm disabled:bg-gray-50 disabled:text-gray-500"
                                        placeholder="Describe el objetivo de esta campaña..."
                                    ></textarea>
                                    <div v-if="errors.description" class="mt-1 text-sm text-red-600">{{ errors.description }}</div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label for="email_template_id" class="block text-sm font-medium text-gray-700 mb-2">
                                            Template de email *
                                        </label>
                                        <select
                                            id="email_template_id"
                                            v-model="form.email_template_id"
                                            required
                                            :disabled="!canEditBasicInfo"
                                            class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm disabled:bg-gray-50 disabled:text-gray-500"
                                            @change="loadTemplatePreview"
                                        >
                                            <option value="">Selecciona un template</option>
                                            <option v-for="template in templates" :key="template.id" :value="template.id">
                                                {{ template.name }}
                                            </option>
                                        </select>
                                        <div v-if="errors.email_template_id" class="mt-1 text-sm text-red-600">{{ errors.email_template_id }}</div>
                                    </div>

                                    <div>
                                        <label for="type" class="block text-sm font-medium text-gray-700 mb-2">
                                            Tipo de campaña *
                                        </label>
                                        <select
                                            id="type"
                                            v-model="form.type"
                                            required
                                            :disabled="!canEditBasicInfo"
                                            class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm disabled:bg-gray-50 disabled:text-gray-500"
                                            @change="updateSegmentationOptions"
                                        >
                                            <option v-for="(label, value) in types" :key="value" :value="value">
                                                {{ label }}
                                            </option>
                                        </select>
                                        <div v-if="errors.type" class="mt-1 text-sm text-red-600">{{ errors.type }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Segmentation -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-6">Segmentación de destinatarios</h3>
                            
                            <div class="space-y-4">
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label for="lead_status" class="block text-sm font-medium text-gray-700 mb-2">
                                            Estado de leads
                                        </label>
                                        <select
                                            id="lead_status"
                                            v-model="form.segment_criteria.lead_status"
                                            multiple
                                            :disabled="!canEditSegmentation"
                                            class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm disabled:bg-gray-50 disabled:text-gray-500"
                                            size="3"
                                        >
                                            <option v-for="status in leadStatuses" :key="status.value" :value="status.value">
                                                {{ status.label }}
                                            </option>
                                        </select>
                                        <p class="text-xs text-gray-500 mt-1">Mantén Ctrl presionado para seleccionar múltiples opciones</p>
                                    </div>

                                    <div>
                                        <label for="lead_source" class="block text-sm font-medium text-gray-700 mb-2">
                                            Fuente de leads
                                        </label>
                                        <select
                                            id="lead_source"
                                            v-model="form.segment_criteria.lead_source"
                                            multiple
                                            :disabled="!canEditSegmentation"
                                            class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm disabled:bg-gray-50 disabled:text-gray-500"
                                            size="3"
                                        >
                                            <option v-for="source in leadSources" :key="source.value" :value="source.value">
                                                {{ source.label }}
                                            </option>
                                        </select>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label for="budget_min" class="block text-sm font-medium text-gray-700 mb-2">
                                            Presupuesto mínimo
                                        </label>
                                        <input
                                            id="budget_min"
                                            v-model="form.segment_criteria.budget_min"
                                            type="number"
                                            :disabled="!canEditSegmentation"
                                            class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm disabled:bg-gray-50 disabled:text-gray-500"
                                            placeholder="100000"
                                        />
                                    </div>

                                    <div>
                                        <label for="budget_max" class="block text-sm font-medium text-gray-700 mb-2">
                                            Presupuesto máximo
                                        </label>
                                        <input
                                            id="budget_max"
                                            v-model="form.segment_criteria.budget_max"
                                            type="number"
                                            :disabled="!canEditSegmentation"
                                            class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm disabled:bg-gray-50 disabled:text-gray-500"
                                            placeholder="500000"
                                        />
                                    </div>
                                </div>

                                <div>
                                    <label for="interests" class="block text-sm font-medium text-gray-700 mb-2">
                                        Intereses
                                    </label>
                                    <textarea
                                        id="interests"
                                        v-model="form.segment_criteria.interests"
                                        rows="2"
                                        :disabled="!canEditSegmentation"
                                        class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm disabled:bg-gray-50 disabled:text-gray-500"
                                        placeholder="Apartamento, Casa, Centro, Norte..."
                                    ></textarea>
                                    <p class="text-xs text-gray-500 mt-1">Palabras clave separadas por comas</p>
                                </div>

                                <div class="bg-blue-50 rounded-lg p-4">
                                    <div class="flex items-center gap-2 mb-2">
                                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span class="text-sm font-medium text-blue-900">Destinatarios estimados</span>
                                    </div>
                                    <p class="text-lg font-bold text-blue-900">{{ estimatedRecipients || campaign.estimated_recipients || 0 }} leads</p>
                                    <button
                                        v-if="canEditSegmentation"
                                        type="button"
                                        @click="calculateRecipients"
                                        class="mt-2 text-sm text-blue-600 hover:text-blue-700 underline"
                                    >
                                        Recalcular
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- A/B Testing -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold text-gray-900">A/B Testing</h3>
                                <label class="inline-flex items-center">
                                    <input
                                        type="checkbox"
                                        v-model="form.is_ab_test"
                                        :disabled="!canEditABTest"
                                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 disabled:opacity-50"
                                    />
                                    <span class="ml-2 text-sm text-gray-700">Habilitar A/B Testing</span>
                                </label>
                            </div>

                            <div v-if="form.is_ab_test" class="space-y-4">
                                <div>
                                    <label for="ab_test_subject_b" class="block text-sm font-medium text-gray-700 mb-2">
                                        Asunto alternativo (Versión B)
                                    </label>
                                    <input
                                        id="ab_test_subject_b"
                                        v-model="form.ab_test_subject_b"
                                        type="text"
                                        :disabled="!canEditABTest"
                                        class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm disabled:bg-gray-50 disabled:text-gray-500"
                                        placeholder="Asunto alternativo para probar..."
                                    />
                                </div>

                                <div>
                                    <label for="ab_test_percentage" class="block text-sm font-medium text-gray-700 mb-2">
                                        Porcentaje para versión A: {{ form.ab_test_percentage }}%
                                    </label>
                                    <input
                                        id="ab_test_percentage"
                                        v-model="form.ab_test_percentage"
                                        type="range"
                                        min="10"
                                        max="90"
                                        step="5"
                                        :disabled="!canEditABTest"
                                        class="block w-full disabled:opacity-50"
                                    />
                                    <div class="flex justify-between text-xs text-gray-500 mt-1">
                                        <span>Versión A: {{ form.ab_test_percentage }}%</span>
                                        <span>Versión B: {{ 100 - form.ab_test_percentage }}%</span>
                                    </div>
                                </div>
                            </div>

                            <div v-if="campaign.is_ab_test && campaign.status === 'sent'" class="mt-4 bg-gray-50 rounded-lg p-4">
                                <h4 class="font-medium text-gray-900 mb-3">Resultados A/B Testing</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <div class="bg-white rounded-lg p-3">
                                        <div class="text-sm font-medium text-gray-700 mb-1">Versión A (Original)</div>
                                        <div class="text-lg font-bold text-gray-900">{{ campaign.ab_version_a_open_rate || 0 }}%</div>
                                    </div>
                                    <div class="bg-white rounded-lg p-3">
                                        <div class="text-sm font-medium text-gray-700 mb-1">Versión B (Alternativa)</div>
                                        <div class="text-lg font-bold text-gray-900">{{ campaign.ab_version_b_open_rate || 0 }}%</div>
                                    </div>
                                </div>
                                <div v-if="campaign.ab_winner" class="mt-3 text-sm">
                                    <span class="font-medium text-green-700">Ganador: Versión {{ campaign.ab_winner.toUpperCase() }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Template Preview -->
                        <div v-if="selectedTemplate" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Vista previa del template</h3>
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Asunto</label>
                                    <div class="p-3 bg-gray-50 rounded-lg text-sm">
                                        {{ selectedTemplate.subject }}
                                    </div>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Contenido</label>
                                    <div class="border border-gray-200 rounded-lg p-4 max-h-64 overflow-y-auto">
                                        <div 
                                            v-html="stripScripts(selectedTemplate.html_content)"
                                            class="w-full h-48 border-0 overflow-auto bg-white"
                                            style="border: 1px solid #e5e7eb; border-radius: 0.5rem;"
                                        ></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="space-y-6">
                        <!-- Actions -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Acciones</h3>
                            
                            <div class="space-y-3">
                                <button
                                    type="submit"
                                    :disabled="processing || !hasChanges"
                                    class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200"
                                >
                                    <svg v-if="processing" class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                    <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    {{ processing ? 'Guardando...' : 'Guardar Cambios' }}
                                </button>
                                
                                <Link
                                    :href="route('email-campaigns.show', campaign.id)"
                                    class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors duration-200"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    Cancelar
                                </Link>
                            </div>
                        </div>

                        <!-- Schedule Options -->
                        <div v-if="canEditSchedule" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Programar envío</h3>
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="inline-flex items-center">
                                        <input
                                            type="radio"
                                            v-model="sendOption"
                                            value="draft"
                                            class="form-radio text-blue-600"
                                        />
                                        <span class="ml-2 text-sm text-gray-700">Mantener como borrador</span>
                                    </label>
                                </div>
                                
                                <div>
                                    <label class="inline-flex items-center">
                                        <input
                                            type="radio"
                                            v-model="sendOption"
                                            value="schedule"
                                            class="form-radio text-blue-600"
                                        />
                                        <span class="ml-2 text-sm text-gray-700">Programar para más tarde</span>
                                    </label>
                                </div>

                                <div v-if="sendOption === 'schedule'" class="space-y-2">
                                    <div>
                                        <label for="scheduled_at_date" class="block text-xs font-medium text-gray-700 mb-1">
                                            Fecha
                                        </label>
                                        <input
                                            id="scheduled_at_date"
                                            v-model="scheduledDate"
                                            type="date"
                                            class="block w-full px-2 py-2 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        />
                                    </div>
                                    <div>
                                        <label for="scheduled_at_time" class="block text-xs font-medium text-gray-700 mb-1">
                                            Hora
                                        </label>
                                        <input
                                            id="scheduled_at_time"
                                            v-model="scheduledTime"
                                            type="time"
                                            class="block w-full px-2 py-2 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Current Status -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Estado actual</h3>
                            
                            <div class="space-y-3">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Estado:</span>
                                    <span
                                        :class="[
                                            'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                            getStatusClass(campaign.status)
                                        ]"
                                    >
                                        {{ statuses[campaign.status] }}
                                    </span>
                                </div>

                                <div v-if="campaign.scheduled_at" class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Programada:</span>
                                    <span class="text-sm text-gray-900">{{ formatDateTime(campaign.scheduled_at) }}</span>
                                </div>

                                <div v-if="campaign.emails_sent > 0" class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Enviados:</span>
                                    <span class="text-sm text-gray-900">{{ formatNumber(campaign.emails_sent) }}</span>
                                </div>

                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600">Última actualización:</span>
                                    <span class="text-sm text-gray-900">{{ formatDateTime(campaign.updated_at) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { useForm, Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

// Props
const props = defineProps({
    campaign: Object,
    templates: Array,
    types: Object,
    statuses: Object,
    leadStatuses: Array,
    leadSources: Array,
    errors: Object
})

// Form
const form = useForm({
    name: props.campaign.name,
    description: props.campaign.description,
    email_template_id: props.campaign.email_template_id,
    type: props.campaign.type,
    segment_criteria: props.campaign.segment_criteria || {
        lead_status: [],
        lead_source: [],
        budget_min: null,
        budget_max: null,
        interests: ''
    },
    is_ab_test: props.campaign.is_ab_test,
    ab_test_subject_b: props.campaign.ab_test_subject_b,
    ab_test_percentage: props.campaign.ab_test_percentage || 50,
    scheduled_at: null,
    status: props.campaign.status
})

// State
const processing = ref(false)
const sendOption = ref(props.campaign.status === 'scheduled' ? 'schedule' : 'draft')
const scheduledDate = ref('')
const scheduledTime = ref('10:00')
const estimatedRecipients = ref(null)
const selectedTemplate = ref(null)

// Computed properties for permissions
const canEdit = computed(() => {
    return ['draft', 'scheduled', 'paused'].includes(props.campaign.status)
})

const canEditBasicInfo = computed(() => {
    return ['draft', 'scheduled'].includes(props.campaign.status)
})

const canEditSegmentation = computed(() => {
    return ['draft', 'scheduled'].includes(props.campaign.status)
})

const canEditABTest = computed(() => {
    return ['draft', 'scheduled'].includes(props.campaign.status)
})

const canEditSchedule = computed(() => {
    return ['draft', 'scheduled'].includes(props.campaign.status)
})

const formattedScheduledAt = computed(() => {
    if (sendOption.value === 'schedule' && scheduledDate.value && scheduledTime.value) {
        return `${scheduledDate.value} ${scheduledTime.value}:00`
    }
    return null
})

const hasChanges = computed(() => {
    // Simple check for changes - in a real implementation you might want a more sophisticated diff
    return JSON.stringify(form.data()) !== JSON.stringify({
        name: props.campaign.name,
        description: props.campaign.description,
        email_template_id: props.campaign.email_template_id,
        type: props.campaign.type,
        segment_criteria: props.campaign.segment_criteria || {
            lead_status: [],
            lead_source: [],
            budget_min: null,
            budget_max: null,
            interests: ''
        },
        is_ab_test: props.campaign.is_ab_test,
        ab_test_subject_b: props.campaign.ab_test_subject_b,
        ab_test_percentage: props.campaign.ab_test_percentage || 50,
        scheduled_at: null,
        status: props.campaign.status
    })
})

// Methods
const submit = () => {
    // Set status and schedule based on send option
    if (sendOption.value === 'schedule') {
        form.status = 'scheduled'
        form.scheduled_at = formattedScheduledAt.value
    } else {
        form.status = 'draft'
        form.scheduled_at = null
    }

    processing.value = true
    form.put(route('email-campaigns.update', props.campaign.id), {
        onSuccess: () => {
            processing.value = false
        },
        onError: () => {
            processing.value = false
        }
    })
}

const updateSegmentationOptions = () => {
    if (canEditSegmentation.value) {
        calculateRecipients()
    }
}

const calculateRecipients = async () => {
    try {
        const response = await fetch(route('email-campaigns.estimate-recipients'), {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({
                segment_criteria: form.segment_criteria,
                type: form.type
            })
        })
        
        const data = await response.json()
        estimatedRecipients.value = data.count
    } catch (error) {
        console.error('Error calculating recipients:', error)
    }
}

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

const loadTemplatePreview = async () => {
    if (!form.email_template_id) {
        selectedTemplate.value = null
        return
    }

    const template = props.templates.find(t => t.id == form.email_template_id)
    if (template) {
        try {
            const response = await fetch(route('email-templates.preview', template.id) + '?json=1')
            const previewData = await response.json()
            selectedTemplate.value = previewData
        } catch (error) {
            console.error('Error loading template preview:', error)
            selectedTemplate.value = template
        }
    }
}

const getStatusClass = (status) => {
    const classes = {
        'draft': 'bg-yellow-100 text-yellow-800',
        'scheduled': 'bg-blue-100 text-blue-800',
        'sending': 'bg-orange-100 text-orange-800',
        'sent': 'bg-green-100 text-green-800',
        'paused': 'bg-gray-100 text-gray-800',
        'cancelled': 'bg-red-100 text-red-800'
    }
    return classes[status] || 'bg-gray-100 text-gray-800'
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

// Initialize scheduled date/time if campaign is scheduled
if (props.campaign.scheduled_at) {
    const scheduledDate = new Date(props.campaign.scheduled_at)
    scheduledDate.value = scheduledDate.toISOString().split('T')[0]
    scheduledTime.value = scheduledDate.toTimeString().slice(0, 5)
}

// Load template preview on mount
if (form.email_template_id) {
    loadTemplatePreview()
}

// Watch for changes in segmentation criteria to auto-calculate recipients
watch(() => form.segment_criteria, () => {
    if (form.type && canEditSegmentation.value) {
        calculateRecipients()
    }
}, { deep: true })

watch(() => form.type, () => {
    if (canEditSegmentation.value) {
        calculateRecipients()
    }
})
</script>