<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
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
                        <h2 class="text-2xl font-bold text-gray-900">{{ template.name }}</h2>
                        <div class="flex items-center gap-3 mt-1">
                            <span
                                :class="[
                                    'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                    template.status === 'active' ? 'bg-green-100 text-green-800' :
                                    template.status === 'draft' ? 'bg-yellow-100 text-yellow-800' :
                                    'bg-gray-100 text-gray-800'
                                ]"
                            >
                                {{ template.formatted_status }}
                            </span>
                            
                            <span
                                v-if="template.is_system_template"
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800"
                            >
                                Template del Sistema
                            </span>
                            
                            <span class="text-sm text-gray-500">
                                {{ template.formatted_category }}
                            </span>
                        </div>
                    </div>
                </div>
                
                <div class="flex items-center gap-3">
                    <button
                        @click="showPreview = true"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-white text-gray-700 text-sm font-medium rounded-lg border border-gray-300 hover:bg-gray-50 transition-colors"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        Vista Previa
                    </button>
                    
                    <button
                        @click="duplicateTemplate"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-white text-gray-700 text-sm font-medium rounded-lg border border-gray-300 hover:bg-gray-50 transition-colors"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                        Duplicar
                    </button>
                    
                    <Link
                        :href="route('email-templates.edit', template.id)"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2.828 2.828 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Editar
                    </Link>
                    
                    <button
                        v-if="canDelete"
                        @click="deleteTemplate"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 transition-colors"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Eliminar
                    </button>
                </div>
            </div>
        </template>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Main Content -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Template Details -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-6">Detalles del template</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Nombre</label>
                                <p class="text-sm text-gray-900">{{ template.name }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Asunto</label>
                                <p class="text-sm text-gray-900">{{ template.subject }}</p>
                            </div>
                            
                            <div v-if="template.description" class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Descripción</label>
                                <p class="text-sm text-gray-600">{{ template.description }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Categoría</label>
                                <p class="text-sm text-gray-900">{{ template.formatted_category }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                                <span
                                    :class="[
                                        'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                        template.status === 'active' ? 'bg-green-100 text-green-800' :
                                        template.status === 'draft' ? 'bg-yellow-100 text-yellow-800' :
                                        'bg-gray-100 text-gray-800'
                                    ]"
                                >
                                    {{ template.formatted_status }}
                                </span>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Creado por</label>
                                <p class="text-sm text-gray-900">{{ template.creator?.name || 'Sistema' }}</p>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Fecha de creación</label>
                                <p class="text-sm text-gray-900">{{ formatDate(template.created_at) }}</p>
                            </div>
                            
                            <div v-if="template.updated_at !== template.created_at">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Actualizado por</label>
                                <p class="text-sm text-gray-900">{{ template.updater?.name || 'Sistema' }}</p>
                            </div>
                            
                            <div v-if="template.updated_at !== template.created_at">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Última actualización</label>
                                <p class="text-sm text-gray-900">{{ formatDate(template.updated_at) }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Content Preview -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <div class="flex items-center justify-between mb-6">
                            <h3 class="text-lg font-semibold text-gray-900">Contenido del template</h3>
                            <div class="flex items-center gap-2">
                                <button
                                    :class="[
                                        'px-3 py-1.5 text-sm font-medium rounded-lg transition-colors',
                                        viewMode === 'preview' 
                                            ? 'bg-green-100 text-green-700' 
                                            : 'text-gray-500 hover:text-gray-700 hover:bg-gray-100'
                                    ]"
                                    @click="viewMode = 'preview'"
                                >
                                    Vista Previa
                                </button>
                                <button
                                    :class="[
                                        'px-3 py-1.5 text-sm font-medium rounded-lg transition-colors',
                                        viewMode === 'html' 
                                            ? 'bg-green-100 text-green-700' 
                                            : 'text-gray-500 hover:text-gray-700 hover:bg-gray-100'
                                    ]"
                                    @click="viewMode = 'html'"
                                >
                                    HTML
                                </button>
                                <button
                                    v-if="template.text_content"
                                    :class="[
                                        'px-3 py-1.5 text-sm font-medium rounded-lg transition-colors',
                                        viewMode === 'text' 
                                            ? 'bg-green-100 text-green-700' 
                                            : 'text-gray-500 hover:text-gray-700 hover:bg-gray-100'
                                    ]"
                                    @click="viewMode = 'text'"
                                >
                                    Texto
                                </button>
                            </div>
                        </div>
                        
                        <div v-if="viewMode === 'preview'" class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Asunto</label>
                                <div class="p-3 bg-gray-50 rounded-lg text-sm">
                                    {{ processVariables(template.subject) }}
                                </div>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Contenido</label>
                                <div class="border border-gray-200 rounded-lg p-4">
                                    <div 
                                        v-html="stripScripts(processVariables(template.html_content))"
                                        class="w-full h-96 border-0 overflow-auto bg-white"
                                        style="border: 1px solid #e5e7eb; border-radius: 0.5rem;"
                                    ></div>
                                </div>
                            </div>
                        </div>
                        
                        <div v-else-if="viewMode === 'html'" class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Contenido HTML</label>
                                <pre class="bg-gray-50 p-4 rounded-lg text-sm overflow-x-auto"><code>{{ template.html_content }}</code></pre>
                            </div>
                        </div>
                        
                        <div v-else-if="viewMode === 'text'" class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Contenido de texto</label>
                                <pre class="bg-gray-50 p-4 rounded-lg text-sm whitespace-pre-wrap">{{ template.text_content }}</pre>
                            </div>
                        </div>
                    </div>

                    <!-- Usage Statistics -->
                    <div v-if="template.email_campaigns && template.email_campaigns.length > 0" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-6">Campañas que usan este template</h3>
                        
                        <div class="space-y-4">
                            <div
                                v-for="campaign in template.email_campaigns"
                                :key="campaign.id"
                                class="flex items-center justify-between p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors"
                            >
                                <div>
                                    <h4 class="font-medium text-gray-900">{{ campaign.name }}</h4>
                                    <p class="text-sm text-gray-500">{{ campaign.description }}</p>
                                    <div class="flex items-center gap-4 mt-2 text-sm text-gray-500">
                                        <span>{{ campaign.formatted_type }}</span>
                                        <span>{{ campaign.formatted_status }}</span>
                                        <span>{{ formatDate(campaign.created_at) }}</span>
                                    </div>
                                </div>
                                
                                <Link
                                    :href="route('email-campaigns.show', campaign.id)"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 text-sm text-green-600 hover:text-green-700 hover:bg-green-50 rounded-lg transition-colors"
                                >
                                    Ver campaña
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                    </svg>
                                </Link>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="space-y-6">
                    <!-- Variables Used -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Variables detectadas</h3>
                        
                        <div v-if="detectedVariables.length > 0" class="space-y-2">
                            <div
                                v-for="variable in detectedVariables"
                                :key="variable"
                                class="flex items-center gap-2 p-2 bg-green-50 rounded-lg"
                            >
                                <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a.997.997 0 01-1.414 0l-7-7A1.997 1.997 0 013 12V7a4 4 0 014-4z" />
                                </svg>
                                <span class="text-sm font-mono text-green-700">{{ getVariableDisplay(variable) }}</span>
                            </div>
                        </div>
                        
                        <div v-else class="text-sm text-gray-500 text-center py-4">
                            No se detectaron variables en este template
                        </div>
                    </div>

                    <!-- Quick Actions -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Acciones rápidas</h3>
                        
                        <div class="space-y-3">
                            <Link
                                :href="route('email-campaigns.create', { template_id: template.id })"
                                class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                                </svg>
                                Crear Campaña
                            </Link>
                            
                            <button
                                @click="testTemplate"
                                class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Enviar Prueba
                            </button>
                        </div>
                    </div>

                    <!-- Template Info -->
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Información adicional</h3>
                        
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600">Tamaño HTML:</span>
                                <span class="text-gray-900">{{ formatBytes(template.html_content?.length || 0) }}</span>
                            </div>
                            
                            <div v-if="template.text_content" class="flex justify-between">
                                <span class="text-gray-600">Tamaño texto:</span>
                                <span class="text-gray-900">{{ formatBytes(template.text_content?.length || 0) }}</span>
                            </div>
                            
                            <div class="flex justify-between">
                                <span class="text-gray-600">Variables:</span>
                                <span class="text-gray-900">{{ detectedVariables.length }}</span>
                            </div>
                            
                            <div class="flex justify-between">
                                <span class="text-gray-600">Campañas:</span>
                                <span class="text-gray-900">{{ template.email_campaigns?.length || 0 }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Preview Modal -->
        <Modal :show="showPreview" @close="showPreview = false">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Vista previa completa</h3>
                    <button
                        @click="showPreview = false"
                        class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                
                <div class="max-w-4xl">
                    <iframe
                        :src="previewUrl"
                        class="w-full h-96 border border-gray-200 rounded-lg"
                    ></iframe>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { router, Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Modal from '@/Components/Modal.vue'

// Props
const props = defineProps({
    template: Object,
    canDelete: Boolean,
    previewUrl: String
})

// State
const viewMode = ref('preview')
const showPreview = ref(false)

// Computed
const detectedVariables = computed(() => {
    const content = props.template.subject + ' ' + props.template.html_content + ' ' + (props.template.text_content || '')
    const matches = content.match(/{{(\w+)}}/g) || []
    return [...new Set(matches.map(match => match.replace(/[{}]/g, '')))]
})

// Methods
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

const formatDate = (date) => {
    return new Date(date).toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    })
}

const formatBytes = (bytes) => {
    if (bytes === 0) return '0 Bytes'
    const k = 1024
    const sizes = ['Bytes', 'KB', 'MB']
    const i = Math.floor(Math.log(bytes) / Math.log(k))
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}

const duplicateTemplate = () => {
    const newName = prompt('Nombre para el template duplicado:', props.template.name + ' (Copia)')
    if (newName) {
        router.post(route('email-templates.duplicate', props.template.id), {
            name: newName
        })
    }
}

const deleteTemplate = () => {
    if (confirm('¿Estás seguro de que quieres eliminar este template? Esta acción no se puede deshacer.')) {
        router.delete(route('email-templates.destroy', props.template.id))
    }
}

const testTemplate = () => {
    // Open test modal or redirect to test page
    router.get(route('email-templates.test', props.template.id))
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
</script>