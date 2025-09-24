<template>
  <Head title="Nuevo Agente" />

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
              Nuevo Agente
            </h2>
            <p class="text-gray-500 text-sm mt-1">
              Agrega un nuevo agente a tu equipo
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
                      class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 rounded"
                    />
                    <label for="is_public" class="ml-2 block text-sm text-gray-700">
                      Mostrar en sitio web público
                    </label>
                  </div>
                  <p class="mt-1 text-xs text-gray-500">
                    Si está activado, el agente será visible en el sitio web público para los visitantes
                  </p>
                  <p v-if="form.errors.is_public" class="mt-1 text-sm text-red-600">{{ form.errors.is_public }}</p>
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
                  label="Foto de perfil *"
                  accept="image/jpeg,image/png,image/jpg,image/gif,image/webp"
                  :required="true"
                  drag-text="Arrastra la foto de perfil aquí"
                  help-text="JPG, PNG, GIF hasta 5MB. Recomendado: 400x400px"
                  @files-changed="handleProfilePictureChange"
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

          <!-- Form Actions -->
          <div class="flex justify-end space-x-3">
            <Link
              :href="route('agents.index')"
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
              {{ form.processing ? 'Creando...' : 'Crear agente' }}
            </button>
          </div>
        </form>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import FileUploader from '@/Components/FileUploader.vue'

// Form
const form = useForm({
  name: '',
  email: '',
  phone: '',
  type: '',
  bio: '',
  facebook: '',
  instagram: '',
  linkedin: '',
  is_active: true,
  is_public: false,
  profile_picture: null,
  gallery: [],
  videos: []
})

// Handle file uploads
const handleProfilePictureChange = (files) => {
  form.profile_picture = files[0] || null
}

const handleGalleryChange = (files) => {
  form.gallery = files
}

const handleVideosChange = (files) => {
  form.videos = files
}

const submit = () => {
  form.post(route('agents.store'), {
    preserveScroll: true
  })
}
</script>