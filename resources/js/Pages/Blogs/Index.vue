<template>
  <Head title="Blogs" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Blogs
        </h2>
        <Link
          :href="route('blogs.create')"
          class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring focus:ring-green-300 disabled:opacity-25 transition ease-in-out duration-150"
        >
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
          </svg>
          Nuevo Blog
        </Link>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Filters and Search -->
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-6">
          <div class="p-6 border-b border-gray-200">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between space-y-4 md:space-y-0">
              <div class="flex flex-col md:flex-row md:items-center space-y-4 md:space-y-0 md:space-x-4">
                <!-- Search -->
                <div class="relative">
                  <input
                    v-model="search"
                    type="text"
                    placeholder="Buscar blogs..."
                    class="w-full md:w-64 pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500"
                    @input="performSearch"
                  />
                  <svg class="absolute left-3 top-2.5 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                  </svg>
                </div>

                <!-- Category Filter -->
                <select
                  v-model="selectedCategory"
                  @change="applyFilters"
                  class="border border-gray-300 rounded-md px-3 py-2 focus:ring-green-500 focus:border-green-500"
                >
                  <option value="">Todas las categorías</option>
                  <option v-for="(label, value) in categories" :key="value" :value="value">
                    {{ label }}
                  </option>
                </select>

                <!-- Status Filter -->
                <select
                  v-model="selectedStatus"
                  @change="applyFilters"
                  class="border border-gray-300 rounded-md px-3 py-2 focus:ring-green-500 focus:border-green-500"
                >
                  <option value="">Todos los estados</option>
                  <option v-for="(label, value) in statuses" :key="value" :value="value">
                    {{ label }}
                  </option>
                </select>

                <!-- Clear Filters -->
                <button
                  v-if="hasFilters"
                  @click="clearFilters"
                  class="text-sm text-gray-500 hover:text-gray-700"
                >
                  Limpiar filtros
                </button>
              </div>

              <!-- View Toggle -->
              <div class="flex items-center space-x-2">
                <button
                  @click="currentView = 'cards'"
                  :class="[
                    'px-3 py-2 text-sm font-medium rounded-md transition-colors',
                    currentView === 'cards'
                      ? 'bg-green-600 text-white'
                      : 'text-gray-500 hover:text-gray-700'
                  ]"
                >
                  Tarjetas
                </button>
                <button
                  @click="currentView = 'table'"
                  :class="[
                    'px-3 py-2 text-sm font-medium rounded-md transition-colors',
                    currentView === 'table'
                      ? 'bg-green-600 text-white'
                      : 'text-gray-500 hover:text-gray-700'
                  ]"
                >
                  Tabla
                </button>
                <button
                  @click="currentView = 'sort'"
                  :class="[
                    'px-3 py-2 text-sm font-medium rounded-md transition-colors',
                    currentView === 'sort'
                      ? 'bg-green-600 text-white'
                      : 'text-gray-500 hover:text-gray-700'
                  ]"
                >
                  Ordenar
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Blogs Display -->
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <!-- Sort View (Drag & Drop) -->
          <div v-if="currentView === 'sort'">
            <DraggableBlogTable 
              :blogs="blogs.data" 
              @order-updated="handleOrderUpdated"
            />
          </div>

          <!-- Table View -->
          <div v-if="currentView === 'table'" class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
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
                    Público
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Vistas
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Acciones
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="blog in blogs.data" :key="blog.id" class="hover:bg-gray-50">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div class="flex-shrink-0 h-16 w-16">
                        <img 
                          :src="blog.cover_image_url || '/images/blog-placeholder.jpg'" 
                          :alt="blog.title"
                          class="h-16 w-16 rounded-lg object-cover"
                        />
                      </div>
                      <div class="ml-4">
                        <div class="text-sm font-medium text-gray-900">
                          {{ blog.title }}
                        </div>
                        <div class="text-sm text-gray-500" v-if="blog.excerpt">
                          {{ truncateText(blog.excerpt, 50) }}
                        </div>
                        <div class="text-xs text-gray-400">
                          {{ formatDate(blog.published_at || blog.created_at) }}
                        </div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span :class="['inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium', getCategoryColor(blog.category)]">
                      {{ categories[blog.category] || blog.category }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span :class="['inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium', getStatusColor(blog.status)]">
                      {{ statuses[blog.status] || blog.status }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <button
                      @click="toggleVisibility(blog)"
                      :class="[
                        'relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-green-600 focus:ring-offset-2',
                        blog.is_public ? 'bg-green-600' : 'bg-gray-200'
                      ]"
                    >
                      <span
                        :class="[
                          blog.is_public ? 'translate-x-5' : 'translate-x-0',
                          'pointer-events-none relative inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out'
                        ]"
                      >
                        <span
                          :class="[
                            blog.is_public ? 'opacity-0 duration-100 ease-out' : 'opacity-100 duration-200 ease-in',
                            'absolute inset-0 flex h-full w-full items-center justify-center transition-opacity'
                          ]"
                          aria-hidden="true"
                        >
                          <svg class="h-3 w-3 text-gray-400" fill="none" viewBox="0 0 12 12">
                            <path d="M4 8l2-2m0 0l2-2M6 6L4 4m2 2l2 2" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                          </svg>
                        </span>
                        <span
                          :class="[
                            blog.is_public ? 'opacity-100 duration-200 ease-in' : 'opacity-0 duration-100 ease-out',
                            'absolute inset-0 flex h-full w-full items-center justify-center transition-opacity'
                          ]"
                          aria-hidden="true"
                        >
                          <svg class="h-3 w-3 text-blue-600" fill="currentColor" viewBox="0 0 12 12">
                            <path d="M3.707 5.293a1 1 0 00-1.414 1.414l1.414-1.414zM5 8l-.707.707a1 1 0 001.414 0L5 8zm4.707-3.293a1 1 0 00-1.414-1.414l1.414 1.414zm-7.414 2l2 2 1.414-1.414-2-2-1.414 1.414zm3.414 2l4-4-1.414-1.414-4 4 1.414 1.414z" />
                          </svg>
                        </span>
                      </span>
                    </button>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ blog.views_count || 0 }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                    <Link
                      :href="route('blogs.show', blog.id)"
                      class="text-blue-600 hover:text-blue-900"
                    >
                      Ver
                    </Link>
                    <Link
                      :href="route('blogs.edit', blog.id)"
                      class="text-indigo-600 hover:text-indigo-900"
                    >
                      Editar
                    </Link>
                    <button
                      @click="confirmDelete(blog)"
                      class="text-red-600 hover:text-red-900"
                    >
                      Eliminar
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Cards View -->
          <div v-else class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              <Link
                v-for="blog in blogs.data"
                :key="blog.id"
                :href="route('blogs.show', blog.id)"
                class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-lg hover:scale-[1.02] transition-all duration-300 cursor-pointer group"
              >
                <!-- Image -->
                <div class="aspect-video relative overflow-hidden">
                  <img
                    :src="blog.cover_image_url || '/images/blog-placeholder.jpg'"
                    :alt="blog.title"
                    class="w-full h-full object-cover"
                  />
                  <div class="absolute top-4 right-4 flex space-x-2">
                    <span :class="['inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium', getCategoryColor(blog.category)]">
                      {{ categories[blog.category] || blog.category }}
                    </span>
                  </div>
                  <div class="absolute bottom-4 left-4 flex space-x-2">
                    <span :class="['inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium', getStatusColor(blog.status)]">
                      {{ statuses[blog.status] || blog.status }}
                    </span>
                    <span v-if="blog.is_public" class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                      Público
                    </span>
                  </div>
                </div>

                <!-- Content -->
                <div class="p-6">
                  <h3 class="text-lg font-semibold text-gray-900 mb-2">
                    {{ blog.title }}
                  </h3>
                  <p v-if="blog.excerpt" class="text-gray-600 text-sm mb-4">
                    {{ truncateText(blog.excerpt, 100) }}
                  </p>
                  
                  <!-- Meta Info -->
                  <div class="flex items-center justify-between text-sm text-gray-500 mb-4">
                    <div class="flex items-center">
                      <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                      </svg>
                      {{ blog.views_count || 0 }} vistas
                    </div>
                    <div>
                      {{ formatDate(blog.published_at || blog.created_at) }}
                    </div>
                  </div>

                  <!-- Tags -->
                  <div v-if="blog.tags && blog.tags.length" class="flex flex-wrap gap-1 mb-4">
                    <span 
                      v-for="tag in blog.tags.slice(0, 3)" 
                      :key="tag"
                      class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-gray-100 text-gray-600"
                    >
                      {{ tag }}
                    </span>
                    <span v-if="blog.tags.length > 3" class="text-xs text-gray-400">
                      +{{ blog.tags.length - 3 }} más
                    </span>
                  </div>

                  <!-- Actions -->
                  <div class="flex space-x-2">
                    <span class="flex-1 inline-flex justify-center items-center px-3 py-2 text-sm font-medium text-gray-600 group-hover:text-blue-600 transition-colors">
                      Ver detalles
                    </span>
                    <button
                      @click.stop.prevent="editBlog(blog.id)"
                      class="inline-flex items-center p-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                      </svg>
                    </button>
                    <button
                      @click.stop.prevent="confirmDelete(blog)"
                      class="inline-flex items-center p-2 border border-red-300 shadow-sm text-sm font-medium rounded-md text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                    >
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                      </svg>
                    </button>
                  </div>
                </div>
              </Link>
            </div>
          </div>

          <!-- Empty State -->
          <div v-if="blogs.data.length === 0" class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 20H5a2 2 0 01-2-2V6a2 2 0 012-2h10a2 2 0 012 2v1m2 13a2 2 0 01-2-2V7m2 13a2 2 0 002-2V9a2 2 0 00-2-2h-2m-4-3H9M7 16h6M7 8h6v4H7V8z"/>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No hay blogs</h3>
            <p class="mt-1 text-sm text-gray-500">Comienza creando un nuevo blog.</p>
            <div class="mt-6">
              <Link
                :href="route('blogs.create')"
                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
              >
                <svg class="-ml-1 mr-2 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
                </svg>
                Nuevo Blog
              </Link>
            </div>
          </div>
        </div>

        <!-- Pagination -->
        <div v-if="blogs.links && blogs.links.length > 3" class="mt-6 flex justify-center">
          <nav class="flex items-center space-x-2">
            <component
              v-for="(link, index) in blogs.links"
              :key="index"
              :is="link.url ? 'Link' : 'span'"
              :href="link.url"
              v-html="link.label"
              :class="[
                'px-3 py-2 text-sm font-medium rounded-md',
                link.active
                  ? 'bg-blue-600 text-white'
                  : link.url
                  ? 'bg-white text-gray-700 hover:bg-gray-50 border border-gray-300'
                  : 'bg-gray-100 text-gray-400 cursor-not-allowed'
              ]"
            />
          </nav>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div v-if="showDeleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full flex items-center justify-center z-50" @click.self="showDeleteModal = false">
      <div class="relative p-4 w-full max-w-md mx-auto">
        <div class="bg-white rounded-lg shadow-xl" @click.stop>
          <div class="p-6">
            <div class="flex items-center">
              <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
              </div>
              <div class="ml-4">
                <h3 class="text-lg font-medium text-gray-900">Eliminar blog</h3>
                <p class="mt-2 text-sm text-gray-500">
                  ¿Estás seguro de que deseas eliminar "{{ blogToDelete?.title }}"? Esta acción no se puede deshacer y se eliminarán todos los archivos asociados.
                </p>
              </div>
            </div>
            <div class="mt-6 flex justify-end space-x-3">
              <button
                @click="showDeleteModal = false"
                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
              >
                Cancelar
              </button>
              <button
                @click="deleteBlog"
                class="px-4 py-2 text-sm font-medium text-white bg-red-600 border border-transparent rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
              >
                Eliminar
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import DraggableBlogTable from '@/Components/Blogs/DraggableBlogTable.vue'
import { debounce } from 'lodash'

const props = defineProps({
  blogs: Object,
  filters: Object,
  categories: Object,
  statuses: Object
})

// State
const currentView = ref('cards')
const search = ref(props.filters.search || '')
const selectedCategory = ref(props.filters.category || '')
const selectedStatus = ref(props.filters.status || '')
const showDeleteModal = ref(false)
const blogToDelete = ref(null)

// Computed
const hasFilters = computed(() => {
  return search.value || selectedCategory.value || selectedStatus.value
})

// Methods
const performSearch = debounce(() => {
  applyFilters()
}, 300)

const applyFilters = () => {
  router.get(route('blogs.index'), {
    search: search.value,
    category: selectedCategory.value,
    status: selectedStatus.value
  }, {
    preserveState: true,
    replace: true
  })
}

const clearFilters = () => {
  search.value = ''
  selectedCategory.value = ''
  selectedStatus.value = ''
  router.get(route('blogs.index'))
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

const getStatusColor = (status) => {
  const colors = {
    'draft': 'bg-gray-100 text-gray-800',
    'published': 'bg-green-100 text-green-800',
    'archived': 'bg-red-100 text-red-800'
  }
  return colors[status] || 'bg-gray-100 text-gray-800'
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

const toggleVisibility = (blog) => {
  router.patch(route('blogs.toggleVisibility', blog.id), {}, {
    preserveState: true,
    preserveScroll: true,
    onSuccess: () => {
      // Update local state
      blog.is_public = !blog.is_public
    }
  })
}

const editBlog = (blogId) => {
  router.visit(route('blogs.edit', blogId))
}

const confirmDelete = (blog) => {
  console.log('confirmDelete called', blog)
  blogToDelete.value = blog
  showDeleteModal.value = true
  console.log('Modal should be shown', showDeleteModal.value)
}

const deleteBlog = () => {
  if (blogToDelete.value) {
    router.delete(route('blogs.destroy', blogToDelete.value.id), {
      onSuccess: () => {
        showDeleteModal.value = false
        blogToDelete.value = null
      }
    })
  }
}

const handleOrderUpdated = () => {
  // Refresh the page to show updated order
  router.reload({ only: ['blogs'] })
}
</script>