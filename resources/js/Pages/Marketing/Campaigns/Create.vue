<template>
  <Head title="Nueva Campaña" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center space-x-4">
        <Link :href="route('campaigns.index')" class="text-gray-500 hover:text-gray-700">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
          </svg>
        </Link>
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Nueva Campaña de Marketing
        </h2>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <form @submit.prevent="submit" class="p-6 space-y-6">
            <!-- Basic Information -->
            <div class="border-b border-gray-200 pb-6">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Información Básica</h3>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Name -->
                <div>
                  <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                    Nombre de la Campaña *
                  </label>
                  <input
                    id="name"
                    v-model="form.name"
                    type="text"
                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                    :class="{ 'border-red-500': form.errors.name }"
                    required
                  />
                  <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">
                    {{ form.errors.name }}
                  </p>
                </div>

                <!-- Type -->
                <div>
                  <label for="type" class="block text-sm font-medium text-gray-700 mb-1">
                    Tipo de Campaña *
                  </label>
                  <select
                    id="type"
                    v-model="form.type"
                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                    :class="{ 'border-red-500': form.errors.type }"
                    required
                  >
                    <option value="">Seleccionar tipo</option>
                    <option v-for="(label, value) in types" :key="value" :value="value">
                      {{ label }}
                    </option>
                  </select>
                  <p v-if="form.errors.type" class="mt-1 text-sm text-red-600">
                    {{ form.errors.type }}
                  </p>
                </div>

                <!-- Status -->
                <div>
                  <label for="status" class="block text-sm font-medium text-gray-700 mb-1">
                    Estado *
                  </label>
                  <select
                    id="status"
                    v-model="form.status"
                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                    :class="{ 'border-red-500': form.errors.status }"
                    required
                  >
                    <option v-for="(label, value) in statuses" :key="value" :value="value">
                      {{ label }}
                    </option>
                  </select>
                  <p v-if="form.errors.status" class="mt-1 text-sm text-red-600">
                    {{ form.errors.status }}
                  </p>
                </div>

                <!-- Budget -->
                <div>
                  <label for="budget" class="block text-sm font-medium text-gray-700 mb-1">
                    Presupuesto
                  </label>
                  <div class="relative">
                    <span class="absolute left-3 top-2 text-gray-500">$</span>
                    <input
                      id="budget"
                      v-model="form.budget"
                      type="number"
                      step="0.01"
                      min="0"
                      class="w-full border border-gray-300 rounded-md pl-8 pr-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                      :class="{ 'border-red-500': form.errors.budget }"
                    />
                  </div>
                  <p v-if="form.errors.budget" class="mt-1 text-sm text-red-600">
                    {{ form.errors.budget }}
                  </p>
                </div>
              </div>

              <!-- Description -->
              <div class="mt-6">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                  Descripción
                </label>
                <textarea
                  id="description"
                  v-model="form.description"
                  rows="3"
                  class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                  :class="{ 'border-red-500': form.errors.description }"
                  placeholder="Describe los objetivos y detalles de la campaña..."
                />
                <p v-if="form.errors.description" class="mt-1 text-sm text-red-600">
                  {{ form.errors.description }}
                </p>
              </div>
            </div>

            <!-- Timeline -->
            <div class="border-b border-gray-200 pb-6">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Programación</h3>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Start Date -->
                <div>
                  <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">
                    Fecha de Inicio
                  </label>
                  <input
                    id="start_date"
                    v-model="form.start_date"
                    type="date"
                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                    :class="{ 'border-red-500': form.errors.start_date }"
                  />
                  <p v-if="form.errors.start_date" class="mt-1 text-sm text-red-600">
                    {{ form.errors.start_date }}
                  </p>
                </div>

                <!-- End Date -->
                <div>
                  <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">
                    Fecha de Finalización
                  </label>
                  <input
                    id="end_date"
                    v-model="form.end_date"
                    type="date"
                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                    :class="{ 'border-red-500': form.errors.end_date }"
                    :min="form.start_date"
                  />
                  <p v-if="form.errors.end_date" class="mt-1 text-sm text-red-600">
                    {{ form.errors.end_date }}
                  </p>
                </div>
              </div>
            </div>

            <!-- Target Audience -->
            <div class="border-b border-gray-200 pb-6">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Audiencia Objetivo</h3>
              
              <div class="space-y-4">
                <!-- Age Range -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                      Edad Mínima
                    </label>
                    <input
                      v-model="targetAudience.age_min"
                      type="number"
                      min="18"
                      max="100"
                      class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                    />
                  </div>
                  <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                      Edad Máxima
                    </label>
                    <input
                      v-model="targetAudience.age_max"
                      type="number"
                      min="18"
                      max="100"
                      class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                    />
                  </div>
                </div>

                <!-- Location -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">
                    Ubicación
                  </label>
                  <input
                    v-model="targetAudience.location"
                    type="text"
                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Ciudades, departamentos o regiones objetivo"
                  />
                </div>

                <!-- Interests -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">
                    Intereses
                  </label>
                  <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                    <label v-for="interest in availableInterests" :key="interest" class="flex items-center">
                      <input
                        v-model="targetAudience.interests"
                        :value="interest"
                        type="checkbox"
                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                      />
                      <span class="ml-2 text-sm text-gray-700">{{ interest }}</span>
                    </label>
                  </div>
                </div>
              </div>
            </div>

            <!-- Campaign Content -->
            <div v-if="form.type === 'email' || form.type === 'sms'" class="pb-6">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Contenido de la Campaña</h3>
              
              <div class="space-y-4">
                <!-- Subject (for email) -->
                <div v-if="form.type === 'email'">
                  <label class="block text-sm font-medium text-gray-700 mb-1">
                    Asunto del Email
                  </label>
                  <input
                    v-model="content.subject"
                    type="text"
                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Asunto atractivo para el email"
                  />
                </div>

                <!-- Message Content -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">
                    {{ form.type === 'email' ? 'Contenido del Email' : 'Mensaje SMS' }}
                  </label>
                  <textarea
                    v-model="content.message"
                    :rows="form.type === 'email' ? 6 : 3"
                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                    :placeholder="form.type === 'email' ? 'Contenido HTML del email...' : 'Mensaje de texto (máximo 160 caracteres)'"
                    :maxlength="form.type === 'sms' ? 160 : null"
                  />
                  <p v-if="form.type === 'sms'" class="mt-1 text-sm text-gray-500">
                    {{ content.message?.length || 0 }}/160 caracteres
                  </p>
                </div>

                <!-- Call to Action -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">
                    Llamada a la Acción
                  </label>
                  <input
                    v-model="content.cta_text"
                    type="text"
                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="Texto del botón o enlace"
                  />
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">
                    URL de Destino
                  </label>
                  <input
                    v-model="content.cta_url"
                    type="url"
                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                    placeholder="https://ejemplo.com/landing-page"
                  />
                </div>
              </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
              <Link
                :href="route('campaigns.index')"
                class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
              >
                Cancelar
              </Link>
              <button
                type="submit"
                :disabled="form.processing"
                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50"
              >
                <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                {{ form.processing ? 'Creando...' : 'Crear Campaña' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, watch } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
  types: Object,
  statuses: Object
})

// Form data
const form = useForm({
  name: '',
  type: '',
  status: 'draft',
  description: '',
  budget: '',
  start_date: '',
  end_date: '',
  target_audience: {},
  content: {}
})

// Target audience reactive data
const targetAudience = ref({
  age_min: '',
  age_max: '',
  location: '',
  interests: []
})

// Content reactive data
const content = ref({
  subject: '',
  message: '',
  cta_text: '',
  cta_url: ''
})

// Available interests
const availableInterests = [
  'Propiedades Residenciales',
  'Propiedades Comerciales',
  'Inversión Inmobiliaria',
  'Primera Vivienda',
  'Vivienda de Lujo',
  'Proyectos Nuevos',
  'Propiedades Usadas'
]

// Watch for changes to update form data
watch(targetAudience, (newValue) => {
  form.target_audience = newValue
}, { deep: true })

watch(content, (newValue) => {
  form.content = newValue
}, { deep: true })

// Submit form
const submit = () => {
  form.post(route('campaigns.store'))
}
</script>