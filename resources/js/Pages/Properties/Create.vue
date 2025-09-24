<template>
  <Head title="Nueva Propiedad" />

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
              Nueva Propiedad
            </h2>
            <p class="text-gray-500 text-sm mt-1">
              Agrega una nueva propiedad a tu cartera
            </p>
          </div>
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
                    Si está activado, la propiedad será visible en el sitio web público para los visitantes
                  </p>
                  <p v-if="form.errors.is_public" class="mt-1 text-sm text-red-600">{{ form.errors.is_public }}</p>
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
                  label="Imagen de portada *"
                  accept="image/jpeg,image/png,image/jpg,image/gif,image/webp"
                  :required="true"
                  drag-text="Arrastra la imagen de portada aquí"
                  help-text="JPG, PNG, GIF hasta 5MB. Recomendado: 1200x600px"
                  @files-changed="handleCoverImageChange"
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
                  drag-text="Arrastra imágenes adicionales aquí"
                  help-text="JPG, PNG, GIF hasta 5MB cada una. Máximo 10 imágenes"
                  @files-changed="handleGalleryChange"
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
                  drag-text="Arrastra videos aquí"
                  help-text="MP4, MOV, AVI, WMV, WEBM hasta 100MB cada uno. Máximo 5 videos"
                  @files-changed="handleVideosChange"
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
              :href="route('properties.index')"
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
              {{ form.processing ? 'Creando...' : 'Crear propiedad' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { computed, onMounted } from 'vue'
import { Head, Link, useForm, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import FileUploader from '@/Components/FileUploader.vue'
import ProjectSelect from '@/Components/ProjectSelect.vue'
import AgentSelect from '@/Components/AgentSelect.vue'

const props = defineProps({
  agents: {
    type: Array,
    required: true
  },
  projects: {
    type: Array,
    required: true
  },
  preselectedProject: {
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

// Form
const form = useForm({
  title: '',
  description: '',
  price: '',
  type: '',
  category: '',
  address: '',
  city: '',
  state: '',
  zip_code: '',
  bedrooms: 0,
  bathrooms: 0,
  area: '',
  agent_id: '',
  project_id: props.preselectedProject?.id || '',
  status: 'available',
  is_public: false,
  cover_image: null,
  gallery: [],
  videos: []
})

// Handle file uploads
const handleCoverImageChange = (files) => {
  form.cover_image = files[0] || null
}

const handleGalleryChange = (files) => {
  form.gallery = files
}

const handleVideosChange = (files) => {
  form.videos = files
}

const handleProjectCreated = (project) => {
  // Project is already selected by ProjectSelect component
  console.log('Nuevo proyecto creado:', project)
}

const handleAgentCreated = (agent) => {
  // Agent is already selected by AgentSelect component
  console.log('Nuevo agente creado:', agent)
}

const submit = () => {
  // Use POST with multipart/form-data for file uploads
  form.post(route('properties.store'), {
    preserveScroll: true
  })
}

// Initialize form with preselected project if available
onMounted(() => {
  if (props.preselectedProject) {
    form.project_id = props.preselectedProject.id
  }
})
</script>