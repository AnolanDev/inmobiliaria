<template>
  <form @submit.prevent="submit" class="space-y-6">
    <!-- Basic Information -->
    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
      <div class="px-4 py-5 sm:p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Información básica</h3>
        
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
          <!-- Name -->
          <div class="sm:col-span-2">
            <label for="name" class="block text-sm font-medium text-gray-700">
              Nombre del proyecto *
            </label>
            <input
              id="name"
              v-model="form.name"
              type="text"
              required
              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
              :class="{ 'border-red-300': form.errors.name }"
            />
            <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
          </div>

          <!-- Type -->
          <div>
            <label for="type" class="block text-sm font-medium text-gray-700">
              Tipo de proyecto *
            </label>
            <select
              id="type"
              v-model="form.type"
              required
              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
              :class="{ 'border-red-300': form.errors.type }"
            >
              <option value="">Selecciona un tipo</option>
              <option v-for="(label, value) in types" :key="value" :value="value">
                {{ label }}
              </option>
            </select>
            <p v-if="form.errors.type" class="mt-1 text-sm text-red-600">{{ form.errors.type }}</p>
          </div>

          <!-- Status -->
          <div>
            <label for="status" class="block text-sm font-medium text-gray-700">
              Estado *
            </label>
            <select
              id="status"
              v-model="form.status"
              required
              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
              :class="{ 'border-red-300': form.errors.status }"
            >
              <option value="">Selecciona un estado</option>
              <option v-for="(label, value) in statuses" :key="value" :value="value">
                {{ label }}
              </option>
            </select>
            <p v-if="form.errors.status" class="mt-1 text-sm text-red-600">{{ form.errors.status }}</p>
          </div>

          <!-- Property Count -->
          <div class="sm:col-span-2">
            <label for="property_count" class="block text-sm font-medium text-gray-700">
              Número de propiedades (opcional)
            </label>
            <input
              id="property_count"
              v-model.number="form.property_count"
              type="number"
              min="0"
              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
              :class="{ 'border-red-300': form.errors.property_count }"
            />
            <p v-if="form.errors.property_count" class="mt-1 text-sm text-red-600">{{ form.errors.property_count }}</p>
          </div>

          <!-- Description -->
          <div class="sm:col-span-2">
            <label for="description" class="block text-sm font-medium text-gray-700">
              Descripción
            </label>
            <textarea
              id="description"
              v-model="form.description"
              rows="4"
              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
              :class="{ 'border-red-300': form.errors.description }"
              placeholder="Describe el proyecto..."
            ></textarea>
            <p v-if="form.errors.description" class="mt-1 text-sm text-red-600">{{ form.errors.description }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Media Upload -->
    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
      <div class="px-4 py-5 sm:p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Multimedia</h3>

        <!-- Cover Image -->
        <div class="mb-6">
          <FileUploader
            id="cover_image"
            label="Imagen de portada"
            accept="image/jpeg,image/png,image/jpg,image/gif,image/webp"
            :required="!isEdit"
            :existing-files="existingCoverImage"
            drag-text="Arrastra la imagen de portada aquí"
            help-text="JPG, PNG, GIF hasta 5MB. Recomendado: 1200x600px"
            @files-changed="handleCoverImageChange"
            @files-removed="handleCoverImageRemove"
          />
          <p v-if="form.errors.cover_image" class="mt-1 text-sm text-red-600">{{ form.errors.cover_image }}</p>
        </div>

        <!-- Gallery Images -->
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
        :href="route('projects.index')"
        class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
      >
        Cancelar
      </Link>
      <button
        type="submit"
        :disabled="form.processing"
        class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50"
      >
        <svg v-if="form.processing" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        {{ form.processing ? 'Guardando...' : (isEdit ? 'Actualizar' : 'Crear proyecto') }}
      </button>
    </div>
  </form>
</template>

<script setup>
import { computed, watch } from 'vue'
import { Link, useForm, router } from '@inertiajs/vue3'
import FileUploader from '@/Components/FileUploader.vue'

const props = defineProps({
  project: {
    type: Object,
    default: null
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

// Computed
const isEdit = computed(() => !!props.project)

const existingCoverImage = computed(() => {
  if (!props.project?.cover_image_url) return []
  return [{
    url: props.project.cover_image_url,
    path: props.project.cover_image || '',
    name: 'cover_image.jpg'
  }]
})

const existingGallery = computed(() => {
  if (!props.project?.gallery_urls || !Array.isArray(props.project.gallery_urls)) return []
  
  return props.project.gallery_urls.map((url, index) => ({
    url,
    path: (props.project.gallery && props.project.gallery[index]) || url,
    name: `gallery_${index}.jpg`
  }))
})

const existingVideos = computed(() => {
  if (!props.project?.videos || !Array.isArray(props.project.videos)) return []
  
  return props.project.videos.map((path, index) => {
    // Ensure path is a string
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
  name: props.project?.name || '',
  description: props.project?.description || '',
  type: props.project?.type || '',
  status: props.project?.status || 'Disponible',
  property_count: props.project?.property_count || 0,
  cover_image: null,
  gallery: [],
  videos: [],
  remove_gallery: [],
  remove_videos: []
})

// Methods
const handleCoverImageChange = (files) => {
  form.cover_image = files[0] || null
}

const handleCoverImageRemove = (paths) => {
  // For cover image, we just clear it
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

const submit = () => {
  if (isEdit.value) {
    // Usar POST con _method: PUT para formularios con archivos
    form.transform(data => ({
      ...data,
      _method: 'PUT'
    })).post(route('projects.update', props.project.id), {
      preserveScroll: true
    })
  } else {
    form.post(route('projects.store'), {
      preserveScroll: true
    })
  }
}
</script>