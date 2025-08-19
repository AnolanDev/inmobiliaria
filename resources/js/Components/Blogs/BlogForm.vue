<template>
  <form @submit.prevent="submit" class="space-y-6">
    <!-- Basic Information -->
    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
      <div class="px-4 py-5 sm:p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Información básica</h3>
        
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
          <!-- Title -->
          <div class="sm:col-span-2">
            <label for="title" class="block text-sm font-medium text-gray-700">
              Título del blog *
            </label>
            <input
              id="title"
              v-model="form.title"
              type="text"
              required
              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
              :class="{ 'border-red-300': form.errors.title }"
              @input="generateSlug"
            />
            <p v-if="form.errors.title" class="mt-1 text-sm text-red-600">{{ form.errors.title }}</p>
          </div>

          <!-- Slug -->
          <div class="sm:col-span-2">
            <label for="slug" class="block text-sm font-medium text-gray-700">
              Slug (URL amigable)
            </label>
            <input
              id="slug"
              v-model="form.slug"
              type="text"
              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
              :class="{ 'border-red-300': form.errors.slug }"
            />
            <p class="mt-1 text-xs text-gray-500">
              Se genera automáticamente desde el título. Solo modifica si necesitas un slug específico.
            </p>
            <p v-if="form.errors.slug" class="mt-1 text-sm text-red-600">{{ form.errors.slug }}</p>
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
              <option value="">Selecciona una categoría</option>
              <option v-for="(label, value) in categories" :key="value" :value="value">
                {{ label }}
              </option>
            </select>
            <p v-if="form.errors.category" class="mt-1 text-sm text-red-600">{{ form.errors.category }}</p>
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

          <!-- Author -->
          <div>
            <label for="author" class="block text-sm font-medium text-gray-700">
              Autor
            </label>
            <input
              id="author"
              v-model="form.author"
              type="text"
              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
              :class="{ 'border-red-300': form.errors.author }"
            />
            <p v-if="form.errors.author" class="mt-1 text-sm text-red-600">{{ form.errors.author }}</p>
          </div>

          <!-- Published At -->
          <div>
            <label for="published_at" class="block text-sm font-medium text-gray-700">
              Fecha de publicación
            </label>
            <input
              id="published_at"
              v-model="form.published_at"
              type="datetime-local"
              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
              :class="{ 'border-red-300': form.errors.published_at }"
            />
            <p class="mt-1 text-xs text-gray-500">
              Si no se especifica, se usará la fecha actual al publicar
            </p>
            <p v-if="form.errors.published_at" class="mt-1 text-sm text-red-600">{{ form.errors.published_at }}</p>
          </div>

          <!-- Public Visibility -->
          <div class="sm:col-span-2">
            <label class="block text-sm font-medium text-gray-700 mb-2">
              Visibilidad
            </label>
            <div class="flex items-center">
              <input
                id="is_public"
                v-model="form.is_public"
                type="checkbox"
                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
              />
              <label for="is_public" class="ml-2 block text-sm text-gray-700">
                Mostrar en sitio web público
              </label>
            </div>
            <p class="mt-1 text-xs text-gray-500">
              Si está activado, el blog será visible en el sitio web público para los visitantes
            </p>
            <p v-if="form.errors.is_public" class="mt-1 text-sm text-red-600">{{ form.errors.is_public }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Content -->
    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
      <div class="px-4 py-5 sm:p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Contenido</h3>
        
        <div class="space-y-6">
          <!-- Excerpt -->
          <div>
            <label for="excerpt" class="block text-sm font-medium text-gray-700">
              Resumen/Extracto
            </label>
            <textarea
              id="excerpt"
              v-model="form.excerpt"
              rows="3"
              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
              :class="{ 'border-red-300': form.errors.excerpt }"
              placeholder="Breve descripción del contenido del blog..."
            ></textarea>
            <p class="mt-1 text-xs text-gray-500">
              Este texto aparecerá como vista previa en listados y redes sociales
            </p>
            <p v-if="form.errors.excerpt" class="mt-1 text-sm text-red-600">{{ form.errors.excerpt }}</p>
          </div>

          <!-- Content -->
          <div>
            <label for="content" class="block text-sm font-medium text-gray-700">
              Contenido *
            </label>
            <textarea
              id="content"
              v-model="form.content"
              rows="15"
              required
              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
              :class="{ 'border-red-300': form.errors.content }"
              placeholder="Escribe el contenido completo del blog aquí..."
            ></textarea>
            <p class="mt-1 text-xs text-gray-500">
              Puedes usar HTML básico para dar formato al texto
            </p>
            <p v-if="form.errors.content" class="mt-1 text-sm text-red-600">{{ form.errors.content }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Tags -->
    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
      <div class="px-4 py-5 sm:p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Etiquetas</h3>
        
        <div>
          <label for="tags_input" class="block text-sm font-medium text-gray-700">
            Etiquetas (separadas por comas)
          </label>
          <input
            id="tags_input"
            v-model="tagsInput"
            type="text"
            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
            placeholder="inmobiliaria, inversión, mercado, propiedades..."
            @input="updateTags"
          />
          <p class="mt-1 text-xs text-gray-500">
            Escribe las etiquetas separadas por comas para ayudar a categorizar el contenido
          </p>
          
          <!-- Tags Display -->
          <div v-if="form.tags && form.tags.length" class="mt-3 flex flex-wrap gap-2">
            <span 
              v-for="(tag, index) in form.tags" 
              :key="index"
              class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800"
            >
              #{{ tag }}
              <button
                type="button"
                @click="removeTag(index)"
                class="ml-2 inline-flex items-center justify-center w-4 h-4 text-blue-600 hover:text-blue-800"
              >
                ×
              </button>
            </span>
          </div>
          
          <p v-if="form.errors.tags" class="mt-1 text-sm text-red-600">{{ form.errors.tags }}</p>
        </div>
      </div>
    </div>

    <!-- Media -->
    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
      <div class="px-4 py-5 sm:p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">Medios</h3>
        
        <div class="space-y-6">
          <!-- Cover Image -->
          <div>
            <label for="cover_image" class="block text-sm font-medium text-gray-700">
              Imagen de portada {{ !isEditing ? '*' : '' }}
            </label>
            <input
              id="cover_image"
              ref="coverImageInput"
              type="file"
              accept="image/*"
              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
              :class="{ 'border-red-300': form.errors.cover_image }"
              @change="handleCoverImageChange"
            />
            <p class="mt-1 text-xs text-gray-500">
              Imagen principal que aparecerá en listados y como portada del artículo
            </p>
            
            <!-- Current Cover Image Preview -->
            <div v-if="currentCoverImage" class="mt-3">
              <p class="text-sm font-medium text-gray-700 mb-2">Imagen actual:</p>
              <img
                :src="currentCoverImage"
                alt="Cover preview"
                class="w-32 h-20 object-cover rounded-lg border border-gray-300"
              />
            </div>
            
            <p v-if="form.errors.cover_image" class="mt-1 text-sm text-red-600">{{ form.errors.cover_image }}</p>
          </div>

          <!-- Gallery -->
          <div>
            <label for="gallery" class="block text-sm font-medium text-gray-700">
              Galería de imágenes (opcional)
            </label>
            <input
              id="gallery"
              ref="galleryInput"
              type="file"
              accept="image/*"
              multiple
              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
              :class="{ 'border-red-300': form.errors.gallery }"
              @change="handleGalleryChange"
            />
            <p class="mt-1 text-xs text-gray-500">
              Selecciona múltiples imágenes para crear una galería al final del artículo
            </p>
            
            <!-- Current Gallery Preview -->
            <div v-if="currentGallery && currentGallery.length" class="mt-3">
              <p class="text-sm font-medium text-gray-700 mb-2">Galería actual:</p>
              <div class="flex flex-wrap gap-2">
                <img
                  v-for="(image, index) in currentGallery.slice(0, 6)"
                  :key="index"
                  :src="image"
                  alt="Gallery preview"
                  class="w-16 h-16 object-cover rounded-lg border border-gray-300"
                />
                <div v-if="currentGallery.length > 6" class="w-16 h-16 rounded-lg border border-gray-300 bg-gray-100 flex items-center justify-center text-xs text-gray-500">
                  +{{ currentGallery.length - 6 }}
                </div>
              </div>
            </div>
            
            <p v-if="form.errors.gallery" class="mt-1 text-sm text-red-600">{{ form.errors.gallery }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- SEO -->
    <div class="bg-white shadow-sm rounded-lg overflow-hidden">
      <div class="px-4 py-5 sm:p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">SEO y Metadatos</h3>
        
        <div class="space-y-6">
          <!-- Meta Title -->
          <div>
            <label for="meta_title" class="block text-sm font-medium text-gray-700">
              Título SEO
            </label>
            <input
              id="meta_title"
              v-model="form.meta_title"
              type="text"
              maxlength="60"
              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
              :class="{ 'border-red-300': form.errors.meta_title }"
              placeholder="Título optimizado para motores de búsqueda..."
            />
            <p class="mt-1 text-xs text-gray-500">
              Título que aparecerá en los resultados de Google (máximo 60 caracteres)
            </p>
            <p v-if="form.errors.meta_title" class="mt-1 text-sm text-red-600">{{ form.errors.meta_title }}</p>
          </div>

          <!-- Meta Description -->
          <div>
            <label for="meta_description" class="block text-sm font-medium text-gray-700">
              Descripción SEO
            </label>
            <textarea
              id="meta_description"
              v-model="form.meta_description"
              rows="3"
              maxlength="160"
              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
              :class="{ 'border-red-300': form.errors.meta_description }"
              placeholder="Descripción que aparecerá en los resultados de búsqueda..."
            ></textarea>
            <p class="mt-1 text-xs text-gray-500">
              Descripción que aparecerá en los resultados de Google (máximo 160 caracteres)
            </p>
            <p v-if="form.errors.meta_description" class="mt-1 text-sm text-red-600">{{ form.errors.meta_description }}</p>
          </div>

          <!-- Meta Keywords -->
          <div>
            <label for="meta_keywords_input" class="block text-sm font-medium text-gray-700">
              Palabras clave SEO (separadas por comas)
            </label>
            <input
              id="meta_keywords_input"
              v-model="metaKeywordsInput"
              type="text"
              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
              placeholder="inmobiliaria, propiedades, inversión, mercado..."
              @input="updateMetaKeywords"
            />
            <p class="mt-1 text-xs text-gray-500">
              Palabras clave relevantes para ayudar en el posicionamiento SEO
            </p>
            
            <!-- Meta Keywords Display -->
            <div v-if="form.meta_keywords && form.meta_keywords.length" class="mt-3 flex flex-wrap gap-2">
              <span 
                v-for="(keyword, index) in form.meta_keywords" 
                :key="index"
                class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800"
              >
                {{ keyword }}
                <button
                  type="button"
                  @click="removeMetaKeyword(index)"
                  class="ml-2 inline-flex items-center justify-center w-4 h-4 text-green-600 hover:text-green-800"
                >
                  ×
                </button>
              </span>
            </div>
            
            <p v-if="form.errors.meta_keywords" class="mt-1 text-sm text-red-600">{{ form.errors.meta_keywords }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Form Actions -->
    <div class="flex items-center justify-end space-x-4 pt-6">
      <Link
        :href="route('blogs.index')"
        class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
      >
        Cancelar
      </Link>
      
      <button
        type="submit"
        :disabled="form.processing"
        class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50 disabled:cursor-not-allowed"
      >
        <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        {{ form.processing ? 'Guardando...' : (isEditing ? 'Actualizar Blog' : 'Crear Blog') }}
      </button>
    </div>
  </form>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useForm, Link } from '@inertiajs/vue3'

const props = defineProps({
  blog: {
    type: Object,
    default: null
  },
  categories: {
    type: Object,
    required: true
  },
  statuses: {
    type: Object,
    required: true
  },
  isEditing: {
    type: Boolean,
    default: false
  }
})

// State
const coverImageInput = ref(null)
const galleryInput = ref(null)
const tagsInput = ref('')
const metaKeywordsInput = ref('')

// Helper function for date formatting
const formatDateForInput = (dateString) => {
  if (!dateString) return ''
  const date = new Date(dateString)
  return date.toISOString().slice(0, 16)
}

// Form
const form = useForm({
  title: props.blog?.title || '',
  slug: props.blog?.slug || '',
  excerpt: props.blog?.excerpt || '',
  content: props.blog?.content || '',
  cover_image: null,
  gallery: [],
  author: props.blog?.author || '',
  category: props.blog?.category || '',
  tags: props.blog?.tags || [],
  status: props.blog?.status || 'draft',
  is_public: props.blog?.is_public || false,
  sort_order: props.blog?.sort_order || 0,
  published_at: props.blog?.published_at ? formatDateForInput(props.blog.published_at) : '',
  meta_title: props.blog?.meta_title || '',
  meta_description: props.blog?.meta_description || '',
  meta_keywords: props.blog?.meta_keywords || [],
  _method: props.isEditing ? 'PATCH' : undefined,
})

// Computed
const currentCoverImage = computed(() => {
  return props.blog?.cover_image_url || null
})

const currentGallery = computed(() => {
  return props.blog?.gallery_urls || []
})

// Methods
const generateSlug = () => {
  if (!props.isEditing && form.title) {
    form.slug = form.title
      .toLowerCase()
      .replace(/[^a-z0-9\s-]/g, '')
      .replace(/\s+/g, '-')
      .replace(/-+/g, '-')
      .trim('-')
  }
}

const updateTags = () => {
  form.tags = tagsInput.value
    .split(',')
    .map(tag => tag.trim())
    .filter(tag => tag.length > 0)
}

const removeTag = (index) => {
  form.tags.splice(index, 1)
  tagsInput.value = form.tags.join(', ')
}

const updateMetaKeywords = () => {
  form.meta_keywords = metaKeywordsInput.value
    .split(',')
    .map(keyword => keyword.trim())
    .filter(keyword => keyword.length > 0)
}

const removeMetaKeyword = (index) => {
  form.meta_keywords.splice(index, 1)
  metaKeywordsInput.value = form.meta_keywords.join(', ')
}

const handleCoverImageChange = (event) => {
  const file = event.target.files[0]
  if (file) {
    form.cover_image = file
  }
}

const handleGalleryChange = (event) => {
  const files = Array.from(event.target.files)
  form.gallery = files
}

const submit = () => {
  if (props.isEditing) {
    form.post(route('blogs.update', props.blog.id), {
      onSuccess: () => {
        // Redirect will be handled by controller
      }
    })
  } else {
    form.post(route('blogs.store'), {
      onSuccess: () => {
        // Redirect will be handled by controller
      }
    })
  }
}

// Initialize
onMounted(() => {
  if (props.blog?.tags && props.blog.tags.length) {
    tagsInput.value = props.blog.tags.join(', ')
  }
  
  if (props.blog?.meta_keywords && props.blog.meta_keywords.length) {
    metaKeywordsInput.value = props.blog.meta_keywords.join(', ')
  }
})
</script>