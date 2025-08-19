<template>
  <Head :title="blog.title" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
          <Link
            :href="route('blogs.index')"
            class="text-gray-400 hover:text-gray-600 transition-colors duration-200"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
          </Link>
          <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
              {{ blog.title }}
            </h2>
            <p class="text-sm text-gray-600 mt-1">
              {{ blog.excerpt }}
            </p>
          </div>
        </div>
        
        <div class="flex items-center space-x-3">
          <Link
            :href="route('blogs.edit', blog.id)"
            class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring focus:ring-green-300 disabled:opacity-25 transition ease-in-out duration-150"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            Editar
          </Link>
        </div>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <!-- Cover Image -->
          <div v-if="blog.cover_image_url" class="aspect-video relative overflow-hidden">
            <img
              :src="blog.cover_image_url"
              :alt="blog.title"
              class="w-full h-full object-cover"
            />
            <div class="absolute top-4 right-4 flex space-x-2">
              <span :class="['inline-flex items-center px-3 py-1 rounded-full text-sm font-medium', getCategoryColor(blog.category)]">
                {{ categories[blog.category] || blog.category }}
              </span>
            </div>
            <div class="absolute bottom-4 left-4 flex space-x-2">
              <span :class="['inline-flex items-center px-3 py-1 rounded-full text-sm font-medium', getStatusColor(blog.status)]">
                {{ statuses[blog.status] || blog.status }}
              </span>
              <span v-if="blog.is_public" class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                Público
              </span>
            </div>
          </div>

          <!-- Content -->
          <div class="p-8">
            <!-- Meta Info -->
            <div class="flex items-center justify-between text-sm text-gray-500 mb-6">
              <div class="flex items-center space-x-4">
                <div class="flex items-center">
                  <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                  </svg>
                  {{ blog.author || 'Sin autor' }}
                </div>
                <div class="flex items-center">
                  <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 0V5a2 2 0 012-2h4a2 2 0 012 2v2m-6 0h8m-6 0v10a2 2 0 002 2h2a2 2 0 002-2V7"/>
                  </svg>
                  {{ formatDate(blog.published_at || blog.created_at) }}
                </div>
                <div class="flex items-center">
                  <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                  </svg>
                  {{ blog.views_count || 0 }} vistas
                </div>
              </div>
              <div class="text-xs">
                Orden: {{ blog.sort_order }}
              </div>
            </div>

            <!-- Tags -->
            <div v-if="blog.tags && blog.tags.length" class="flex flex-wrap gap-2 mb-6">
              <span 
                v-for="tag in blog.tags" 
                :key="tag"
                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-700"
              >
                #{{ tag }}
              </span>
            </div>

            <!-- Content -->
            <div class="prose prose-lg max-w-none" v-html="blog.content"></div>

            <!-- Gallery -->
            <div v-if="blog.gallery_urls && blog.gallery_urls.length" class="mt-8">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Galería</h3>
              <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div 
                  v-for="(image, index) in blog.gallery_urls" 
                  :key="index"
                  class="aspect-video relative overflow-hidden rounded-lg"
                >
                  <img
                    :src="image"
                    :alt="`Galería ${index + 1}`"
                    class="w-full h-full object-cover hover:scale-105 transition-transform duration-300 cursor-pointer"
                    @click="openImageModal(image)"
                  />
                </div>
              </div>
            </div>
          </div>

          <!-- SEO Information (solo para admin) -->
          <div v-if="blog.meta_title || blog.meta_description || (blog.meta_keywords && blog.meta_keywords.length)" class="border-t border-gray-200 p-8 bg-gray-50">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Información SEO</h3>
            <div class="space-y-4">
              <div v-if="blog.meta_title">
                <label class="block text-sm font-medium text-gray-700">Título SEO</label>
                <p class="mt-1 text-sm text-gray-900">{{ blog.meta_title }}</p>
              </div>
              <div v-if="blog.meta_description">
                <label class="block text-sm font-medium text-gray-700">Descripción SEO</label>
                <p class="mt-1 text-sm text-gray-900">{{ blog.meta_description }}</p>
              </div>
              <div v-if="blog.meta_keywords && blog.meta_keywords.length">
                <label class="block text-sm font-medium text-gray-700">Palabras clave SEO</label>
                <div class="mt-1 flex flex-wrap gap-2">
                  <span 
                    v-for="keyword in blog.meta_keywords" 
                    :key="keyword"
                    class="inline-flex items-center px-2 py-1 rounded-md text-xs font-medium bg-blue-100 text-blue-800"
                  >
                    {{ keyword }}
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Image Modal -->
    <Modal :show="showImageModal" @close="showImageModal = false" max-width="5xl">
      <div class="p-4">
        <img
          v-if="selectedImage"
          :src="selectedImage"
          alt="Imagen ampliada"
          class="w-full h-auto max-h-screen object-contain"
        />
      </div>
    </Modal>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Modal from '@/Components/Modal.vue'

const props = defineProps({
  blog: {
    type: Object,
    required: true
  },
  categories: {
    type: Object,
    required: true
  },
  statuses: {
    type: Object,
    required: true
  }
})

// State for image modal
const showImageModal = ref(false)
const selectedImage = ref(null)

// Methods
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
    month: 'long',
    day: 'numeric'
  })
}

const openImageModal = (image) => {
  selectedImage.value = image
  showImageModal.value = true
}
</script>

<style>
/* Estilos adicionales para el contenido del blog */
.prose {
  color: #374151;
}

.prose h1,
.prose h2,
.prose h3,
.prose h4,
.prose h5,
.prose h6 {
  color: #111827;
  font-weight: 600;
}

.prose p {
  margin-bottom: 1.25em;
}

.prose img {
  border-radius: 0.5rem;
  margin: 1.5rem 0;
}

.prose blockquote {
  border-left: 4px solid #3b82f6;
  padding-left: 1rem;
  font-style: italic;
  background-color: #f8fafc;
  padding: 1rem;
  border-radius: 0.5rem;
}

.prose code {
  background-color: #f1f5f9;
  padding: 0.25rem 0.5rem;
  border-radius: 0.25rem;
  font-size: 0.875em;
}

.prose pre {
  background-color: #1e293b;
  color: #e2e8f0;
  padding: 1rem;
  border-radius: 0.5rem;
  overflow-x: auto;
}

.prose ul,
.prose ol {
  margin: 1.25em 0;
}

.prose li {
  margin: 0.5em 0;
}
</style>