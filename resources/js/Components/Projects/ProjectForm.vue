<template>
  <form @submit.prevent="submit" class="space-y-6">
    <!-- Form Instructions -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
      <div class="flex">
        <div class="flex-shrink-0">
          <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
          </svg>
        </div>
        <div class="ml-3">
          <h3 class="text-sm font-medium text-blue-800">
            Información del formulario
          </h3>
          <div class="mt-2 text-sm text-blue-700">
            <p><strong>Campos requeridos (*):</strong> Nombre, tipo, estado, ciudad y departamento</p>
            <p><strong>Campos opcionales:</strong> Descripción, número de propiedades, imagen de portada, galería y videos</p>
          </div>
        </div>
      </div>
    </div>

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
              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
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
              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
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
              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
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
          <div>
            <label for="property_count" class="block text-sm font-medium text-gray-700">
              Número de propiedades (opcional)
            </label>
            <input
              id="property_count"
              v-model.number="form.property_count"
              type="number"
              min="0"
              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
              :class="{ 'border-red-300': form.errors.property_count }"
            />
            <p v-if="form.errors.property_count" class="mt-1 text-sm text-red-600">{{ form.errors.property_count }}</p>
          </div>

          <!-- Public Visibility -->
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Visibilidad
            </label>
            <div class="flex items-center">
              <input
                id="is_public"
                v-model="form.is_public"
                type="checkbox"
                class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded"
              />
              <label for="is_public" class="ml-2 block text-sm text-gray-700">
                Mostrar en sitio web público
              </label>
            </div>
            <p class="mt-1 text-xs text-gray-500">
              Si está activado, el proyecto será visible en el sitio web público para los visitantes
            </p>
            <p v-if="form.errors.is_public" class="mt-1 text-sm text-red-600">{{ form.errors.is_public }}</p>
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
              placeholder="Ej: Medellín, Bogotá, Cartagena"
            />
            <p v-if="form.errors.city" class="mt-1 text-sm text-red-600">{{ form.errors.city }}</p>
          </div>

          <!-- State -->
          <div>
            <label for="state" class="block text-sm font-medium text-gray-700">
              Departamento *
            </label>
            <input
              id="state"
              v-model="form.state"
              type="text"
              required
              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
              :class="{ 'border-red-300': form.errors.state }"
              placeholder="Ej: Antioquia, Cundinamarca, Bolívar"
            />
            <p v-if="form.errors.state" class="mt-1 text-sm text-red-600">{{ form.errors.state }}</p>
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
              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
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
            label="Imagen de portada (opcional)"
            accept="image/jpeg,image/png,image/jpg,image/gif,image/webp"
            :required="false"
            :existing-files="existingCoverImage"
            drag-text="Arrastra la imagen de portada aquí"
            help-text="JPG, PNG, GIF hasta 5MB. Recomendado: 1200x600px. Si no subes una imagen, se mostrará un placeholder automático."
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
        class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
      >
        Cancelar
      </Link>
      <button
        type="submit"
        :disabled="form.processing || !isFormValid"
        class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors"
        :class="[
          form.processing || !isFormValid 
            ? 'bg-gray-400 cursor-not-allowed' 
            : 'bg-green-600 hover:bg-green-700'
        ]"
      >
        <svg v-if="form.processing" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        {{ getSubmitButtonText() }}
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
    path: props.project.cover_image_url, // Use the URL directly since it's already a complete URL
    name: 'cover_image.jpg'
  }]
})

const existingGallery = computed(() => {
  if (!props.project?.gallery_urls || !Array.isArray(props.project.gallery_urls)) return []
  
  return props.project.gallery_urls.map((imageSet, index) => {
    // Handle responsive image format from gallery_urls
    let url = imageSet
    if (typeof imageSet === 'object' && imageSet !== null) {
      if (imageSet.medium?.url) {
        url = imageSet.medium.url
      } else if (imageSet.thumbnail?.url) {
        url = imageSet.thumbnail.url
      } else if (imageSet.original?.url) {
        url = imageSet.original.url
      } else if (typeof imageSet === 'string') {
        url = imageSet
      } else {
        // If it's an object but doesn't have expected structure, try to extract URL
        url = imageSet.url || imageSet.path || ''
      }
    }
    
    return {
      url: typeof url === 'string' ? url : '',
      path: typeof url === 'string' ? url : '', // Use the URL directly since it's already complete
      name: `gallery_${index}.jpg`
    }
  }).filter(item => item.url) // Filter out items without valid URLs
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

// Form validation
const isFormValid = computed(() => {
  return form.name && 
         form.type && 
         form.status && 
         form.city && 
         form.state
})

// Form
const form = useForm({
  name: props.project?.name || '',
  description: props.project?.description || '',
  type: props.project?.type || '',
  status: props.project?.status || 'Disponible',
  property_count: props.project?.property_count || 0,
  is_public: props.project?.is_public === 1 || props.project?.is_public === true || false,
  city: props.project?.city || '',
  state: props.project?.state || '',
  cover_image: null,
  gallery: [],
  videos: [],
  remove_gallery: [],
  remove_videos: []
})

// Methods
const handleCoverImageChange = (files) => {
  const file = files[0] || null
  form.cover_image = file && Object.keys(file).length > 0 ? file : null
}

const handleCoverImageRemove = (paths) => {
  // For cover image, we just clear it
  form.cover_image = null
}

const handleGalleryChange = (files) => {
  form.gallery = files.filter(file => file && Object.keys(file).length > 0)
}

const handleGalleryRemove = (paths) => {
  form.remove_gallery = [...form.remove_gallery, ...paths]
}

const handleVideosChange = (files) => {
  form.videos = files.filter(file => file && Object.keys(file).length > 0)
}

const handleVideosRemove = (paths) => {
  form.remove_videos = [...form.remove_videos, ...paths]
}

const getSubmitButtonText = () => {
  if (form.processing) {
    return isEdit.value ? 'Actualizando...' : 'Creando...'
  }
  if (!isFormValid.value) {
    return 'Complete los campos requeridos'
  }
  return isEdit.value ? 'Actualizar proyecto' : 'Crear proyecto'
}

const submit = () => {
  const formData = form.data()
  console.log('Form data before submit (complete):', JSON.stringify(formData, null, 2))
  console.log('Required fields check:')
  console.log('- name:', formData.name)
  console.log('- type:', formData.type) 
  console.log('- status:', formData.status)
  console.log('- city:', formData.city)
  console.log('- state:', formData.state)
  console.log('CSRF Token from meta:', document.querySelector('meta[name="csrf-token"]')?.content)
  console.log('Axios CSRF header:', window.axios.defaults.headers.common['X-CSRF-TOKEN'])
  
  // Validate required fields
  if (!isFormValid.value) {
    alert('Por favor completa todos los campos requeridos: Nombre, Tipo, Estado, Ciudad y Departamento')
    return
  }
  
  if (isEdit.value) {
    // Usar POST con _method: PATCH para formularios con archivos
    form.transform(data => ({
      ...data,
      _method: 'PATCH',
      cover_image: data.cover_image && Object.keys(data.cover_image).length > 0 ? data.cover_image : null,
      gallery: data.gallery?.filter(item => item && Object.keys(item).length > 0) || [],
      videos: data.videos?.filter(item => item && Object.keys(item).length > 0) || []
    })).post(route('projects.update', props.project.id), {
      preserveScroll: true,
      onError: (errors) => {
        console.error('Validation errors:', errors)
        alert('Errores de validación: ' + JSON.stringify(errors))
      },
      onSuccess: () => {
        console.log('Project updated successfully')
      }
    })
  } else {
    form.transform(data => ({
      ...data,
      cover_image: data.cover_image && Object.keys(data.cover_image).length > 0 ? data.cover_image : null,
      gallery: data.gallery?.filter(item => item && Object.keys(item).length > 0) || [],
      videos: data.videos?.filter(item => item && Object.keys(item).length > 0) || []
    })).post(route('projects.store'), {
      preserveScroll: true,
      onError: (errors) => {
        console.error('Validation errors:', errors)
        alert('Errores de validación: ' + JSON.stringify(errors))
      },
      onSuccess: () => {
        console.log('Project created successfully')
      }
    })
  }
}
</script>