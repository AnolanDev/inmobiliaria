<template>
  <AuthenticatedLayout title="Nuevo Cliente">
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
            <h1 class="text-3xl font-bold text-gray-900">Nuevo Cliente</h1>
          </div>
          <p class="text-gray-600">Agrega un nuevo cliente a tu base de datos</p>
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
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
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
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
                    :class="{ 'border-red-300': form.errors.document_type }"
                  >
                    <option value="">Selecciona un tipo</option>
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
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
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
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
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
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
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
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
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
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
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
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
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
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
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
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
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
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
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
                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
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
              
              <FileUploader
                id="profile_image"
                label="Foto de perfil"
                accept="image/jpeg,image/png,image/jpg,image/gif,image/webp"
                :multiple="false"
                :max-files="1"
                drag-text="Arrastra una foto aquí"
                help-text="JPG, PNG, GIF hasta 2MB"
                @files-changed="handleProfileImageChange"
              />
              <p v-if="form.errors.profile_image" class="mt-1 text-sm text-red-600">{{ form.errors.profile_image }}</p>
            </div>
          </div>

          <!-- Attachments -->
          <div class="bg-white shadow-sm rounded-lg overflow-hidden">
            <div class="px-4 py-5 sm:p-6">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Archivos Adjuntos</h3>
              <p class="text-sm text-gray-500 mb-4">Documentos de identidad, contratos, etc.</p>
              
              <FileUploader
                id="attachments"
                label="Archivos adjuntos"
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
                  class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
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
              :href="route('clients.index')"
              class="bg-white py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
            >
              Cancelar
            </Link>
            <button
              type="submit"
              :disabled="form.processing"
              class="bg-green-600 py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50"
            >
              <span v-if="form.processing" class="flex items-center">
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Creando...
              </span>
              <span v-else>Crear Cliente</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Link } from '@inertiajs/vue3'
import FileUploader from '@/Components/FileUploader.vue'

const props = defineProps({
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
  name: '',
  email: '',
  phone: '',
  secondary_phone: '',
  document_type: 'cedula',
  document_number: '',
  address: '',
  birth_date: '',
  occupation: '',
  notes: '',
  interest_level: 'medium',
  status: 'prospecto',
  preferred_contact_method: 'phone',
  profile_image: null,
  attachments: null,
})

const handleProfileImageChange = (files) => {
  form.profile_image = files[0] || null
}

const handleAttachmentsChange = (files) => {
  form.attachments = files
}

const submit = () => {
  form.post(route('clients.store'))
}
</script>