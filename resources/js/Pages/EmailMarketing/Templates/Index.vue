<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">Email Templates</h2>
                    <p class="text-sm text-gray-600 mt-1">
                        Gestiona los templates de email para tus campañas
                    </p>
                </div>
                
                <div class="flex items-center gap-3">
                    <Link
                        :href="route('email-templates.create')"
                        class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Nuevo Template
                    </Link>
                </div>
            </div>
        </template>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Total Templates</p>
                            <p class="text-2xl font-bold text-gray-900">{{ stats.total }}</p>
                        </div>
                        <div class="p-3 bg-blue-50 rounded-lg">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm p-6 border border-gray-100">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600">Activos</p>
                            <p class="text-2xl font-bold text-green-600">{{ stats.active }}</p>
                        </div>
                        <div class="p-3 bg-green-50 rounded-lg">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
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
                            <p class="text-sm font-medium text-gray-600">Sistema</p>
                            <p class="text-2xl font-bold text-purple-600">{{ stats.system }}</p>
                        </div>
                        <div class="p-3 bg-purple-50 rounded-lg">
                            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
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
                                placeholder="Buscar por nombre, asunto o descripción..."
                                class="block w-full pl-10 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                                @input="debouncedSearch"
                            />
                        </div>
                    </div>

                    <div class="w-full sm:w-48">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Categoría</label>
                        <select
                            v-model="selectedCategory"
                            class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                            @change="applyFilters"
                        >
                            <option value="">Todas las categorías</option>
                            <option v-for="(label, value) in categories" :key="value" :value="value">
                                {{ label }}
                            </option>
                        </select>
                    </div>

                    <div class="w-full sm:w-48">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                        <select
                            v-model="selectedStatus"
                            class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                            @change="applyFilters"
                        >
                            <option value="">Todos los estados</option>
                            <option v-for="(label, value) in statuses" :key="value" :value="value">
                                {{ label }}
                            </option>
                        </select>
                    </div>

                    <div class="w-full sm:w-48">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Tipo</label>
                        <select
                            v-model="selectedType"
                            class="block w-full px-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-sm"
                            @change="applyFilters"
                        >
                            <option value="">Todos los tipos</option>
                            <option value="system">Solo sistema</option>
                            <option value="user">Solo usuario</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Templates List -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="text-lg font-semibold text-gray-900">Templates</h3>
                </div>

                <div v-if="templates.data.length === 0" class="p-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                    </svg>
                    <h3 class="mt-4 text-lg font-medium text-gray-900">No hay templates</h3>
                    <p class="mt-2 text-sm text-gray-500">Comienza creando tu primer template de email.</p>
                    <div class="mt-6">
                        <Link
                            :href="route('email-templates.create')"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Crear Template
                        </Link>
                    </div>
                </div>

                <div v-else class="divide-y divide-gray-200">
                    <div
                        v-for="template in templates.data"
                        :key="template.id"
                        class="p-6 hover:bg-gray-50 transition-colors duration-200"
                    >
                        <div class="flex items-start justify-between">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-3 mb-2">
                                    <Link
                                        :href="route('email-templates.show', template.id)"
                                        class="text-lg font-semibold text-gray-900 hover:text-blue-600 transition-colors"
                                    >
                                        {{ template.name }}
                                    </Link>
                                    
                                    <span
                                        :class="[
                                            'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                                            template.status === 'active' ? 'bg-green-100 text-green-800' :
                                            template.status === 'draft' ? 'bg-yellow-100 text-yellow-800' :
                                            'bg-gray-100 text-gray-800'
                                        ]"
                                    >
                                        {{ statuses[template.status] }}
                                    </span>
                                    
                                    <span
                                        v-if="template.is_system_template"
                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800"
                                    >
                                        Sistema
                                    </span>
                                </div>
                                
                                <p class="text-sm text-gray-600 mb-2">{{ template.subject }}</p>
                                
                                <div v-if="template.description" class="text-sm text-gray-500 mb-3">
                                    {{ template.description }}
                                </div>
                                
                                <div class="flex items-center gap-4 text-sm text-gray-500">
                                    <span class="inline-flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a.997.997 0 01-1.414 0l-7-7A1.997 1.997 0 013 12V7a4 4 0 014-4z" />
                                        </svg>
                                        {{ categories[template.category] }}
                                    </span>
                                    
                                    <span class="inline-flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                        </svg>
                                        {{ template.creator?.name }}
                                    </span>
                                    
                                    <span class="inline-flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ formatDate(template.created_at) }}
                                    </span>
                                </div>
                            </div>
                            
                            <div class="flex items-center gap-2 ml-4">
                                <button
                                    @click="previewTemplate(template)"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 text-sm text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    Vista previa
                                </button>
                                
                                <Link
                                    :href="route('email-templates.edit', template.id)"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 text-sm text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2.828 2.828 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                    Editar
                                </Link>
                                
                                <div class="relative" ref="dropdownRef">
                                    <button
                                        @click="toggleDropdown(template.id)"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 text-sm text-gray-600 hover:text-gray-900 hover:bg-gray-50 rounded-lg transition-colors"
                                    >
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                        </svg>
                                    </button>
                                    
                                    <div
                                        v-if="activeDropdown === template.id"
                                        class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-10"
                                    >
                                        <button
                                            @click="duplicateTemplate(template)"
                                            class="block w-full px-4 py-2 text-left text-sm text-gray-700 hover:bg-gray-50"
                                        >
                                            Duplicar
                                        </button>
                                        
                                        <button
                                            v-if="!template.is_system_template"
                                            @click="deleteTemplate(template)"
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
                <div v-if="templates.data.length > 0" class="px-6 py-4 border-t border-gray-200 bg-gray-50">
                    <Pagination :links="templates.links" />
                </div>
            </div>
        </div>

        <!-- Preview Modal -->
        <Modal :show="showPreviewModal" @close="closePreviewModal">
            <div class="p-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900">Vista previa del template</h3>
                    <button
                        @click="closePreviewModal"
                        class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                
                <div v-if="previewContent" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Asunto</label>
                        <div class="p-3 bg-gray-50 rounded-lg text-sm">
                            {{ previewContent.subject }}
                        </div>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Contenido</label>
                        <div class="border border-gray-200 rounded-lg p-4 max-h-96 overflow-y-auto">
                            <div 
                                v-html="stripScripts(previewContent.html_content)"
                                class="w-full h-64 border-0 overflow-auto bg-white"
                                style="border: 1px solid #e5e7eb; border-radius: 0.5rem;"
                            ></div>
                        </div>
                    </div>
                </div>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { router, Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Pagination from '@/Components/Pagination.vue'
import Modal from '@/Components/Modal.vue'

// Props
const props = defineProps({
    templates: Object,
    filters: Object,
    categories: Object,
    statuses: Object,
    stats: Object
})

// State
const searchQuery = ref(props.filters.search || '')
const selectedCategory = ref(props.filters.category || '')
const selectedStatus = ref(props.filters.status || '')
const selectedType = ref(props.filters.system_only ? 'system' : props.filters.user_only ? 'user' : '')
const activeDropdown = ref(null)
const showPreviewModal = ref(false)
const previewContent = ref(null)
const searchTimeout = ref(null)

// Methods
const debouncedSearch = () => {
    if (searchTimeout.value) {
        clearTimeout(searchTimeout.value)
    }
    searchTimeout.value = setTimeout(() => {
        applyFilters()
    }, 500)
}

const applyFilters = () => {
    const params = {}
    
    if (searchQuery.value) params.search = searchQuery.value
    if (selectedCategory.value) params.category = selectedCategory.value
    if (selectedStatus.value) params.status = selectedStatus.value
    if (selectedType.value === 'system') params.system_only = true
    if (selectedType.value === 'user') params.user_only = true
    
    router.get(route('email-templates.index'), params, {
        preserveState: true,
        preserveScroll: true
    })
}

const toggleDropdown = (templateId) => {
    activeDropdown.value = activeDropdown.value === templateId ? null : templateId
}

const closeDropdowns = () => {
    activeDropdown.value = null
}

const previewTemplate = async (template) => {
    try {
        const response = await fetch(route('email-templates.preview', template.id) + '?json=1', {
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            credentials: 'same-origin'
        })
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`)
        }
        
        const contentType = response.headers.get('content-type')
        if (!contentType || !contentType.includes('application/json')) {
            throw new Error('Response is not JSON')
        }
        
        previewContent.value = await response.json()
        showPreviewModal.value = true
    } catch (error) {
        console.error('Error loading preview:', error)
        alert('Error al cargar la vista previa. Por favor, recarga la página e intenta nuevamente.')
    }
}

const closePreviewModal = () => {
    showPreviewModal.value = false
    previewContent.value = null
}

const duplicateTemplate = (template) => {
    const newName = prompt('Nombre para el template duplicado:', template.name + ' (Copia)')
    if (newName) {
        router.post(route('email-templates.duplicate', template.id), {
            name: newName
        })
    }
    closeDropdowns()
}

const deleteTemplate = (template) => {
    if (confirm('¿Estás seguro de que quieres eliminar este template?')) {
        router.delete(route('email-templates.destroy', template.id))
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

// Event listeners
onMounted(() => {
    document.addEventListener('click', closeDropdowns)
})

onUnmounted(() => {
    document.removeEventListener('click', closeDropdowns)
    if (searchTimeout.value) {
        clearTimeout(searchTimeout.value)
    }
})
</script>