<template>
  <AuthenticatedLayout title="Editar Cliente">
    <div class="py-12">
      <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
          <div class="flex items-center gap-4 mb-4">
            <Link
              :href="route('clients.index')"
              class="text-gray-500 hover:text-gray-700"
            >
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
              </svg>
            </Link>
            <h1 class="text-3xl font-bold text-gray-900">Editar Cliente</h1>
          </div>
          <p class="text-gray-600">Actualiza la información de {{ client.name }}</p>
        </div>

        <form @submit.prevent="submit" class="space-y-8">
          <!-- Basic Information -->
          <div class="bg-white shadow-sm rounded-lg overflow-hidden">
            <div class="px-4 py-5 sm:p-6">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Información Básica</h3>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Name -->
                <div class="md:col-span-2">
                  <label for="name" class="block text-sm font-medium text-gray-700">
                    Nombre completo *
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

                <!-- Document Type -->
                <div>
                  <label for="document_type" class="block text-sm font-medium text-gray-700">
                    Tipo de documento *
                  </label>
                  <select
                    id="document_type"
                    v-model="form.document_type"
                    required
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                    :class="{ 'border-red-300': form.errors.document_type }"
                  >
                    <option v-for="(label, value) in documentTypes" :key="value" :value="value">
                      {{ label }}
                    </option>
                  </select>
                  <p v-if="form.errors.document_type" class="mt-1 text-sm text-red-600">{{ form.errors.document_type }}</p>
                </div>

                <!-- Document Number -->
                <div>
                  <label for="document_number" class="block text-sm font-medium text-gray-700">
                    Número de documento
                  </label>
                  <input
                    id="document_number"
                    v-model="form.document_number"
                    type="text"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                    :class="{ 'border-red-300': form.errors.document_number }"
                  />
                  <p v-if="form.errors.document_number" class="mt-1 text-sm text-red-600">{{ form.errors.document_number }}</p>
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
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                    :class="{ 'border-red-300': form.errors.email }"
                  />
                  <p v-if="form.errors.email" class="mt-1 text-sm text-red-600">{{ form.errors.email }}</p>
                </div>

                <!-- Phone -->
                <div>
                  <label for="phone" class="block text-sm font-medium text-gray-700">
                    Teléfono principal
                  </label>
                  <input
                    id="phone"
                    v-model="form.phone"
                    type="tel"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                    :class="{ 'border-red-300': form.errors.phone }"
                  />
                  <p v-if="form.errors.phone" class="mt-1 text-sm text-red-600">{{ form.errors.phone }}</p>
                </div>

                <!-- Secondary Phone -->
                <div>
                  <label for="secondary_phone" class="block text-sm font-medium text-gray-700">
                    Teléfono secundario
                  </label>
                  <input
                    id="secondary_phone"
                    v-model="form.secondary_phone"
                    type="tel"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                    :class="{ 'border-red-300': form.errors.secondary_phone }"
                  />
                  <p v-if="form.errors.secondary_phone" class="mt-1 text-sm text-red-600">{{ form.errors.secondary_phone }}</p>
                </div>

                <!-- Birth Date -->
                <div>
                  <label for="birth_date" class="block text-sm font-medium text-gray-700">
                    Fecha de nacimiento
                  </label>
                  <input
                    id="birth_date"
                    v-model="form.birth_date"
                    type="date"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                    :class="{ 'border-red-300': form.errors.birth_date }"
                  />
                  <p v-if="form.errors.birth_date" class="mt-1 text-sm text-red-600">{{ form.errors.birth_date }}</p>
                </div>

                <!-- Occupation -->
                <div>
                  <label for="occupation" class="block text-sm font-medium text-gray-700">
                    Ocupación
                  </label>
                  <input
                    id="occupation"
                    v-model="form.occupation"
                    type="text"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                    :class="{ 'border-red-300': form.errors.occupation }"
                  />
                  <p v-if="form.errors.occupation" class="mt-1 text-sm text-red-600">{{ form.errors.occupation }}</p>
                </div>

                <!-- Address -->
                <div class="md:col-span-2">
                  <label for="address" class="block text-sm font-medium text-gray-700">
                    Dirección
                  </label>
                  <textarea
                    id="address"
                    v-model="form.address"
                    rows="3"
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                    :class="{ 'border-red-300': form.errors.address }"
                  ></textarea>
                  <p v-if="form.errors.address" class="mt-1 text-sm text-red-600">{{ form.errors.address }}</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Client Settings -->
          <div class="bg-white shadow-sm rounded-lg overflow-hidden">
            <div class="px-4 py-5 sm:p-6">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Configuración del Cliente</h3>
              
              <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
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
                    <option v-for="(label, value) in statuses" :key="value" :value="value">
                      {{ label }}
                    </option>
                  </select>
                  <p v-if="form.errors.status" class="mt-1 text-sm text-red-600">{{ form.errors.status }}</p>
                </div>

                <!-- Interest Level -->
                <div>
                  <label for="interest_level" class="block text-sm font-medium text-gray-700">
                    Nivel de interés *
                  </label>
                  <select
                    id="interest_level"
                    v-model="form.interest_level"
                    required
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                    :class="{ 'border-red-300': form.errors.interest_level }"
                  >
                    <option v-for="(label, value) in interestLevels" :key="value" :value="value">
                      {{ label }}
                    </option>
                  </select>
                  <p v-if="form.errors.interest_level" class="mt-1 text-sm text-red-600">{{ form.errors.interest_level }}</p>
                </div>

                <!-- Preferred Contact Method -->
                <div>
                  <label for="preferred_contact_method" class="block text-sm font-medium text-gray-700">
                    Método de contacto preferido *
                  </label>
                  <select
                    id="preferred_contact_method"
                    v-model="form.preferred_contact_method"
                    required
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                    :class="{ 'border-red-300': form.errors.preferred_contact_method }"
                  >
                    <option v-for="(label, value) in contactMethods" :key="value" :value="value">
                      {{ label }}
                    </option>
                  </select>
                  <p v-if="form.errors.preferred_contact_method" class="mt-1 text-sm text-red-600">{{ form.errors.preferred_contact_method }}</p>
                </div>
              </div>
            </div>
          </div>

          <!-- Profile Image -->
          <div class="bg-white shadow-sm rounded-lg overflow-hidden">
            <div class="px-4 py-5 sm:p-6">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Foto de Perfil</h3>
              
              <!-- Current Image -->
              <div v-if="client.profile_image_url" class="mb-4">
                <p class="text-sm font-medium text-gray-700 mb-2">Imagen actual:</p>
                <img :src="client.profile_image_url" :alt="client.name" class="w-24 h-24 rounded-full object-cover">
              </div>
              
              <FileUploader
                id="profile_image"
                label="Nueva foto de perfil"
                accept="image/jpeg,image/png,image/jpg,image/gif,image/webp"
                :multiple="false"
                :max-files="1"
                drag-text="Arrastra una foto aquí"
                help-text="JPG, PNG, GIF hasta 2MB. Deja vacío para mantener la imagen actual."
                @files-changed="handleProfileImageChange"
              />
              <p v-if="form.errors.profile_image" class="mt-1 text-sm text-red-600">{{ form.errors.profile_image }}</p>
            </div>
          </div>

          <!-- Attachments -->
          <div class="bg-white shadow-sm rounded-lg overflow-hidden">
            <div class="px-4 py-5 sm:p-6">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Archivos Adjuntos</h3>
              
              <!-- Current Attachments -->
              <div v-if="client.attachments && client.attachments.length > 0" class="mb-6">
                <p class="text-sm font-medium text-gray-700 mb-3">Archivos actuales:</p>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                  <div
                    v-for="(attachment, index) in client.attachments"
                    :key="index"
                    class="flex items-center justify-between p-3 bg-gray-50 rounded-lg"
                  >
                    <div class="flex items-center gap-2 min-w-0 flex-1">
                      <svg class="w-5 h-5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                      </svg>
                      <div class="min-w-0">
                        <p class="text-sm font-medium text-gray-900 truncate">{{ attachment.name }}</p>
                        <p class="text-xs text-gray-500">{{ formatFileSize(attachment.size) }}</p>
                      </div>
                    </div>
                    <div class="flex items-center gap-2">
                      <a
                        :href="`/storage/${attachment.path}`"
                        target="_blank"
                        class="text-blue-600 hover:text-blue-800 p-1 rounded"
                        title="Descargar"
                      >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                      </a>
                      <button
                        @click="removeAttachment(index)"
                        type="button"
                        class="text-red-600 hover:text-red-800 p-1 rounded"
                        title="Eliminar"
                      >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                      </button>
                    </div>
                  </div>
                </div>
              </div>
              
              <FileUploader
                id="attachments"
                label="Agregar más archivos"
                accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.gif"
                :multiple="true"
                :max-files="10"
                drag-text="Arrastra archivos aquí"
                help-text="PDF, DOC, imágenes hasta 10MB cada uno. Máximo 10 archivos."
                @files-changed="handleAttachmentsChange"
              />
              <p v-if="form.errors.attachments" class="mt-1 text-sm text-red-600">{{ form.errors.attachments }}</p>
            </div>
          </div>

          <!-- Notes -->
          <div class="bg-white shadow-sm rounded-lg overflow-hidden">
            <div class="px-4 py-5 sm:p-6">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Notas Internas</h3>
              
              <div>
                <label for="notes" class="block text-sm font-medium text-gray-700">
                  Observaciones del agente
                </label>
                <textarea
                  id="notes"
                  v-model="form.notes"
                  rows="4"
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                  :class="{ 'border-red-300': form.errors.notes }"
                  placeholder="Información adicional sobre el cliente, preferencias, historial de contacto..."
                ></textarea>
                <p v-if="form.errors.notes" class="mt-1 text-sm text-red-600">{{ form.errors.notes }}</p>
              </div>
            </div>
          </div>

          <!-- Submit Buttons -->
          <div class="flex justify-end gap-4">
            <Link
              :href="route('clients.show', client.id)"
              class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
              Cancelar
            </Link>
            <button
              type="submit"
              :disabled="form.processing"
              class="bg-blue-600 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50"
            >
              <span v-if="form.processing" class="flex items-center">
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Guardando...
              </span>
              <span v-else>Guardar Cambios</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Link } from '@inertiajs/vue3'
import FileUploader from '@/Components/FileUploader.vue'

const props = defineProps({
  client: {
    type: Object,
    required: true
  },
  documentTypes: {
    type: Object,
    required: true
  },
  statuses: {
    type: Object,
    required: true
  },
  interestLevels: {
    type: Object,
    required: true
  },
  contactMethods: {
    type: Object,
    required: true
  }
})

const form = useForm({
  name: props.client.name || '',
  email: props.client.email || '',
  phone: props.client.phone || '',
  secondary_phone: props.client.secondary_phone || '',
  document_type: props.client.document_type || 'cedula',
  document_number: props.client.document_number || '',
  address: props.client.address || '',
  birth_date: props.client.birth_date || '',
  occupation: props.client.occupation || '',
  notes: props.client.notes || '',
  interest_level: props.client.interest_level || 'medium',
  status: props.client.status || 'prospecto',
  preferred_contact_method: props.client.preferred_contact_method || 'phone',
  profile_image: null,
  attachments: null,
  remove_attachments: [],
})

const handleProfileImageChange = (files) => {
  form.profile_image = files[0] || null
}

const handleAttachmentsChange = (files) => {
  form.attachments = files
}

const removeAttachment = (index) => {
  if (confirm('¿Estás seguro de que deseas eliminar este archivo?')) {
    form.remove_attachments.push(index)
    // Remove from client attachments array for UI
    props.client.attachments.splice(index, 1)
  }
}

const formatFileSize = (bytes) => {
  if (bytes === 0) return '0 Bytes'
  const k = 1024
  const sizes = ['Bytes', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}

const submit = () => {
  form.put(route('clients.update', props.client.id))
}
</script>