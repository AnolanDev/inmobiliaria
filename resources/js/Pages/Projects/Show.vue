<template>
  <Head :title="project.name" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
          <Link
            :href="route('projects.index')"
            class="text-gray-400 hover:text-gray-600 transition-colors duration-200"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
          </Link>
          <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
              {{ project.name }}
            </h2>
            <div class="flex items-center space-x-4 mt-1">
              <span :class="['inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium', getTypeColor(project.type)]">
                {{ project.type }}
              </span>
              <span :class="['inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium', getStatusColor(project.status)]">
                {{ project.status }}
              </span>
            </div>
          </div>
        </div>
        <div class="flex space-x-2">
          <Link
            :href="route('visits.create', { project_id: project.id })"
            class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none focus:border-green-900 focus:ring focus:ring-green-300 disabled:opacity-25 transition ease-in-out duration-150"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            Nueva Visita
          </Link>
          <Link
            :href="route('projects.edit', project.id)"
            class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none focus:border-green-900 focus:ring focus:ring-green-300 disabled:opacity-25 transition ease-in-out duration-150"
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
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <!-- Main Content -->
          <div class="lg:col-span-2 space-y-6">
            <!-- Project Images and Videos -->
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
              <div class="aspect-video relative">
                <img
                  :src="project.cover_image_url"
                  :alt="project.name"
                  class="w-full h-full object-cover"
                />
              </div>
              
              <!-- Gallery -->
              <div v-if="project.gallery_urls && project.gallery_urls.length > 0" class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Galería de imágenes</h3>
                
                <!-- Gallery Carousel -->
                <div class="mb-6">
                  <div class="relative bg-gray-100 rounded-xl overflow-hidden">
                    <!-- Main Image Display -->
                    <div class="aspect-video">
                      <img
                        :src="getGalleryImageUrl(project.gallery_urls[currentGalleryIndex])"
                        :alt="`Imagen ${currentGalleryIndex + 1}`"
                        class="w-full h-full object-cover cursor-pointer"
                        @click="openLightbox(currentGalleryIndex)"
                      />
                    </div>
                    
                    <!-- Navigation Arrows -->
                    <div v-if="project.gallery_urls.length > 1" class="absolute inset-0 flex items-center justify-between p-4">
                      <button
                        @click="previousGalleryImage"
                        class="p-2 bg-black bg-opacity-50 hover:bg-opacity-75 text-white rounded-full transition-all duration-200 hover:scale-110"
                        :class="{ 'opacity-50 cursor-not-allowed': currentGalleryIndex === 0 }"
                      >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                      </button>
                      
                      <button
                        @click="nextGalleryImage"
                        class="p-2 bg-black bg-opacity-50 hover:bg-opacity-75 text-white rounded-full transition-all duration-200 hover:scale-110"
                        :class="{ 'opacity-50 cursor-not-allowed': currentGalleryIndex === project.gallery_urls.length - 1 }"
                      >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                      </button>
                    </div>

                    <!-- Image Counter -->
                    <div v-if="project.gallery_urls.length > 1" class="absolute top-4 right-4">
                      <div class="bg-black bg-opacity-50 text-white px-3 py-1 rounded-full text-sm">
                        {{ currentGalleryIndex + 1 }} / {{ project.gallery_urls.length }}
                      </div>
                    </div>

                    <!-- Fullscreen Button -->
                    <div class="absolute bottom-4 right-4">
                      <button
                        @click="openLightbox(currentGalleryIndex)"
                        class="p-2 bg-black bg-opacity-50 hover:bg-opacity-75 text-white rounded-full transition-all duration-200"
                        title="Ver en pantalla completa"
                      >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
                        </svg>
                      </button>
                    </div>
                  </div>

                  <!-- Navigation Dots -->
                  <div v-if="project.gallery_urls.length > 1" class="flex justify-center space-x-2 mt-4">
                    <button
                      v-for="(image, index) in project.gallery_urls"
                      :key="index"
                      @click="currentGalleryIndex = index"
                      class="w-3 h-3 rounded-full transition-all duration-200"
                      :class="index === currentGalleryIndex 
                        ? 'bg-green-600' 
                        : 'bg-gray-300 hover:bg-gray-400'"
                    />
                  </div>
                </div>

                <!-- Thumbnail Grid -->
                <div class="grid grid-cols-4 md:grid-cols-6 lg:grid-cols-8 gap-2">
                  <div
                    v-for="(image, index) in project.gallery_urls"
                    :key="index"
                    class="aspect-square rounded-lg overflow-hidden cursor-pointer transition-all duration-200 border-2"
                    :class="index === currentGalleryIndex 
                      ? 'border-green-500 shadow-lg' 
                      : 'border-transparent hover:border-gray-300 hover:shadow-md'"
                    @click="currentGalleryIndex = index"
                  >
                    <img
                      :src="getGalleryImageUrl(image, 'thumbnail')"
                      :alt="`Miniatura ${index + 1}`"
                      class="w-full h-full object-cover"
                    />
                  </div>
                </div>
              </div>

              <!-- Videos -->
              <div v-if="project.videos && project.videos.length > 0" class="p-6 border-t border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Videos</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div
                    v-for="(video, index) in project.videos"
                    :key="index"
                    class="aspect-video rounded-lg overflow-hidden"
                  >
                    <video
                      :src="`/storage/${video}`"
                      class="w-full h-full object-cover"
                      controls
                      preload="metadata"
                    ></video>
                  </div>
                </div>
              </div>
            </div>

            <!-- Description -->
            <div v-if="project.description" class="bg-white shadow-sm rounded-lg overflow-hidden">
              <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-3">Descripción</h3>
                <p class="text-gray-700 leading-relaxed whitespace-pre-wrap">{{ project.description }}</p>
              </div>
            </div>

            <!-- Properties -->
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
              <div class="px-4 py-5 sm:p-6">
                <div class="flex items-center justify-between mb-4">
                  <h3 class="text-lg font-medium text-gray-900">
                    Propiedades ({{ project.properties.length }})
                  </h3>
                  <Link
                    :href="route('properties.create', { project_id: project.id })"
                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-green-700 bg-green-100 hover:bg-green-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                  >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Agregar propiedad
                  </Link>
                </div>

                <div v-if="project.properties.length > 0" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div
                    v-for="property in project.properties"
                    :key="property.id"
                    class="border border-gray-200 rounded-lg p-4 hover:border-gray-300 transition-colors"
                  >
                    <div class="flex items-start justify-between">
                      <div class="flex-1">
                        <h4 class="text-sm font-medium text-gray-900">
                          {{ property.title }}
                        </h4>
                        <p class="text-sm text-gray-500 mt-1">
                          {{ formatPrice(property.price) }}
                        </p>
                        <div class="flex items-center space-x-4 mt-2 text-xs text-gray-500">
                          <span v-if="property.bedrooms">{{ property.bedrooms }} hab.</span>
                          <span v-if="property.bathrooms">{{ property.bathrooms }} baños</span>
                          <span v-if="property.area">{{ property.area }}m²</span>
                        </div>
                        <p v-if="property.agent" class="text-xs text-gray-500 mt-1">
                          Agente: {{ property.agent.name }}
                        </p>
                      </div>
                      <Link
                        :href="route('properties.show', property.id)"
                        class="text-green-600 hover:text-green-800 text-sm"
                      >
                        Ver
                      </Link>
                    </div>
                  </div>
                </div>

                <div v-else class="text-center py-8">
                  <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-2m-2 0H7m10 0v-2c0-.553-.447-1-1-1s-1 .447-1 1v2m1-10V9a2 2 0 00-2-2M9 7h3M9 11h3M9 15h3"/>
                  </svg>
                  <h3 class="mt-2 text-sm font-medium text-gray-900">No hay propiedades</h3>
                  <p class="mt-1 text-sm text-gray-500">Comienza agregando propiedades a este proyecto.</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Sidebar -->
          <div class="space-y-6">
            <!-- Project Stats -->
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
              <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Información del proyecto</h3>
                
                <dl class="space-y-4">
                  <div>
                    <dt class="text-sm font-medium text-gray-500">Estado</dt>
                    <dd class="mt-1">
                      <span :class="['inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium', getStatusColor(project.status)]">
                        {{ project.status }}
                      </span>
                    </dd>
                  </div>
                  
                  <div>
                    <dt class="text-sm font-medium text-gray-500">Tipo</dt>
                    <dd class="mt-1">
                      <span :class="['inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium', getTypeColor(project.type)]">
                        {{ project.type }}
                      </span>
                    </dd>
                  </div>

                  <div v-if="project.property_count">
                    <dt class="text-sm font-medium text-gray-500">Propiedades planificadas</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ project.property_count }}</dd>
                  </div>

                  <div>
                    <dt class="text-sm font-medium text-gray-500">Propiedades registradas</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ project.properties.length }}</dd>
                  </div>

                  <div>
                    <dt class="text-sm font-medium text-gray-500">Fecha de creación</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ formatDate(project.created_at) }}</dd>
                  </div>

                  <div v-if="project.updated_at !== project.created_at">
                    <dt class="text-sm font-medium text-gray-500">Última actualización</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ formatDate(project.updated_at) }}</dd>
                  </div>
                </dl>
              </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
              <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Acciones rápidas</h3>
                <div class="space-y-3">
                  <Link
                    :href="route('properties.create', { project_id: project.id })"
                    class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                  >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Agregar propiedad
                  </Link>
                  
                  <Link
                    :href="route('projects.edit', project.id)"
                    class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                  >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Editar proyecto
                  </Link>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Lightbox Modal -->
    <Modal :show="showLightbox" @close="closeLightbox" max-width="6xl">
      <div class="relative bg-black rounded-lg overflow-hidden">
        <!-- Close button -->
        <button
          @click="closeLightbox"
          class="absolute top-4 right-4 z-20 p-2 bg-black bg-opacity-50 hover:bg-opacity-75 text-white rounded-full transition-all duration-200"
          title="Cerrar (Esc)"
        >
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
          </svg>
        </button>
        
        <div v-if="currentImageIndex !== null" class="relative">
          <!-- Main image -->
          <div class="flex items-center justify-center min-h-[60vh] max-h-[80vh] bg-black">
            <img
              :src="getGalleryImageUrl(project.gallery_urls[currentImageIndex], 'large')"
              :alt="`Imagen ${currentImageIndex + 1}`"
              class="max-w-full max-h-full object-contain"
            />
          </div>
          
          <!-- Navigation arrows -->
          <div v-if="project.gallery_urls.length > 1" class="absolute inset-y-0 left-0 flex items-center">
            <button
              @click="previousImage"
              class="ml-4 p-3 bg-black bg-opacity-50 hover:bg-opacity-75 text-white rounded-full transition-all duration-200 hover:scale-110"
              :class="{ 'opacity-50 cursor-not-allowed': currentImageIndex === 0 }"
              :disabled="currentImageIndex === 0"
              title="Imagen anterior (←)"
            >
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
              </svg>
            </button>
          </div>
          
          <div v-if="project.gallery_urls.length > 1" class="absolute inset-y-0 right-0 flex items-center">
            <button
              @click="nextImage"
              class="mr-4 p-3 bg-black bg-opacity-50 hover:bg-opacity-75 text-white rounded-full transition-all duration-200 hover:scale-110"
              :class="{ 'opacity-50 cursor-not-allowed': currentImageIndex === project.gallery_urls.length - 1 }"
              :disabled="currentImageIndex === project.gallery_urls.length - 1"
              title="Imagen siguiente (→)"
            >
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
              </svg>
            </button>
          </div>

          <!-- Image counter and info -->
          <div class="absolute bottom-0 left-0 right-0 bg-gradient-to-t from-black to-transparent p-6">
            <div class="flex items-center justify-between">
              <div class="text-white">
                <h4 class="font-medium">{{ project.name }}</h4>
                <p class="text-sm text-gray-300">Galería de imágenes</p>
              </div>
              <div class="bg-black bg-opacity-50 text-white px-3 py-2 rounded-full text-sm font-medium">
                {{ currentImageIndex + 1 }} / {{ project.gallery_urls.length }}
              </div>
            </div>
          </div>

          <!-- Thumbnail strip -->
          <div v-if="project.gallery_urls.length > 1" class="absolute bottom-16 left-1/2 transform -translate-x-1/2">
            <div class="flex space-x-2 bg-black bg-opacity-50 rounded-lg p-2">
              <button
                v-for="(image, index) in project.gallery_urls.slice(Math.max(0, currentImageIndex - 2), currentImageIndex + 3)"
                :key="index + Math.max(0, currentImageIndex - 2)"
                @click="currentImageIndex = index + Math.max(0, currentImageIndex - 2)"
                class="w-12 h-12 rounded overflow-hidden border-2 transition-all duration-200"
                :class="(index + Math.max(0, currentImageIndex - 2)) === currentImageIndex 
                  ? 'border-white' 
                  : 'border-transparent hover:border-gray-400'"
              >
                <img
                  :src="getGalleryImageUrl(image, 'thumbnail')"
                  :alt="`Miniatura ${index + Math.max(0, currentImageIndex - 2) + 1}`"
                  class="w-full h-full object-cover"
                />
              </button>
            </div>
          </div>

          <!-- Keyboard shortcuts hint -->
          <div class="absolute top-4 left-4 text-white text-xs bg-black bg-opacity-50 px-2 py-1 rounded">
            <span class="hidden md:inline">← → para navegar • Esc para cerrar</span>
            <span class="md:hidden">Toca las flechas para navegar</span>
          </div>
        </div>
      </div>
    </Modal>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { Head, Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Modal from '@/Components/Modal.vue'

const props = defineProps({
  project: {
    type: Object,
    required: true
  }
})

// Gallery state
const currentGalleryIndex = ref(0)

// Lightbox state
const showLightbox = ref(false)
const currentImageIndex = ref(null)

// Methods
const getTypeColor = (type) => {
  const colors = {
    'Campestres': 'bg-green-100 text-green-800',
    'Urbanos': 'bg-green-100 text-green-800',
    'Turísticos': 'bg-purple-100 text-purple-800'
  }
  return colors[type] || 'bg-gray-100 text-gray-800'
}

const getStatusColor = (status) => {
  const colors = {
    'Vendido': 'bg-red-100 text-red-800',
    'Disponible': 'bg-green-100 text-green-800',
    'Reservado': 'bg-yellow-100 text-yellow-800'
  }
  return colors[status] || 'bg-gray-100 text-gray-800'
}

const formatPrice = (price) => {
  return new Intl.NumberFormat('es-CO', {
    style: 'currency',
    currency: 'COP',
    minimumFractionDigits: 0
  }).format(price)
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('es-ES', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

// Helper function to get image URL from responsive image object
const getGalleryImageUrl = (imageData, size = 'medium') => {
  // Handle legacy string format
  if (typeof imageData === 'string') {
    return imageData
  }
  
  // Handle new responsive format
  if (imageData && typeof imageData === 'object') {
    // Try to get the requested size, fallback to medium, then any available size
    if (imageData[size]?.url) {
      return imageData[size].url
    }
    if (imageData.medium?.url) {
      return imageData.medium.url
    }
    if (imageData.large?.url) {
      return imageData.large.url
    }
    if (imageData.original?.url) {
      return imageData.original.url
    }
    if (imageData.thumbnail?.url) {
      return imageData.thumbnail.url
    }
  }
  
  // Fallback
  return '/placeholder-image.jpg'
}

// Gallery navigation functions
const previousGalleryImage = () => {
  if (currentGalleryIndex.value > 0) {
    currentGalleryIndex.value--
  }
}

const nextGalleryImage = () => {
  if (currentGalleryIndex.value < props.project.gallery_urls.length - 1) {
    currentGalleryIndex.value++
  }
}

// Keyboard navigation
const handleKeyPress = (event) => {
  if (!props.project.gallery_urls || props.project.gallery_urls.length === 0) {
    return
  }

  switch (event.key) {
    case 'ArrowLeft':
      event.preventDefault()
      if (showLightbox.value) {
        previousImage()
      } else {
        previousGalleryImage()
      }
      break
    case 'ArrowRight':
      event.preventDefault()
      if (showLightbox.value) {
        nextImage()
      } else {
        nextGalleryImage()
      }
      break
    case 'Escape':
      event.preventDefault()
      if (showLightbox.value) {
        closeLightbox()
      }
      break
    case ' ':
    case 'Enter':
      event.preventDefault()
      if (!showLightbox.value) {
        openLightbox(currentGalleryIndex.value)
      }
      break
  }
}

// Lifecycle hooks
onMounted(() => {
  document.addEventListener('keydown', handleKeyPress)
})

onUnmounted(() => {
  document.removeEventListener('keydown', handleKeyPress)
})

const openLightbox = (index) => {
  currentImageIndex.value = index
  currentGalleryIndex.value = index // Sync gallery index
  showLightbox.value = true
}

const closeLightbox = () => {
  showLightbox.value = false
  currentImageIndex.value = null
}

const previousImage = () => {
  if (currentImageIndex.value > 0) {
    currentImageIndex.value--
    currentGalleryIndex.value = currentImageIndex.value // Sync with gallery
  }
}

const nextImage = () => {
  if (currentImageIndex.value < props.project.gallery_urls.length - 1) {
    currentImageIndex.value++
    currentGalleryIndex.value = currentImageIndex.value // Sync with gallery
  }
}
</script>