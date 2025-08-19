<template>
  <div class="bg-white shadow-sm rounded-lg overflow-hidden">
    <!-- Header with drag info -->
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
      <div class="flex items-center justify-between">
        <div>
          <h3 class="text-lg font-medium text-gray-900">Ordenar Blogs</h3>
          <p class="text-sm text-gray-500 mt-1">
            Arrastra y suelta las filas para cambiar el orden automáticamente. Usa el toggle para cambiar la visibilidad pública.
          </p>
        </div>
        <div class="text-sm text-gray-500">
          <svg class="w-5 h-5 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
          </svg>
          Auto-guardado
        </div>
      </div>
    </div>

    <!-- Draggable Table -->
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-8">
              #
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Blog
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Categoría
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Estado
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Visibilidad
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Vistas
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Orden
            </th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <draggable
            v-model="localBlogs"
            :component-data="{ tag: 'tr', type: 'transition-group' }"
            v-bind="dragOptions"
            @start="onDragStart"
            @end="onDragEnd"
            item-key="id"
          >
            <template #item="{ element: blog, index }">
              <tr 
                :class="[
                  'transition-all duration-200',
                  dragging ? 'cursor-grabbing' : 'cursor-grab hover:bg-gray-50',
                  !blog.is_public ? 'opacity-60' : ''
                ]"
              >
                <!-- Drag Handle -->
                <td class="px-6 py-4 whitespace-nowrap text-center">
                  <div class="flex items-center justify-center">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"/>
                    </svg>
                  </div>
                </td>

                <!-- Blog Title -->
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div class="flex-shrink-0 h-10 w-10">
                      <img 
                        class="h-10 w-10 rounded-lg object-cover" 
                        :src="blog.cover_image_url || '/images/blog-placeholder.jpg'" 
                        :alt="blog.title"
                      >
                    </div>
                    <div class="ml-4">
                      <div class="text-sm font-medium text-gray-900">{{ blog.title }}</div>
                      <div class="text-sm text-gray-500" v-if="blog.excerpt">
                        {{ truncateText(blog.excerpt, 40) }}
                      </div>
                      <div class="text-xs text-gray-400">
                        {{ formatDate(blog.published_at || blog.created_at) }}
                      </div>
                    </div>
                  </div>
                </td>

                <!-- Category -->
                <td class="px-6 py-4 whitespace-nowrap">
                  <span 
                    class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                    :class="getCategoryColor(blog.category)"
                  >
                    {{ getCategoryLabel(blog.category) }}
                  </span>
                </td>

                <!-- Status -->
                <td class="px-6 py-4 whitespace-nowrap">
                  <span 
                    class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                    :class="getStatusColor(blog.status)"
                  >
                    {{ getStatusLabel(blog.status) }}
                  </span>
                </td>

                <!-- Public Status -->
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <button
                      @click="togglePublicStatus(blog)"
                      :disabled="updatingVisibility.has(blog.id)"
                      class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed"
                      :class="blog.is_public ? 'bg-green-600' : 'bg-gray-200'"
                    >
                      <span
                        class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                        :class="blog.is_public ? 'translate-x-5' : 'translate-x-0'"
                      ></span>
                    </button>
                    <span class="ml-2 text-sm text-gray-500">
                      {{ blog.is_public ? 'Público' : 'Privado' }}
                    </span>
                  </div>
                </td>

                <!-- Views Count -->
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center text-sm text-gray-500">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                    {{ blog.views_count || 0 }}
                  </div>
                </td>

                <!-- Sort Order -->
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ index + 1 }}
                </td>
              </tr>
            </template>
          </draggable>
        </tbody>
      </table>
    </div>

    <!-- Auto-save Status -->
    <div v-if="saving" class="px-6 py-3 bg-blue-50 border-t border-blue-200">
      <div class="flex items-center">
        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-blue-600" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <p class="text-sm text-blue-700">
          Guardando orden automáticamente...
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import draggable from 'vuedraggable'

const props = defineProps({
  blogs: {
    type: Array,
    required: true
  }
})

const emit = defineEmits(['orderUpdated'])

// State
const localBlogs = ref([...props.blogs])
const dragging = ref(false)
const saving = ref(false)
const updatingVisibility = ref(new Set())

const dragOptions = computed(() => ({
  animation: 200,
  group: 'blogs',
  disabled: false,
  ghostClass: 'ghost'
}))

// Watch for props changes
watch(
  () => props.blogs,
  (newBlogs) => {
    localBlogs.value = [...newBlogs]
  },
  { deep: true }
)

// Methods
const onDragStart = () => {
  dragging.value = true
}

const onDragEnd = () => {
  dragging.value = false
  
  // Auto-save the new order
  saveOrder()
}

const saveOrder = async () => {
  saving.value = true
  
  try {
    const blogsData = localBlogs.value.map((blog, index) => ({
      id: blog.id,
      sort_order: index
    }))

    await router.post(route('blogs.updateOrder'), {
      blogs: blogsData
    }, {
      preserveState: true,
      preserveScroll: true,
      onSuccess: () => {
        emit('orderUpdated')
      }
    })
  } catch (error) {
    console.error('Error updating blog order:', error)
  } finally {
    saving.value = false
  }
}

const getCategoryColor = (category) => {
  const colors = {
    'inmobiliario': 'bg-blue-100 text-blue-800',
    'mercado': 'bg-green-100 text-green-800',
    'consejos': 'bg-yellow-100 text-yellow-800',
    'legal': 'bg-purple-100 text-purple-800',
    'financiero': 'bg-indigo-100 text-indigo-800',
    'inversion': 'bg-red-100 text-red-800',
    'tecnologia': 'bg-gray-100 text-gray-800',
    'noticias': 'bg-pink-100 text-pink-800'
  }
  return colors[category] || 'bg-gray-100 text-gray-800'
}

const getCategoryLabel = (category) => {
  const labels = {
    'inmobiliario': 'Sector Inmobiliario',
    'mercado': 'Tendencias de Mercado',
    'consejos': 'Consejos de Compra',
    'legal': 'Legal y Normativo',
    'financiero': 'Financiamiento',
    'inversion': 'Inversión',
    'tecnologia': 'Tecnología',
    'noticias': 'Noticias'
  }
  return labels[category] || category
}

const getStatusColor = (status) => {
  const colors = {
    'draft': 'bg-gray-100 text-gray-800',
    'published': 'bg-green-100 text-green-800',
    'archived': 'bg-red-100 text-red-800'
  }
  return colors[status] || 'bg-gray-100 text-gray-800'
}

const getStatusLabel = (status) => {
  const labels = {
    'draft': 'Borrador',
    'published': 'Publicado',
    'archived': 'Archivado'
  }
  return labels[status] || status
}

const formatDate = (dateString) => {
  if (!dateString) return ''
  const date = new Date(dateString)
  return date.toLocaleDateString('es-ES', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  })
}

const truncateText = (text, length) => {
  if (!text) return ''
  return text.length > length ? text.substring(0, length) + '...' : text
}

const togglePublicStatus = async (blog) => {
  const originalStatus = blog.is_public
  
  // Add to updating set
  updatingVisibility.value.add(blog.id)
  
  try {
    // Optimistic update
    blog.is_public = !blog.is_public
    
    await router.patch(route('blogs.toggleVisibility', blog.id), {}, {
      preserveState: true,
      preserveScroll: true,
      onError: () => {
        // Revert on error
        blog.is_public = originalStatus
      },
      onFinish: () => {
        // Remove from updating set
        updatingVisibility.value.delete(blog.id)
      }
    })
  } catch (error) {
    // Revert on error
    blog.is_public = originalStatus
    updatingVisibility.value.delete(blog.id)
    console.error('Error updating blog visibility:', error)
  }
}
</script>

<style scoped>
.ghost {
  opacity: 0.5;
  background: #dbeafe;
}

/* Drag handle hover effect */
tr:hover td:first-child svg {
  color: #6b7280;
}

/* Transition for row changes */
.sortable-chosen {
  background-color: #f3f4f6;
}

.sortable-drag {
  background-color: #ffffff;
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
  transform: rotate(2deg);
}
</style>