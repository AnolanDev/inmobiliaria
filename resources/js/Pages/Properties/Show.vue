<template>
  <Head :title="property.title" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
          <Link
            :href="route('properties.index')"
            class="text-gray-400 hover:text-gray-600 transition-colors duration-200"
          >
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
          </Link>
          <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
              {{ property.title }}
            </h2>
            <div class="flex items-center space-x-4 mt-1">
              <span class="text-sm text-gray-500">
                {{ property.address }}, {{ property.city }}
              </span>
              <span 
                :class="['inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium', getStatusColor(currentStatus)]"
                :style="getStatusStyle(currentStatus)"
              >
                {{ getStatusName(currentStatus) }}
              </span>
            </div>
          </div>
        </div>
        <div class="flex space-x-2">
          <Link
            :href="route('visits.create', { property_id: property.id })"
            class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none focus:border-green-900 focus:ring focus:ring-green-300 disabled:opacity-25 transition ease-in-out duration-150"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            Nueva Visita
          </Link>
          <Link
            :href="route('properties.edit', property.id)"
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
            <!-- Property Images and Videos -->
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
              <!-- Cover Image -->
              <div class="aspect-video relative">
                <img
                  :src="property.cover_image_url"
                  :alt="property.title"
                  class="w-full h-full object-cover"
                />
                
                <!-- Badges Overlay -->
                <div class="absolute top-4 left-4 space-y-2">
                  <span class="inline-block bg-green-600 text-white px-3 py-1 rounded-full text-sm font-medium">
                    {{ property.type === 'sale' ? 'Venta' : 'Alquiler' }}
                  </span>
                  <span class="block bg-white bg-opacity-90 text-gray-900 px-3 py-1 rounded-full text-sm font-medium">
                    {{ getCategoryName(property.category) }}
                  </span>
                </div>

                <!-- Price -->
                <div class="absolute bottom-4 right-4">
                  <div class="bg-black bg-opacity-75 text-white px-4 py-2 rounded-lg">
                    <span class="text-2xl font-bold">
                      ${{ Number(property.price).toLocaleString() }}
                    </span>
                    <span v-if="property.type === 'rent'" class="text-sm">/mes</span>
                  </div>
                </div>
              </div>
              
              <!-- Gallery -->
              <div v-if="property.gallery_urls && property.gallery_urls.length > 0" class="p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Galería de imágenes</h3>
                
                <!-- Gallery Carousel -->
                <div class="mb-6">
                  <div class="relative bg-gray-100 rounded-xl overflow-hidden">
                    <!-- Main Image Display -->
                    <div class="aspect-video">
                      <img
                        :src="property.gallery_urls[currentGalleryIndex]"
                        :alt="`Imagen ${currentGalleryIndex + 1}`"
                        class="w-full h-full object-cover cursor-pointer"
                        @click="openLightbox(currentGalleryIndex)"
                      />
                    </div>
                    
                    <!-- Navigation Arrows -->
                    <div v-if="property.gallery_urls.length > 1" class="absolute inset-0 flex items-center justify-between p-4">
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
                        :class="{ 'opacity-50 cursor-not-allowed': currentGalleryIndex === property.gallery_urls.length - 1 }"
                      >
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                      </button>
                    </div>

                    <!-- Image Counter -->
                    <div v-if="property.gallery_urls.length > 1" class="absolute top-4 right-4">
                      <div class="bg-black bg-opacity-50 text-white px-3 py-1 rounded-full text-sm">
                        {{ currentGalleryIndex + 1 }} / {{ property.gallery_urls.length }}
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
                  <div v-if="property.gallery_urls.length > 1" class="flex justify-center space-x-2 mt-4">
                    <button
                      v-for="(image, index) in property.gallery_urls"
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
                    v-for="(image, index) in property.gallery_urls"
                    :key="index"
                    class="aspect-square rounded-lg overflow-hidden cursor-pointer transition-all duration-200 border-2"
                    :class="index === currentGalleryIndex 
                      ? 'border-green-500 shadow-lg' 
                      : 'border-transparent hover:border-gray-300 hover:shadow-md'"
                    @click="currentGalleryIndex = index"
                  >
                    <img
                      :src="image"
                      :alt="`Miniatura ${index + 1}`"
                      class="w-full h-full object-cover"
                    />
                  </div>
                </div>
              </div>

              <!-- Videos -->
              <div v-if="property.video_urls && property.video_urls.length > 0" class="p-6 border-t border-gray-200">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Videos</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div
                    v-for="(video, index) in property.video_urls"
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

            <!-- Description -->
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
              <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-3">Descripción</h3>
                <p class="text-gray-700 leading-relaxed whitespace-pre-wrap">{{ property.description }}</p>
              </div>
            </div>

            <!-- Property Features -->
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
              <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Características</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                  <div v-if="property.bedrooms > 0" class="text-center">
                    <div class="inline-flex items-center justify-center w-12 h-12 bg-green-100 rounded-lg mb-2">
                      <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"/>
                      </svg>
                    </div>
                    <p class="text-2xl font-bold text-gray-900">{{ property.bedrooms }}</p>
                    <p class="text-sm text-gray-600">Habitaciones</p>
                  </div>
                  
                  <div v-if="property.bathrooms > 0" class="text-center">
                    <div class="inline-flex items-center justify-center w-12 h-12 bg-green-100 rounded-lg mb-2">
                      <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/>
                      </svg>
                    </div>
                    <p class="text-2xl font-bold text-gray-900">{{ property.bathrooms }}</p>
                    <p class="text-sm text-gray-600">Baños</p>
                  </div>
                  
                  <div class="text-center">
                    <div class="inline-flex items-center justify-center w-12 h-12 bg-purple-100 rounded-lg mb-2">
                      <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/>
                      </svg>
                    </div>
                    <p class="text-2xl font-bold text-gray-900">{{ property.area }}</p>
                    <p class="text-sm text-gray-600">m²</p>
                  </div>
                  
                  <div class="text-center">
                    <div class="inline-flex items-center justify-center w-12 h-12 bg-orange-100 rounded-lg mb-2">
                      <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-2m-2 0H7m10 0v-2c0-.553-.447-1-1-1s-1 .447-1 1v2m1-10V9a2 2 0 00-2-2M9 7h3M9 11h3M9 15h3"/>
                      </svg>
                    </div>
                    <p class="text-lg font-bold text-gray-900">{{ getCategoryName(property.category) }}</p>
                    <p class="text-sm text-gray-600">Tipo</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Project Information -->
            <div v-if="property.project" class="bg-white shadow-sm rounded-lg overflow-hidden">
              <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Proyecto</h3>
                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                  <div class="flex-1">
                    <h4 class="text-lg font-medium text-gray-900">{{ property.project.name }}</h4>
                    <div class="flex items-center space-x-4 mt-2">
                      <span 
                        :class="['inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium', getProjectTypeColor(property.project.type)]"
                        :style="getProjectTypeStyle(property.project.type)"
                      >
                        {{ property.project.type }}
                      </span>
                      <span 
                        :class="['inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium', getProjectStatusColor(property.project.status)]"
                        :style="getProjectStatusStyle(property.project.status)"
                      >
                        {{ property.project.status }}
                      </span>
                    </div>
                    <p v-if="property.project.description" class="text-sm text-gray-600 mt-2">
                      {{ property.project.description }}
                    </p>
                  </div>
                  <Link
                    :href="route('projects.show', property.project.id)"
                    class="ml-4 inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-green-700 bg-green-100 hover:bg-green-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                  >
                    Ver proyecto
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
                <h3 class="text-lg font-medium text-gray-900 mb-4">Agente responsable</h3>
                
                <div v-if="property.agent" class="flex items-center space-x-4">
                  <div class="flex-shrink-0">
                    <div class="h-12 w-12 rounded-full bg-gray-300 flex items-center justify-center">
                      <svg class="h-6 w-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                      </svg>
                    </div>
                  </div>
                  <div>
                    <p class="text-lg font-medium text-gray-900">{{ property.agent.name }}</p>
                    <p v-if="property.agent.email" class="text-sm text-gray-600">{{ property.agent.email }}</p>
                    <p v-if="property.agent.phone" class="text-sm text-gray-600">{{ property.agent.phone }}</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Property Details -->
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
              <div class="px-4 py-5 sm:p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Información de la propiedad</h3>
                
                <dl class="space-y-4">
                  <div>
                    <dt class="text-sm font-medium text-gray-500">Precio</dt>
                    <dd class="mt-1 text-lg font-bold text-gray-900">
                      ${{ Number(property.price).toLocaleString() }}
                      <span v-if="property.type === 'rent'" class="text-sm font-normal text-gray-600">/mes</span>
                    </dd>
                  </div>
                  
                  <div>
                    <dt class="text-sm font-medium text-gray-500">Estado</dt>
                    <dd class="mt-1">
                      <span 
                        :class="['inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium', getStatusColor(currentStatus)]"
                        :style="getStatusStyle(currentStatus)"
                      >
                        {{ getStatusName(currentStatus) }}
                      </span>
                    </dd>
                  </div>
                  
                  <div>
                    <dt class="text-sm font-medium text-gray-500">Tipo de operación</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ property.type === 'sale' ? 'Venta' : 'Alquiler' }}</dd>
                  </div>

                  <div>
                    <dt class="text-sm font-medium text-gray-500">Categoría</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ getCategoryName(property.category) }}</dd>
                  </div>

                  <div>
                    <dt class="text-sm font-medium text-gray-500">Ubicación completa</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                      {{ property.address }}<br>
                      {{ property.city }}, {{ property.state }}
                      <span v-if="property.zip_code">{{ property.zip_code }}</span>
                    </dd>
                  </div>

                  <div>
                    <dt class="text-sm font-medium text-gray-500">Fecha de creación</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ formatDate(property.created_at) }}</dd>
                  </div>

                  <div v-if="property.updated_at !== property.created_at">
                    <dt class="text-sm font-medium text-gray-500">Última actualización</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ formatDate(property.updated_at) }}</dd>
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
                    :href="route('properties.edit', property.id)"
                    class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                  >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Editar propiedad
                  </Link>
                  
                  <Link
                    v-if="property.project"
                    :href="route('projects.show', property.project.id)"
                    class="w-full inline-flex justify-center items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
                  >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-2m-2 0H7m10 0v-2c0-.553-.447-1-1-1s-1 .447-1 1v2m1-10V9a2 2 0 00-2-2M9 7h3M9 11h3M9 15h3"/>
                    </svg>
                    Ver proyecto
                  </Link>
                  
                  <button
                    @click="toggleStatus"
                    :class="[
                      'w-full inline-flex justify-center items-center px-4 py-2 border shadow-sm text-sm font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2',
                      getStatusToggleClass()
                    ]"
                  >
                    <svg v-if="currentStatus === 'available'" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <svg v-else class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    {{ getStatusToggleText() }}
                  </button>
                  
                  <button
                    @click="confirmDelete"
                    class="w-full inline-flex justify-center items-center px-4 py-2 border border-red-300 shadow-sm text-sm font-medium rounded-md text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
                  >
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Eliminar propiedad
                  </button>
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
              :src="property.gallery_urls[currentImageIndex]"
              :alt="`Imagen ${currentImageIndex + 1}`"
              class="max-w-full max-h-full object-contain"
            />
          </div>
          
          <!-- Navigation arrows -->
          <div v-if="property.gallery_urls.length > 1" class="absolute inset-y-0 left-0 flex items-center">
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
          
          <div v-if="property.gallery_urls.length > 1" class="absolute inset-y-0 right-0 flex items-center">
            <button
              @click="nextImage"
              class="mr-4 p-3 bg-black bg-opacity-50 hover:bg-opacity-75 text-white rounded-full transition-all duration-200 hover:scale-110"
              :class="{ 'opacity-50 cursor-not-allowed': currentImageIndex === property.gallery_urls.length - 1 }"
              :disabled="currentImageIndex === property.gallery_urls.length - 1"
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
                <h4 class="font-medium">{{ property.title }}</h4>
                <p class="text-sm text-gray-300">Galería de imágenes</p>
              </div>
              <div class="bg-black bg-opacity-50 text-white px-3 py-2 rounded-full text-sm font-medium">
                {{ currentImageIndex + 1 }} / {{ property.gallery_urls.length }}
              </div>
            </div>
          </div>

          <!-- Thumbnail strip -->
          <div v-if="property.gallery_urls.length > 1" class="absolute bottom-16 left-1/2 transform -translate-x-1/2">
            <div class="flex space-x-2 bg-black bg-opacity-50 rounded-lg p-2">
              <button
                v-for="(image, index) in property.gallery_urls.slice(Math.max(0, currentImageIndex - 2), currentImageIndex + 3)"
                :key="index + Math.max(0, currentImageIndex - 2)"
                @click="currentImageIndex = index + Math.max(0, currentImageIndex - 2)"
                class="w-12 h-12 rounded overflow-hidden border-2 transition-all duration-200"
                :class="(index + Math.max(0, currentImageIndex - 2)) === currentImageIndex 
                  ? 'border-white' 
                  : 'border-transparent hover:border-gray-400'"
              >
                <img
                  :src="image"
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

    <!-- Delete Confirmation Modal -->
    <Modal :show="showDeleteModal" @close="cancelDelete">
      <div class="p-6">
        <div class="flex items-center">
          <div class="flex-shrink-0">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
              <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
              </svg>
            </div>
          </div>
          <div class="ml-4">
            <h3 class="text-lg font-medium text-gray-900">Eliminar Propiedad</h3>
            <div class="mt-2">
              <p class="text-sm text-gray-500">
                ¿Estás seguro de que quieres eliminar la propiedad <strong>{{ property.title }}</strong>? Esta acción no se puede deshacer.
              </p>
            </div>
          </div>
        </div>
        <div class="mt-6 flex justify-end space-x-3">
          <button
            @click="cancelDelete"
            type="button"
            class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
          >
            Cancelar
          </button>
          <button
            @click="deleteProperty"
            type="button"
            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500"
          >
            Eliminar
          </button>
        </div>
      </div>
    </Modal>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import Modal from '@/Components/Modal.vue'

const props = defineProps({
  property: {
    type: Object,
    required: true
  }
})

// Gallery state
const currentGalleryIndex = ref(0)

// Lightbox state
const showLightbox = ref(false)
const currentImageIndex = ref(null)

// Delete confirmation state
const showDeleteModal = ref(false)

// Local property status for immediate UI feedback
const currentStatus = ref(props.property.status)

// Methods
const getCategoryName = (category) => {
  const categories = {
    'house': 'Casa',
    'apartment': 'Apartamento',
    'office': 'Oficina',
    'land': 'Terreno',
    'commercial': 'Local Comercial'
  }
  return categories[category] || category
}

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
    'available': '',       // Se maneja con estilos inline
    'sold': '',            // Se maneja con estilos inline
    'rented': '',          // Se maneja con estilos inline
    'pending': '',         // Se maneja con estilos inline
    'reserved': ''         // Se maneja con estilos inline
  }
  return colors[status] || 'bg-gray-100 text-gray-800'
}

const getStatusStyle = (status) => {
  const styles = {
    'available': { backgroundColor: '#10b981', color: 'white' },    // Verde vibrante
    'sold': { backgroundColor: '#ef4444', color: 'white' },         // Rojo intenso
    'rented': { backgroundColor: '#3b82f6', color: 'white' },       // Azul brillante
    'pending': { backgroundColor: '#eab308', color: 'white' },      // Amarillo vibrante
    'reserved': { backgroundColor: '#a855f7', color: 'white' }      // Púrpura intenso
  }
  return styles[status] || {}
}

const getProjectTypeColor = (type) => {
  const colors = {
    'Campestres': '',         // Se maneja con estilos inline
    'Urbanos': '',            // Se maneja con estilos inline
    'Turísticos': ''          // Se maneja con estilos inline
  }
  return colors[type] || 'bg-gray-100 text-gray-800'
}

const getProjectTypeStyle = (type) => {
  const styles = {
    'Campestres': { backgroundColor: '#10b981', color: 'white' },     // Verde vibrante
    'Urbanos': { backgroundColor: '#3b82f6', color: 'white' },        // Azul brillante
    'Turísticos': { backgroundColor: '#a855f7', color: 'white' }      // Púrpura intenso
  }
  return styles[type] || {}
}

const getProjectStatusColor = (status) => {
  const colors = {
    'Vendido': '',              // Se maneja con estilos inline
    'Disponible': '',           // Se maneja con estilos inline
    'Reservado': ''             // Se maneja con estilos inline
  }
  return colors[status] || 'bg-gray-100 text-gray-800'
}

const getProjectStatusStyle = (status) => {
  const styles = {
    'Vendido': { backgroundColor: '#ef4444', color: 'white' },        // Rojo intenso
    'Disponible': { backgroundColor: '#10b981', color: 'white' },     // Verde vibrante
    'Reservado': { backgroundColor: '#eab308', color: 'white' }       // Amarillo vibrante
  }
  return styles[status] || {}
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('es-ES', {
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

// Gallery navigation functions
const previousGalleryImage = () => {
  if (currentGalleryIndex.value > 0) {
    currentGalleryIndex.value--
  }
}

const nextGalleryImage = () => {
  if (props.property.gallery_urls && currentGalleryIndex.value < props.property.gallery_urls.length - 1) {
    currentGalleryIndex.value++
  }
}

// Keyboard navigation
const handleKeyPress = (event) => {
  if (!props.property.gallery_urls || props.property.gallery_urls.length === 0) {
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
  if (props.property.gallery_urls && currentImageIndex.value < props.property.gallery_urls.length - 1) {
    currentImageIndex.value++
    currentGalleryIndex.value = currentImageIndex.value // Sync with gallery
  }
}

// Status toggle methods
const getStatusToggleClass = () => {
  return currentStatus.value === 'available'
    ? 'border-red-300 text-red-700 bg-red-50 hover:bg-red-100 focus:ring-red-500'
    : 'border-green-300 text-green-700 bg-green-50 hover:bg-green-100 focus:ring-green-500'
}

const getStatusToggleText = () => {
  return currentStatus.value === 'available' ? 'Marcar como Vendida' : 'Marcar como Disponible'
}

const toggleStatus = () => {
  const newStatus = currentStatus.value === 'available' ? 'sold' : 'available'
  
  // Actualizar inmediatamente la UI local
  const previousStatus = currentStatus.value
  currentStatus.value = newStatus
  
  // Usar la nueva ruta toggle-status dedicada
  router.patch(route('properties.toggle-status', props.property.id), {}, {
    preserveScroll: true,
    onSuccess: () => {
      // Success - currentStatus ya está actualizado
    },
    onError: (errors) => {
      // Revertir en caso de error
      currentStatus.value = previousStatus
      console.log('Error updating status:', errors)
    }
  })
}

// Delete methods
const confirmDelete = () => {
  showDeleteModal.value = true
}

const cancelDelete = () => {
  showDeleteModal.value = false
}

const deleteProperty = () => {
  router.delete(route('properties.destroy', props.property.id), {
    onSuccess: () => {
      // Redirect to properties index after successful deletion
      router.visit(route('properties.index'))
    }
  })
}
</script>