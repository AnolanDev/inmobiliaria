<template>
  <Head :title="agent.name" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
          <Link
            :href="route('agents.index')"
            class="text-gray-400 hover:text-gray-600 transition-colors duration-200"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
          </Link>
          <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
              {{ agent.name }}
            </h2>
            <div class="flex items-center space-x-4 mt-1">
              <span class="text-sm text-gray-500">
                {{ agent.email }}
              </span>
              <span :class="['inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium', 
                agent.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800']">
                {{ agent.is_active ? 'Activo' : 'Inactivo' }}
              </span>
            </div>
          </div>
        </div>
        <div class="flex space-x-2">
          <Link
            :href="route('agents.edit', agent.id)"
            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:border-blue-900 focus:ring focus:ring-blue-300 disabled:opacity-25 transition ease-in-out duration-150"
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
            <!-- Profile Picture and Gallery -->
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
              <!-- Profile Picture -->
              <div class="aspect-square md:aspect-video relative">
                <img
                  :src="agent.profile_picture_url"
                  :alt="agent.name"
                  class="w-full h-full object-cover"
                />
                
                <!-- Badges Overlay -->
                <div class="absolute top-4 left-4 space-y-2">
                  <span :class="['inline-block px-3 py-1 rounded-full text-sm font-medium',
                    agent.type === 'Interno' ? 'bg-blue-600 text-white' : 'bg-purple-600 text-white']">
                    {{ agent.type }}
                  </span>
                </div>
              </div>
              
              <!-- Gallery -->
              <div v-if="agent.gallery_urls && agent.gallery_urls.length > 0" class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Galería de imágenes</h3>
                
                <!-- Gallery Grid -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                  <div
                    v-for="(image, index) in agent.gallery_urls"
                    :key="index"
                    class="aspect-square rounded-lg overflow-hidden cursor-pointer transition-all duration-200 hover:shadow-md"
                    @click="openLightbox(index)"
                  >
                    <img
                      :src="image"
                      :alt="`Imagen ${index + 1}`"
                      class="w-full h-full object-cover"
                    />
                  </div>
                </div>
              </div>

              <!-- Videos -->
              <div v-if="agent.video_urls && agent.video_urls.length > 0" class="p-6 border-t border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Videos</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div
                    v-for="(video, index) in agent.video_urls"
                    :key="index"
                    class="aspect-video rounded-lg overflow-hidden"
                  >
                    <video
                      :src="video"
                      class="w-full h-full object-cover"
                      controls
                      preload="metadata"
                    >
                      Tu navegador no soporta el elemento video.
                    </video>
                  </div>
                </div>
              </div>
            </div>

            <!-- Biography -->
            <div v-if="agent.bio" class="bg-white shadow-sm rounded-lg overflow-hidden">
              <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-3">Biografía</h3>
                <p class="text-gray-700 leading-relaxed whitespace-pre-wrap">{{ agent.bio }}</p>
              </div>
            </div>

            <!-- Properties -->
            <div v-if="agent.properties && agent.properties.length > 0" class="bg-white shadow-sm rounded-lg overflow-hidden">
              <div class="px-4 py-5 sm:p-6">
                <div class="flex items-center justify-between mb-4">
                  <h3 class="text-lg font-medium text-gray-900">Propiedades asignadas</h3>
                  <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                    {{ agent.properties.length }} {{ agent.properties.length === 1 ? 'propiedad' : 'propiedades' }}
                  </span>
                </div>
                
                <div class="grid grid-cols-1 gap-4">
                  <div
                    v-for="property in agent.properties"
                    :key="property.id"
                    class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors duration-200"
                    @click="goToProperty(property.id)"
                  >
                    <div class="flex-1">
                      <h4 class="text-lg font-medium text-gray-900">{{ property.title }}</h4>
                      <div class="flex items-center space-x-4 mt-2">
                        <span :class="['inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                          getStatusColor(property.status)]">
                          {{ getStatusName(property.status) }}
                        </span>
                        <span v-if="property.project" class="text-sm text-gray-600">
                          {{ property.project.name }}
                        </span>
                      </div>
                    </div>
                    <div class="ml-4">
                      <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                      </svg>
                    </div>
                  </div>
                </div>

                <div v-if="agent.properties.length >= 10" class="mt-4 text-center">
                  <Link
                    :href="route('properties.index', { agent_id: agent.id })"
                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                  >
                    Ver todas las propiedades
                  </Link>
                </div>
              </div>
            </div>
          </div>

          <!-- Sidebar -->
          <div class="space-y-6">
            <!-- Contact Info -->
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
              <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Información de contacto</h3>
                
                <dl class="space-y-4">
                  <div>
                    <dt class="text-sm font-medium text-gray-500">Correo electrónico</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                      <a :href="`mailto:${agent.email}`" class="text-blue-600 hover:text-blue-800">
                        {{ agent.email }}
                      </a>
                    </dd>
                  </div>
                  
                  <div>
                    <dt class="text-sm font-medium text-gray-500">Teléfono</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                      <a :href="`tel:${agent.phone}`" class="text-blue-600 hover:text-blue-800">
                        {{ agent.phone }}
                      </a>
                    </dd>
                  </div>
                  
                  <div>
                    <dt class="text-sm font-medium text-gray-500">Tipo de agente</dt>
                    <dd class="mt-1">
                      <span :class="['inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                        agent.type === 'Interno' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800']">
                        {{ agent.type }}
                      </span>
                    </dd>
                  </div>

                  <div>
                    <dt class="text-sm font-medium text-gray-500">Estado</dt>
                    <dd class="mt-1">
                      <span :class="['inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                        agent.is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800']">
                        {{ agent.is_active ? 'Activo' : 'Inactivo' }}
                      </span>
                    </dd>
                  </div>
                </dl>
              </div>
            </div>

            <!-- Social Media -->
            <div v-if="hasSocialMedia" class="bg-white shadow-sm rounded-lg overflow-hidden">
              <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Redes sociales</h3>
                
                <div class="space-y-3">
                  <div v-if="agent.facebook" class="flex items-center">
                    <div class="flex-shrink-0">
                      <svg class="w-5 h-5 text-blue-600" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                      </svg>
                    </div>
                    <div class="ml-3">
                      <a
                        :href="agent.facebook"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="text-sm text-blue-600 hover:text-blue-800"
                      >
                        Facebook
                      </a>
                    </div>
                  </div>

                  <div v-if="agent.instagram" class="flex items-center">
                    <div class="flex-shrink-0">
                      <svg class="w-5 h-5 text-pink-600" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 6.62 5.367 11.987 11.987 11.987 6.62 0 11.987-5.367 11.987-11.987C24.014 5.367 18.647.001 12.017.001zM8.449 16.988c-1.297 0-2.448-.596-3.205-1.530-.757-.933-1.183-2.183-1.183-3.456s.426-2.523 1.183-3.456c.757-.934 1.908-1.530 3.205-1.530s2.448.596 3.205 1.530c.757.933 1.183 2.183 1.183 3.456s-.426 2.523-1.183 3.456c-.757.934-1.908 1.530-3.205 1.530zm7.119 0c-1.297 0-2.448-.596-3.205-1.530-.757-.933-1.183-2.183-1.183-3.456s.426-2.523 1.183-3.456c.757-.934 1.908-1.530 3.205-1.530s2.448.596 3.205 1.530c.757.933 1.183 2.183 1.183 3.456s-.426 2.523-1.183 3.456c-.757.934-1.908 1.530-3.205 1.530z"/>
                      </svg>
                    </div>
                    <div class="ml-3">
                      <a
                        :href="agent.instagram"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="text-sm text-pink-600 hover:text-pink-800"
                      >
                        Instagram
                      </a>
                    </div>
                  </div>

                  <div v-if="agent.linkedin" class="flex items-center">
                    <div class="flex-shrink-0">
                      <svg class="w-5 h-5 text-blue-700" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433c-1.144 0-2.063-.926-2.063-2.065 0-1.138.92-2.063 2.063-2.063 1.14 0 2.064.925 2.064 2.063 0 1.139-.925 2.065-2.064 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/>
                      </svg>
                    </div>
                    <div class="ml-3">
                      <a
                        :href="agent.linkedin"
                        target="_blank"
                        rel="noopener noreferrer"
                        class="text-sm text-blue-700 hover:text-blue-900"
                      >
                        LinkedIn
                      </a>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
              <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Acciones rápidas</h3>
                <div class="space-y-3">
                  <Link
                    :href="route('agents.edit', agent.id)"
                    class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                  >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Editar agente
                  </Link>
                  
                  <Link
                    :href="route('properties.index', { agent_id: agent.id })"
                    class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                  >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-2m-2 0H7m10 0v-2c0-.553-.447-1-1-1s-1 .447-1 1v2m1-10V9a2 2 0 00-2-2M9 7h3M9 11h3M9 15h3"/>
                    </svg>
                    Ver propiedades
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
              :src="agent.gallery_urls[currentImageIndex]"
              :alt="`Imagen ${currentImageIndex + 1}`"
              class="max-w-full max-h-full object-contain"
            />
          </div>
          
          <!-- Navigation arrows -->
          <div v-if="agent.gallery_urls.length > 1" class="absolute inset-y-0 left-0 flex items-center">
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
          
          <div v-if="agent.gallery_urls.length > 1" class="absolute inset-y-0 right-0 flex items-center">
            <button
              @click="nextImage"
              class="mr-4 p-3 bg-black bg-opacity-50 hover:bg-opacity-75 text-white rounded-full transition-all duration-200 hover:scale-110"
              :class="{ 'opacity-50 cursor-not-allowed': currentImageIndex === agent.gallery_urls.length - 1 }"
              :disabled="currentImageIndex === agent.gallery_urls.length - 1"
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
                <h4 class="font-medium">{{ agent.name }}</h4>
                <p class="text-sm text-gray-300">Galería de imágenes</p>
              </div>
              <div class="bg-black bg-opacity-50 text-white px-3 py-2 rounded-full text-sm font-medium">
                {{ currentImageIndex + 1 }} / {{ agent.gallery_urls.length }}
              </div>
            </div>
          </div>
        </div>
      </div>
    </Modal>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Modal from '@/Components/Modal.vue'

const props = defineProps({
  agent: {
    type: Object,
    required: true
  }
})

// Lightbox state
const showLightbox = ref(false)
const currentImageIndex = ref(null)

// Computed properties
const hasSocialMedia = computed(() => {
  return props.agent.facebook || props.agent.instagram || props.agent.linkedin
})

// Methods
const getStatusName = (status) => {
  const statuses = {
    'available': 'Disponible',
    'sold': 'Vendida',
    'rented': 'Alquilada',
    'pending': 'Pendiente'
  }
  return statuses[status] || status
}

const getStatusColor = (status) => {
  const colors = {
    'available': 'bg-green-100 text-green-800',
    'sold': 'bg-red-100 text-red-800',
    'rented': 'bg-blue-100 text-blue-800',
    'pending': 'bg-yellow-100 text-yellow-800'
  }
  return colors[status] || 'bg-gray-100 text-gray-800'
}

const goToProperty = (propertyId) => {
  router.visit(route('properties.show', propertyId))
}

// Lightbox methods
const openLightbox = (index) => {
  currentImageIndex.value = index
  showLightbox.value = true
}

const closeLightbox = () => {
  showLightbox.value = false
  currentImageIndex.value = null
}

const previousImage = () => {
  if (currentImageIndex.value > 0) {
    currentImageIndex.value--
  }
}

const nextImage = () => {
  if (props.agent.gallery_urls && currentImageIndex.value < props.agent.gallery_urls.length - 1) {
    currentImageIndex.value++
  }
}

// Keyboard navigation
const handleKeyPress = (event) => {
  if (!props.agent.gallery_urls || props.agent.gallery_urls.length === 0) {
    return
  }

  switch (event.key) {
    case 'ArrowLeft':
      event.preventDefault()
      if (showLightbox.value) {
        previousImage()
      }
      break
    case 'ArrowRight':
      event.preventDefault()
      if (showLightbox.value) {
        nextImage()
      }
      break
    case 'Escape':
      event.preventDefault()
      if (showLightbox.value) {
        closeLightbox()
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
</script>