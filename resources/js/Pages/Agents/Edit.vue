<template>
  <Head :title="`Editar: ${agent.name}`" />

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
              Editar Agente
            </h2>
            <p class="text-gray-500 text-sm mt-1">
              {{ agent.name }}
            </p>
          </div>
        </div>
        <div class="flex space-x-2">
          <Link
            :href="route('agents.show', agent.id)"
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
                <!-- Name -->
                <div class="sm:col-span-2">
                  <label for="name" class="block text-sm font-medium text-gray-700">
                    Nombre completo *
                  </label>
                  <input
                    id="name"
                    v-model="form.name"
                    type="text"
                    required
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                    :class="{ 'border-red-300': form.errors.name }"
                    placeholder="Ej: Juan Carlos Rodríguez"
                  />
                  <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
                </div>

                <!-- Email -->
                <div>
                  <label for="email" class="block text-sm font-medium text-gray-700">
                    Correo electrónico *
                  </label>
                  <input
                    id="email"
                    v-model="form.email"
                    type="email"
                    required
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                    :class="{ 'border-red-300': form.errors.email }"
                    placeholder="Ej: juan.rodriguez@inmobiliaria.com"
                  />
                  <p v-if="form.errors.email" class="mt-1 text-sm text-red-600">{{ form.errors.email }}</p>
                </div>

                <!-- Phone -->
                <div>
                  <label for="phone" class="block text-sm font-medium text-gray-700">
                    Teléfono *
                  </label>
                  <input
                    id="phone"
                    v-model="form.phone"
                    type="tel"
                    required
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                    :class="{ 'border-red-300': form.errors.phone }"
                    placeholder="Ej: +57 300 123 4567"
                  />
                  <p v-if="form.errors.phone" class="mt-1 text-sm text-red-600">{{ form.errors.phone }}</p>
                </div>

                <!-- Type -->
                <div>
                  <label for="type" class="block text-sm font-medium text-gray-700">
                    Tipo de agente *
                  </label>
                  <select
                    id="type"
                    v-model="form.type"
                    required
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                    :class="{ 'border-red-300': form.errors.type }"
                  >
                    <option value="">Selecciona el tipo</option>
                    <option value="Interno">Interno</option>
                    <option value="Externo">Externo</option>
                  </select>
                  <p v-if="form.errors.type" class="mt-1 text-sm text-red-600">{{ form.errors.type }}</p>
                </div>

                <!-- Status -->
                <div>
                  <label for="is_active" class="block text-sm font-medium text-gray-700">
                    Estado
                  </label>
                  <select
                    id="is_active"
                    v-model="form.is_active"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                    :class="{ 'border-red-300': form.errors.is_active }"
                  >
                    <option :value="true">Activo</option>
                    <option :value="false">Inactivo</option>
                  </select>
                  <p v-if="form.errors.is_active" class="mt-1 text-sm text-red-600">{{ form.errors.is_active }}</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Biografía -->
          <div class="bg-white shadow-sm rounded-lg overflow-hidden mb-6">
            <div class="px-4 py-5 sm:p-6">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Biografía</h3>
              
              <div>
                <label for="bio" class="block text-sm font-medium text-gray-700">
                  Descripción corta
                </label>
                <textarea
                  id="bio"
                  v-model="form.bio"
                  rows="4"
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                  :class="{ 'border-red-300': form.errors.bio }"
                  placeholder="Describe la experiencia y especialidades del agente..."
                />
                <p v-if="form.errors.bio" class="mt-1 text-sm text-red-600">{{ form.errors.bio }}</p>
              </div>
            </div>
          </div>

          <!-- Redes Sociales -->
          <div class="bg-white shadow-sm rounded-lg overflow-hidden mb-6">
            <div class="px-4 py-5 sm:p-6">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Redes sociales</h3>
              
              <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                <!-- Facebook -->
                <div>
                  <label for="facebook" class="block text-sm font-medium text-gray-700">
                    Facebook
                  </label>
                  <input
                    id="facebook"
                    v-model="form.facebook"
                    type="url"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                    :class="{ 'border-red-300': form.errors.facebook }"
                    placeholder="https://facebook.com/usuario"
                  />
                  <p v-if="form.errors.facebook" class="mt-1 text-sm text-red-600">{{ form.errors.facebook }}</p>
                </div>

                <!-- Instagram -->
                <div>
                  <label for="instagram" class="block text-sm font-medium text-gray-700">
                    Instagram
                  </label>
                  <input
                    id="instagram"
                    v-model="form.instagram"
                    type="url"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                    :class="{ 'border-red-300': form.errors.instagram }"
                    placeholder="https://instagram.com/usuario"
                  />
                  <p v-if="form.errors.instagram" class="mt-1 text-sm text-red-600">{{ form.errors.instagram }}</p>
                </div>

                <!-- LinkedIn -->
                <div>
                  <label for="linkedin" class="block text-sm font-medium text-gray-700">
                    LinkedIn
                  </label>
                  <input
                    id="linkedin"
                    v-model="form.linkedin"
                    type="url"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                    :class="{ 'border-red-300': form.errors.linkedin }"
                    placeholder="https://linkedin.com/in/usuario"
                  />
                  <p v-if="form.errors.linkedin" class="mt-1 text-sm text-red-600">{{ form.errors.linkedin }}</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Multimedia -->
          <div class="bg-white shadow-sm rounded-lg overflow-hidden mb-6">
            <div class="px-4 py-5 sm:p-6">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Multimedia</h3>

              <!-- Profile Picture -->
              <div class="mb-6">
                <FileUploader
                  id="profile_picture"
                  label="Foto de perfil"
                  accept="image/jpeg,image/png,image/jpg,image/gif,image/webp"
                  :existing-files="existingProfilePicture"
                  drag-text="Arrastra la nueva foto de perfil aquí o reemplaza la existente"
                  help-text="JPG, PNG, GIF hasta 5MB. Recomendado: 400x400px"
                  @files-changed="handleProfilePictureChange"
                  @files-removed="handleProfilePictureRemove"
                />
                <p v-if="form.errors.profile_picture" class="mt-1 text-sm text-red-600">{{ form.errors.profile_picture }}</p>
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

          <!-- Form Actions -->
          <div class="flex justify-end space-x-3">
            <Link
              :href="route('agents.show', agent.id)"
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

const props = defineProps({
  agent: {
    type: Object,
    required: true
  }
})

// Computed properties for existing media
const existingProfilePicture = computed(() => {
  if (!props.agent?.profile_picture_url) return []
  return [{
    url: props.agent.profile_picture_url,
    path: props.agent.profile_picture || '',
    name: 'profile_picture.jpg'
  }]
})

const existingGallery = computed(() => {
  if (!props.agent?.gallery_urls || !Array.isArray(props.agent.gallery_urls)) return []
  
  return props.agent.gallery_urls.map((url, index) => ({
    url,
    path: (props.agent.gallery && props.agent.gallery[index]) || url,
    name: `gallery_${index}.jpg`
  }))
})

const existingVideos = computed(() => {
  if (!props.agent?.videos || !Array.isArray(props.agent.videos)) return []
  
  return props.agent.videos.map((path, index) => {
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
  name: props.agent.name || '',
  email: props.agent.email || '',
  phone: props.agent.phone || '',
  type: props.agent.type || '',
  bio: props.agent.bio || '',
  facebook: props.agent.facebook || '',
  instagram: props.agent.instagram || '',
  linkedin: props.agent.linkedin || '',
  is_active: props.agent.is_active ?? true,
  profile_picture: null,
  gallery: [],
  videos: [],
  remove_gallery: [],
  remove_videos: []
})

// Handle file uploads
const handleProfilePictureChange = (files) => {
  form.profile_picture = files[0] || null
}

const handleProfilePictureRemove = (paths) => {
  form.profile_picture = null
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

const submit = () => {
  // Use POST with _method: PATCH for file uploads
  form.transform(data => ({
    ...data,
    _method: 'PATCH'
  })).post(route('agents.update', props.agent.id), {
    preserveScroll: true
  })
}
</script>