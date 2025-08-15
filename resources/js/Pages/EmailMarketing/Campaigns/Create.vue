<template>
    <AuthenticatedLayout>
        <template #header>
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
                    <h2 class="text-2xl font-bold text-gray-900">Crear Campaña de Email</h2>
                    <p class="text-sm text-gray-600 mt-1">
                        Configura y envía una nueva campaña de email marketing
                    </p>
                </div>
            </div>
        </template>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
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
                                        class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm"
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
                                        class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm"
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
                                            class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm"
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
                                            class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm"
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
                                            class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm"
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
                                            class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm"
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
                                            class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm"
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
                                            class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm"
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
                                        class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm"
                                        placeholder="Apartamento, Casa, Centro, Norte..."
                                    ></textarea>
                                    <p class="text-xs text-gray-500 mt-1">Palabras clave separadas por comas</p>
                                </div>

                                <div class="bg-green-50 rounded-lg p-4">
                                    <div class="flex items-center gap-2 mb-2">
                                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span class="text-sm font-medium text-green-900">Destinatarios estimados</span>
                                    </div>
                                    <p class="text-lg font-bold text-green-900">{{ estimatedRecipients || 0 }} leads</p>
                                    <button
                                        type="button"
                                        @click="calculateRecipients"
                                        class="mt-2 text-sm text-green-600 hover:text-green-700 underline"
                                    >
                                        Recalcular
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- A/B Testing (optional) -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold text-gray-900">A/B Testing</h3>
                                <label class="inline-flex items-center">
                                    <input
                                        type="checkbox"
                                        v-model="form.is_ab_test"
                                        class="rounded border-gray-300 text-green-600 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200"
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
                                        class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm"
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
                                        class="block w-full"
                                    />
                                    <div class="flex justify-between text-xs text-gray-500 mt-1">
                                        <span>Versión A: {{ form.ab_test_percentage }}%</span>
                                        <span>Versión B: {{ 100 - form.ab_test_percentage }}%</span>
                                    </div>
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
                                    :disabled="processing"
                                    class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200"
                                >
                                    <svg v-if="processing" class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                    <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                    {{ processing ? 'Creando...' : 'Crear Campaña' }}
                                </button>
                                
                                <Link
                                    :href="route('email-campaigns.index')"
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
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Programar envío</h3>
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="inline-flex items-center">
                                        <input
                                            type="radio"
                                            v-model="sendOption"
                                            value="now"
                                            class="form-radio text-green-600"
                                        />
                                        <span class="ml-2 text-sm text-gray-700">Enviar ahora</span>
                                    </label>
                                </div>
                                
                                <div>
                                    <label class="inline-flex items-center">
                                        <input
                                            type="radio"
                                            v-model="sendOption"
                                            value="draft"
                                            class="form-radio text-green-600"
                                        />
                                        <span class="ml-2 text-sm text-gray-700">Guardar como borrador</span>
                                    </label>
                                </div>
                                
                                <div>
                                    <label class="inline-flex items-center">
                                        <input
                                            type="radio"
                                            v-model="sendOption"
                                            value="schedule"
                                            class="form-radio text-green-600"
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
                                            class="block w-full px-2 py-2 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500"
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
                                            class="block w-full px-2 py-2 border border-gray-300 rounded text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Tips -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Consejos</h3>
                            
                            <div class="space-y-3 text-sm text-gray-600">
                                <div class="flex items-start gap-2">
                                    <svg class="w-4 h-4 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>Segmenta bien a tus destinatarios para mejor engagement</span>
                                </div>
                                
                                <div class="flex items-start gap-2">
                                    <svg class="w-4 h-4 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>Usa A/B testing para optimizar tus asuntos</span>
                                </div>
                                
                                <div class="flex items-start gap-2">
                                    <svg class="w-4 h-4 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>Los mejores horarios suelen ser martes a jueves, 10-11 AM</span>
                                </div>
                                
                                <div class="flex items-start gap-2">
                                    <svg class="w-4 h-4 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>Revisa la vista previa antes de enviar</span>
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
    templates: Array,
    types: Object,
    leadStatuses: Array,
    leadSources: Array,
    errors: Object
})

// Form
const form = useForm({
    name: '',
    description: '',
    email_template_id: '',
    type: 'newsletter',
    segment_criteria: {
        lead_status: [],
        lead_source: [],
        budget_min: null,
        budget_max: null,
        interests: ''
    },
    is_ab_test: false,
    ab_test_subject_b: '',
    ab_test_percentage: 50,
    scheduled_at: null,
    status: 'draft'
})

// State
const processing = ref(false)
const sendOption = ref('draft')
const scheduledDate = ref('')
const scheduledTime = ref('10:00')
const estimatedRecipients = ref(null)
const selectedTemplate = ref(null)

// Computed
const formattedScheduledAt = computed(() => {
    if (sendOption.value === 'schedule' && scheduledDate.value && scheduledTime.value) {
        return `${scheduledDate.value} ${scheduledTime.value}:00`
    }
    return null
})

// Methods
const submit = () => {
    // Set status based on send option
    if (sendOption.value === 'now') {
        form.status = 'sending'
    } else if (sendOption.value === 'schedule') {
        form.status = 'scheduled'
        form.scheduled_at = formattedScheduledAt.value
    } else {
        form.status = 'draft'
    }

    processing.value = true
    form.post(route('email-campaigns.store'), {
        onSuccess: () => {
            processing.value = false
        },
        onError: () => {
            processing.value = false
        }
    })
}

const updateSegmentationOptions = () => {
    // This method can be used to update available segmentation options based on campaign type
    // For now, it's just a placeholder for future enhancements
}

const calculateRecipients = async () => {
    try {
        const response = await fetch(route('email-campaigns.calculate-recipients'), {
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

// Watch for changes in segmentation criteria to auto-calculate recipients
watch(() => form.segment_criteria, () => {
    if (form.type) {
        calculateRecipients()
    }
}, { deep: true })

watch(() => form.type, () => {
    calculateRecipients()
})

// Set default scheduled date to tomorrow
const tomorrow = new Date()
tomorrow.setDate(tomorrow.getDate() + 1)
scheduledDate.value = tomorrow.toISOString().split('T')[0]

// Initial recipients calculation
calculateRecipients()
</script>