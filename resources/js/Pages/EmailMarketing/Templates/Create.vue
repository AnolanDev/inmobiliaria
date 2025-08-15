<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-4">
                <Link
                    :href="route('email-templates.index')"
                    class="p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-100 rounded-lg transition-colors"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                </Link>
                
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Crear Email Template</h2>
                    <p class="text-sm text-gray-600 mt-1">
                        Crea un nuevo template para tus campañas de email
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
                                        Nombre del template *
                                    </label>
                                    <input
                                        id="name"
                                        v-model="form.name"
                                        type="text"
                                        required
                                        class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm"
                                        placeholder="Ej: Bienvenida nuevos leads"
                                    />
                                    <div v-if="errors.name" class="mt-1 text-sm text-red-600">{{ errors.name }}</div>
                                </div>

                                <div>
                                    <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">
                                        Asunto del email *
                                    </label>
                                    <input
                                        id="subject"
                                        v-model="form.subject"
                                        type="text"
                                        required
                                        class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm"
                                        placeholder="Ej: ¡Bienvenido {{recipient_name}}!"
                                    />
                                    <div v-if="errors.subject" class="mt-1 text-sm text-red-600">{{ errors.subject }}</div>
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
                                        placeholder="Describe el propósito de este template..."
                                    ></textarea>
                                    <div v-if="errors.description" class="mt-1 text-sm text-red-600">{{ errors.description }}</div>
                                </div>

                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                    <div>
                                        <label for="category" class="block text-sm font-medium text-gray-700 mb-2">
                                            Categoría *
                                        </label>
                                        <select
                                            id="category"
                                            v-model="form.category"
                                            required
                                            class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm"
                                            @change="updateAvailableVariables"
                                        >
                                            <option value="">Selecciona una categoría</option>
                                            <option v-for="(label, value) in categories" :key="value" :value="value">
                                                {{ label }}
                                            </option>
                                        </select>
                                        <div v-if="errors.category" class="mt-1 text-sm text-red-600">{{ errors.category }}</div>
                                    </div>

                                    <div>
                                        <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                            Estado *
                                        </label>
                                        <select
                                            id="status"
                                            v-model="form.status"
                                            required
                                            class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm"
                                        >
                                            <option v-for="(label, value) in statuses" :key="value" :value="value">
                                                {{ label }}
                                            </option>
                                        </select>
                                        <div v-if="errors.status" class="mt-1 text-sm text-red-600">{{ errors.status }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Email Content -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-6">Contenido del email</h3>
                            
                            <div class="space-y-4">
                                <div>
                                    <label for="html_content" class="block text-sm font-medium text-gray-700 mb-2">
                                        Contenido HTML *
                                    </label>
                                    <textarea
                                        id="html_content"
                                        v-model="form.html_content"
                                        rows="15"
                                        required
                                        class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm font-mono"
                                        placeholder="Ingresa el contenido HTML del email..."
                                    ></textarea>
                                    <div v-if="errors.html_content" class="mt-1 text-sm text-red-600">{{ errors.html_content }}</div>
                                </div>

                                <div>
                                    <label for="text_content" class="block text-sm font-medium text-gray-700 mb-2">
                                        Contenido de texto (opcional)
                                    </label>
                                    <textarea
                                        id="text_content"
                                        v-model="form.text_content"
                                        rows="8"
                                        class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 text-sm font-mono"
                                        placeholder="Versión en texto plano del email (recomendado para mejor compatibilidad)..."
                                    ></textarea>
                                    <div v-if="errors.text_content" class="mt-1 text-sm text-red-600">{{ errors.text_content }}</div>
                                </div>
                            </div>
                        </div>

                        <!-- Preview -->
                        <div v-if="form.html_content" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                            <div class="flex items-center justify-between mb-4">
                                <h3 class="text-lg font-semibold text-gray-900">Vista previa</h3>
                                <button
                                    type="button"
                                    @click="refreshPreview"
                                    class="inline-flex items-center gap-2 px-3 py-1.5 text-sm text-gray-600 hover:text-green-600 hover:bg-green-50 rounded-lg transition-colors"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                    Actualizar
                                </button>
                            </div>
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Asunto</label>
                                    <div class="p-3 bg-gray-50 rounded-lg text-sm">
                                        {{ processVariables(form.subject) }}
                                    </div>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-1">Contenido</label>
                                    <div class="border border-gray-200 rounded-lg p-4">
                                        <div 
                                            v-html="stripScripts(processVariables(form.html_content))"
                                            class="w-full h-64 border-0 overflow-auto bg-white"
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
                                    {{ processing ? 'Guardando...' : 'Crear Template' }}
                                </button>
                                
                                <Link
                                    :href="route('email-templates.index')"
                                    class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-gray-100 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-200 transition-colors duration-200"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                    Cancelar
                                </Link>
                            </div>
                        </div>

                        <!-- Variables Available -->
                        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Variables disponibles</h3>
                            
                            <div class="space-y-3">
                                <div v-for="(description, variable) in availableVariables" :key="variable">
                                    <button
                                        type="button"
                                        @click="insertVariable(variable)"
                                        class="w-full text-left p-2 rounded-lg hover:bg-gray-50 transition-colors group"
                                    >
                                        <div class="text-sm font-mono text-green-600 group-hover:text-green-700">
                                            {{ getVariableDisplay(variable) }}
                                        </div>
                                        <div class="text-xs text-gray-500 mt-1">
                                            {{ description }}
                                        </div>
                                    </button>
                                </div>
                            </div>
                            
                            <div class="mt-4 p-3 bg-green-50 rounded-lg">
                                <p class="text-xs text-green-700">
                                    Haz clic en cualquier variable para insertarla en el cursor del contenido HTML.
                                </p>
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
                                    <span>Usa variables para personalizar el contenido automáticamente</span>
                                </div>
                                
                                <div class="flex items-start gap-2">
                                    <svg class="w-4 h-4 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>Incluye siempre {{ getVariableDisplay('unsubscribe_url') }} para cumplir con las regulaciones</span>
                                </div>
                                
                                <div class="flex items-start gap-2">
                                    <svg class="w-4 h-4 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>La versión en texto es importante para clientes que no soportan HTML</span>
                                </div>
                                
                                <div class="flex items-start gap-2">
                                    <svg class="w-4 h-4 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span>Usa la vista previa para verificar el contenido antes de guardar</span>
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
    categories: Object,
    statuses: Object,
    defaultVariables: Object,
    leadVariables: Object,
    errors: Object
})

// Form
const form = useForm({
    name: '',
    subject: '',
    description: '',
    html_content: '',
    text_content: '',
    category: '',
    status: 'draft',
    variables: [],
    metadata: {}
})

// State
const processing = ref(false)

// Computed
const availableVariables = computed(() => {
    let variables = { ...props.defaultVariables }
    
    // Add lead variables for specific categories
    if (['welcome', 'follow_up'].includes(form.category)) {
        variables = { ...variables, ...props.leadVariables }
    }
    
    return variables
})

// Methods
const submit = () => {
    processing.value = true
    form.post(route('email-templates.store'), {
        onSuccess: () => {
            processing.value = false
        },
        onError: () => {
            processing.value = false
        }
    })
}

const updateAvailableVariables = () => {
    // This will trigger the computed property to update
}

const insertVariable = (variable) => {
    const variableText = `{{${variable}}}`
    const textarea = document.getElementById('html_content')
    
    if (textarea) {
        const start = textarea.selectionStart
        const end = textarea.selectionEnd
        const text = textarea.value
        
        const before = text.substring(0, start)
        const after = text.substring(end, text.length)
        
        form.html_content = before + variableText + after
        
        // Set cursor position after inserted variable
        setTimeout(() => {
            textarea.selectionStart = textarea.selectionEnd = start + variableText.length
            textarea.focus()
        }, 0)
    }
}

const processVariables = (content) => {
    if (!content) return ''
    
    // Sample data for preview
    const sampleData = {
        recipient_name: 'María García',
        recipient_email: 'maria@example.com',
        company_name: 'InmoApp',
        current_date: new Date().toLocaleDateString('es-ES'),
        lead_first_name: 'María',
        lead_last_name: 'García',
        lead_full_name: 'María García',
        lead_status: 'Nuevo',
        lead_source: 'Sitio Web',
        lead_budget_min: '$200,000',
        lead_budget_max: '$300,000',
        lead_interests: 'Apartamento, Centro',
        assigned_agent_name: 'Juan Pérez',
        unsubscribe_url: '#unsubscribe'
    }
    
    let processedContent = content
    
    // Replace variables with sample data
    Object.keys(sampleData).forEach(key => {
        const regex = new RegExp(`{{${key}}}`, 'g')
        processedContent = processedContent.replace(regex, sampleData[key])
    })
    
    return processedContent
}

const refreshPreview = () => {
    // Force reactivity update for preview
}

const getVariableDisplay = (variable) => {
    return `{{${variable}}}`
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

// Watch for category changes to update available variables
watch(() => form.category, () => {
    updateAvailableVariables()
})
</script>