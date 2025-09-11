<template>
  <Head :title="`Editar: ${property.title}`" />

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
              Editar Propiedad
            </h2>
            <p class="text-gray-500 text-sm mt-1">
              {{ property.title }}
            </p>
          </div>
        </div>
        <div class="flex space-x-2">
          <Link
            :href="route('properties.show', property.id)"
            class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-medium rounded-md text-green-700 bg-green-100 hover:bg-green-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
            </svg>
            Ver
          </Link>
        </div>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <form @submit.prevent="submit" enctype="multipart/form-data">
          <!-- Información Básica -->
          <div class="bg-white shadow-sm rounded-lg overflow-hidden mb-6">
            <div class="px-4 py-5 sm:p-6">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Información básica</h3>
              
              <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <!-- Title -->
                <div class="sm:col-span-2">
                  <label for="title" class="block text-sm font-medium text-gray-700">
                    Título de la propiedad *
                  </label>
                  <input
                    id="title"
                    v-model="form.title"
                    type="text"
                    required
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                    :class="{ 'border-red-300': form.errors.title }"
                    placeholder="Ej: Casa familiar con jardín"
                  />
                  <p v-if="form.errors.title" class="mt-1 text-sm text-red-600">{{ form.errors.title }}</p>
                </div>

                <!-- Description -->
                <div class="sm:col-span-2">
                  <label for="description" class="block text-sm font-medium text-gray-700">
                    Descripción *
                  </label>
                  <textarea
                    id="description"
                    v-model="form.description"
                    rows="4"
                    required
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                    :class="{ 'border-red-300': form.errors.description }"
                    placeholder="Describe las características principales de la propiedad..."
                  />
                  <p v-if="form.errors.description" class="mt-1 text-sm text-red-600">{{ form.errors.description }}</p>
                </div>

                <!-- Price -->
                <div>
                  <label for="price" class="block text-sm font-medium text-gray-700">
                    Precio *
                  </label>
                  <div class="mt-1 relative rounded-md shadow-sm">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                      <span class="text-gray-500 sm:text-sm">$</span>
                    </div>
                    <input
                      id="price"
                      v-model.number="form.price"
                      type="number"
                      step="0.01"
                      min="0"
                      required
                      class="block w-full pl-7 border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                      :class="{ 'border-red-300': form.errors.price }"
                      placeholder="350000"
                    />
                  </div>
                  <p v-if="form.errors.price" class="mt-1 text-sm text-red-600">{{ form.errors.price }}</p>
                </div>

                <!-- Type -->
                <div>
                  <label for="type" class="block text-sm font-medium text-gray-700">
                    Tipo de operación *
                  </label>
                  <select
                    id="type"
                    v-model="form.type"
                    required
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                    :class="{ 'border-red-300': form.errors.type }"
                  >
                    <option value="">Selecciona el tipo</option>
                    <option value="sale">Venta</option>
                    <option value="rent">Alquiler</option>
                  </select>
                  <p v-if="form.errors.type" class="mt-1 text-sm text-red-600">{{ form.errors.type }}</p>
                </div>

                <!-- Category -->
                <div>
                  <label for="category" class="block text-sm font-medium text-gray-700">
                    Categoría *
                  </label>
                  <select
                    id="category"
                    v-model="form.category"
                    required
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                    :class="{ 'border-red-300': form.errors.category }"
                  >
                    <option value="">Selecciona la categoría</option>
                    <option value="house">Casa</option>
                    <option value="apartment">Apartamento</option>
                    <option value="office">Oficina</option>
                    <option value="land">Terreno</option>
                    <option value="commercial">Local Comercial</option>
                  </select>
                  <p v-if="form.errors.category" class="mt-1 text-sm text-red-600">{{ form.errors.category }}</p>
                </div>

                <!-- Status -->
                <div>
                  <label for="status" class="block text-sm font-medium text-gray-700">
                    Estado
                  </label>
                  <select
                    id="status"
                    v-model="form.status"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                    :class="{ 'border-red-300': form.errors.status }"
                  >
                    <option value="available">Disponible</option>
                    <option value="sold">Vendida</option>
                    <option value="rented">Alquilada</option>
                    <option value="pending">Pendiente</option>
                  </select>
                  <p v-if="form.errors.status" class="mt-1 text-sm text-red-600">{{ form.errors.status }}</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Ubicación -->
          <div class="bg-white shadow-sm rounded-lg overflow-hidden mb-6">
            <div class="px-4 py-5 sm:p-6">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Ubicación</h3>
              
              <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <!-- Address -->
                <div class="sm:col-span-2">
                  <label for="address" class="block text-sm font-medium text-gray-700">
                    Dirección *
                  </label>
                  <input
                    id="address"
                    v-model="form.address"
                    type="text"
                    required
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                    :class="{ 'border-red-300': form.errors.address }"
                    placeholder="Ej: Calle 123 #45-67"
                  />
                  <p v-if="form.errors.address" class="mt-1 text-sm text-red-600">{{ form.errors.address }}</p>
                </div>

                <!-- City -->
                <div>
                  <label for="city" class="block text-sm font-medium text-gray-700">
                    Ciudad *
                  </label>
                  <input
                    id="city"
                    v-model="form.city"
                    type="text"
                    required
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                    :class="{ 'border-red-300': form.errors.city }"
                    placeholder="Ej: Bogotá"
                  />
                  <p v-if="form.errors.city" class="mt-1 text-sm text-red-600">{{ form.errors.city }}</p>
                </div>

                <!-- State -->
                <div>
                  <label for="state" class="block text-sm font-medium text-gray-700">
                    Departamento/Estado *
                  </label>
                  <input
                    id="state"
                    v-model="form.state"
                    type="text"
                    required
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                    :class="{ 'border-red-300': form.errors.state }"
                    placeholder="Ej: Cundinamarca"
                  />
                  <p v-if="form.errors.state" class="mt-1 text-sm text-red-600">{{ form.errors.state }}</p>
                </div>

                <!-- Zip Code -->
                <div class="sm:col-span-2">
                  <label for="zip_code" class="block text-sm font-medium text-gray-700">
                    Código Postal
                  </label>
                  <input
                    id="zip_code"
                    v-model="form.zip_code"
                    type="text"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                    :class="{ 'border-red-300': form.errors.zip_code }"
                    placeholder="Ej: 110111"
                  />
                  <p v-if="form.errors.zip_code" class="mt-1 text-sm text-red-600">{{ form.errors.zip_code }}</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Características -->
          <div class="bg-white shadow-sm rounded-lg overflow-hidden mb-6">
            <div class="px-4 py-5 sm:p-6">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Características</h3>
              
              <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                <!-- Bedrooms -->
                <div>
                  <label for="bedrooms" class="block text-sm font-medium text-gray-700">
                    Habitaciones
                  </label>
                  <input
                    id="bedrooms"
                    v-model.number="form.bedrooms"
                    type="number"
                    min="0"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                    :class="{ 'border-red-300': form.errors.bedrooms }"
                  />
                  <p v-if="form.errors.bedrooms" class="mt-1 text-sm text-red-600">{{ form.errors.bedrooms }}</p>
                </div>

                <!-- Bathrooms -->
                <div>
                  <label for="bathrooms" class="block text-sm font-medium text-gray-700">
                    Baños
                  </label>
                  <input
                    id="bathrooms"
                    v-model.number="form.bathrooms"
                    type="number"
                    min="0"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                    :class="{ 'border-red-300': form.errors.bathrooms }"
                  />
                  <p v-if="form.errors.bathrooms" class="mt-1 text-sm text-red-600">{{ form.errors.bathrooms }}</p>
                </div>

                <!-- Area -->
                <div>
                  <label for="area" class="block text-sm font-medium text-gray-700">
                    Área (m²) *
                  </label>
                  <input
                    id="area"
                    v-model.number="form.area"
                    type="number"
                    step="0.01"
                    min="0"
                    required
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                    :class="{ 'border-red-300': form.errors.area }"
                    placeholder="120"
                  />
                  <p v-if="form.errors.area" class="mt-1 text-sm text-red-600">{{ form.errors.area }}</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Proyecto -->
          <div class="bg-white shadow-sm rounded-lg overflow-hidden mb-6">
            <div class="px-4 py-5 sm:p-6">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Proyecto</h3>
              
              <ProjectSelect
                id="project_id"
                v-model="form.project_id"
                :projects="projects"
                :types="types"
                :statuses="statuses"
                :error="form.errors.project_id"
                label="Proyecto asociado"
                @project-created="handleProjectCreated"
              />
            </div>
          </div>

          <!-- Multimedia -->
          <div class="bg-white shadow-sm rounded-lg overflow-hidden mb-6">
            <div class="px-4 py-5 sm:p-6">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Multimedia</h3>

              <!-- Cover Image -->
              <div class="mb-6">
                <FileUploader
                  id="cover_image"
                  label="Imagen de portada"
                  accept="image/jpeg,image/png,image/jpg,image/gif,image/webp"
                  :existing-files="existingCoverImage"
                  drag-text="Arrastra la imagen de portada aquí o reemplaza la existente"
                  help-text="JPG, PNG, GIF hasta 5MB. Recomendado: 1200x600px"
                  @files-changed="handleCoverImageChange"
                  @files-removed="handleCoverImageRemove"
                />
                <p v-if="form.errors.cover_image" class="mt-1 text-sm text-red-600">{{ form.errors.cover_image }}</p>
              </div>

              <!-- Gallery -->
              <div class="mb-6">
                <FileUploader
                  id="gallery"
                  label="Galería de imágenes"
                  accept="image/jpeg,image/png,image/jpg,image/gif,image/webp"
                  :multiple="true"
                  :max-files="10"
                  :existing-files="existingGallery"
                  drag-text="Arrastra imágenes adicionales aquí"
                  help-text="JPG, PNG, GIF hasta 5MB cada una. Máximo 10 imágenes"
                  @files-changed="handleGalleryChange"
                  @files-removed="handleGalleryRemove"
                />
                <p v-if="form.errors.gallery" class="mt-1 text-sm text-red-600">{{ form.errors.gallery }}</p>
              </div>

              <!-- Videos -->
              <div>
                <FileUploader
                  id="videos"
                  label="Videos"
                  accept="video/mp4,video/mov,video/avi,video/wmv,video/webm"
                  :multiple="true"
                  :max-files="5"
                  :max-size="104857600"
                  :existing-files="existingVideos"
                  drag-text="Arrastra videos aquí"
                  help-text="MP4, MOV, AVI, WMV, WEBM hasta 100MB cada uno. Máximo 5 videos"
                  @files-changed="handleVideosChange"
                  @files-removed="handleVideosRemove"
                />
                <p v-if="form.errors.videos" class="mt-1 text-sm text-red-600">{{ form.errors.videos }}</p>
              </div>
            </div>
          </div>

          <!-- Agente -->
          <div class="bg-white shadow-sm rounded-lg overflow-hidden mb-6">
            <div class="px-4 py-5 sm:p-6">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Agente responsable</h3>
              
              <AgentSelect
                id="agent_id"
                v-model="form.agent_id"
                :agents="agents"
                :required="false"
                :error="form.errors.agent_id"
                label="Agente responsable"
                @agent-created="handleAgentCreated"
              />
            </div>
          </div>

          <!-- Form Actions -->
          <div class="flex justify-end space-x-3">
            <Link
              :href="route('properties.show', property.id)"
              class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
            >
              Cancelar
            </Link>
            <button
              type="submit"
              :disabled="form.processing"
              class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50"
            >
              <svg v-if="form.processing" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              {{ form.processing ? 'Guardando...' : 'Guardar cambios' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { computed } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import FileUploader from '@/Components/FileUploader.vue'
import ProjectSelect from '@/Components/ProjectSelect.vue'
import AgentSelect from '@/Components/AgentSelect.vue'

const props = defineProps({
  property: {
    type: Object,
    required: true
  },
  agents: {
    type: Array,
    required: true
  },
  projects: {
    type: Array,
    required: true
  },
  types: {
    type: Object,
    required: true
  },
  statuses: {
    type: Object,
    required: true
  }
})

// Computed properties for existing media
const existingCoverImage = computed(() => {
  if (!props.property?.cover_image_url) return []
  return [{
    url: props.property.cover_image_url,
    path: props.property.cover_image || '',
    name: 'cover_image.jpg'
  }]
})

const existingGallery = computed(() => {
  if (!props.property?.gallery_urls || !Array.isArray(props.property.gallery_urls)) return []
  
  return props.property.gallery_urls.map((url, index) => ({
    url,
    path: (props.property.gallery && props.property.gallery[index]) || url,
    name: `gallery_${index}.jpg`
  }))
})

const existingVideos = computed(() => {
  if (!props.property?.videos || !Array.isArray(props.property.videos)) return []
  
  return props.property.videos.map((path, index) => {
    const videoPath = typeof path === 'string' ? path : ''
    
    return {
      url: videoPath ? `/storage/${videoPath}` : '',
      path: videoPath,
      name: `video_${index}.mp4`
    }
  })
})

// Form
const form = useForm({
  title: props.property.title || '',
  description: props.property.description || '',
  price: props.property.price || '',
  type: props.property.type || '',
  category: props.property.category || '',
  address: props.property.address || '',
  city: props.property.city || '',
  state: props.property.state || '',
  zip_code: props.property.zip_code || '',
  bedrooms: props.property.bedrooms || 0,
  bathrooms: props.property.bathrooms || 0,
  area: props.property.area || '',
  agent_id: props.property.agent_id || '',
  project_id: props.property.project_id || '',
  status: props.property.status || 'available',
  cover_image: null,
  gallery: [],
  videos: [],
  remove_gallery: [],
  remove_videos: []
})

// Handle file uploads
const handleCoverImageChange = (files) => {
  form.cover_image = files[0] || null
}

const handleCoverImageRemove = (paths) => {
  form.cover_image = null
}

const handleGalleryChange = (files) => {
  form.gallery = files
}

const handleGalleryRemove = (paths) => {
  form.remove_gallery = [...form.remove_gallery, ...paths]
}

const handleVideosChange = (files) => {
  form.videos = files
}

const handleVideosRemove = (paths) => {
  form.remove_videos = [...form.remove_videos, ...paths]
}

const handleProjectCreated = (project) => {
  console.log('Nuevo proyecto creado:', project)
}

const handleAgentCreated = (agent) => {
  console.log('Nuevo agente creado:', agent)
}

const submit = () => {
  // Use POST with _method: PATCH for file uploads
  form.transform(data => ({
    ...data,
    _method: 'PATCH'
  })).post(route('properties.update', props.property.id), {
    preserveScroll: true
  })
}
</script>