<template>
  <Head title="Nuevo Lead" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center space-x-4">
        <Link :href="route('leads.index')" class="text-gray-500 hover:text-gray-700">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
          </svg>
        </Link>
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Nuevo Lead
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
                <!-- First Name -->
                <div>
                  <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">
                    Nombre *
                  </label>
                  <input
                    id="first_name"
                    v-model="form.first_name"
                    type="text"
                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                    :class="{ 'border-red-500': form.errors.first_name }"
                    required
                  />
                  <p v-if="form.errors.first_name" class="mt-1 text-sm text-red-600">
                    {{ form.errors.first_name }}
                  </p>
                </div>

                <!-- Last Name -->
                <div>
                  <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">
                    Apellido *
                  </label>
                  <input
                    id="last_name"
                    v-model="form.last_name"
                    type="text"
                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                    :class="{ 'border-red-500': form.errors.last_name }"
                    required
                  />
                  <p v-if="form.errors.last_name" class="mt-1 text-sm text-red-600">
                    {{ form.errors.last_name }}
                  </p>
                </div>

                <!-- Email -->
                <div>
                  <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
                    Email *
                  </label>
                  <input
                    id="email"
                    v-model="form.email"
                    type="email"
                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                    :class="{ 'border-red-500': form.errors.email }"
                    required
                  />
                  <p v-if="form.errors.email" class="mt-1 text-sm text-red-600">
                    {{ form.errors.email }}
                  </p>
                </div>

                <!-- Phone -->
                <div>
                  <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">
                    Teléfono
                  </label>
                  <input
                    id="phone"
                    v-model="form.phone"
                    type="tel"
                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                    :class="{ 'border-red-500': form.errors.phone }"
                  />
                  <p v-if="form.errors.phone" class="mt-1 text-sm text-red-600">
                    {{ form.errors.phone }}
                  </p>
                </div>
              </div>
            </div>

            <!-- Lead Details -->
            <div class="border-b border-gray-200 pb-6">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Detalles del Lead</h3>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
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

                <!-- Source -->
                <div>
                  <label for="source" class="block text-sm font-medium text-gray-700 mb-1">
                    Fuente *
                  </label>
                  <select
                    id="source"
                    v-model="form.source"
                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                    :class="{ 'border-red-500': form.errors.source }"
                    required
                  >
                    <option value="">Seleccionar fuente</option>
                    <option v-for="(label, value) in sources" :key="value" :value="value">
                      {{ label }}
                    </option>
                  </select>
                  <p v-if="form.errors.source" class="mt-1 text-sm text-red-600">
                    {{ form.errors.source }}
                  </p>
                </div>

                <!-- Priority -->
                <div>
                  <label for="priority" class="block text-sm font-medium text-gray-700 mb-1">
                    Prioridad *
                  </label>
                  <select
                    id="priority"
                    v-model="form.priority"
                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                    :class="{ 'border-red-500': form.errors.priority }"
                    required
                  >
                    <option v-for="(label, value) in priorities" :key="value" :value="value">
                      {{ label }}
                    </option>
                  </select>
                  <p v-if="form.errors.priority" class="mt-1 text-sm text-red-600">
                    {{ form.errors.priority }}
                  </p>
                </div>

                <!-- Campaign -->
                <div>
                  <label for="campaign_id" class="block text-sm font-medium text-gray-700 mb-1">
                    Campaña
                  </label>
                  <select
                    id="campaign_id"
                    v-model="form.campaign_id"
                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                    :class="{ 'border-red-500': form.errors.campaign_id }"
                  >
                    <option value="">Sin campaña</option>
                    <option v-for="campaign in campaigns" :key="campaign.id" :value="campaign.id">
                      {{ campaign.name }}
                    </option>
                  </select>
                  <p v-if="form.errors.campaign_id" class="mt-1 text-sm text-red-600">
                    {{ form.errors.campaign_id }}
                  </p>
                </div>

                <!-- Assigned Agent -->
                <div>
                  <label for="assigned_agent_id" class="block text-sm font-medium text-gray-700 mb-1">
                    Agente Asignado
                  </label>
                  <select
                    id="assigned_agent_id"
                    v-model="form.assigned_agent_id"
                    class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                    :class="{ 'border-red-500': form.errors.assigned_agent_id }"
                  >
                    <option value="">Sin asignar</option>
                    <option v-for="agent in agents" :key="agent.id" :value="agent.id">
                      {{ agent.name }}
                    </option>
                  </select>
                  <p v-if="form.errors.assigned_agent_id" class="mt-1 text-sm text-red-600">
                    {{ form.errors.assigned_agent_id }}
                  </p>
                </div>

                <!-- Budget Range -->
                <div>
                  <label for="budget_min" class="block text-sm font-medium text-gray-700 mb-1">
                    Presupuesto Mínimo
                  </label>
                  <div class="relative">
                    <span class="absolute left-3 top-2 text-gray-500">$</span>
                    <input
                      id="budget_min"
                      v-model="form.budget_min"
                      type="number"
                      step="1000000"
                      min="0"
                      class="w-full border border-gray-300 rounded-md pl-8 pr-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                      :class="{ 'border-red-500': form.errors.budget_min }"
                    />
                  </div>
                  <p v-if="form.errors.budget_min" class="mt-1 text-sm text-red-600">
                    {{ form.errors.budget_min }}
                  </p>
                </div>
              </div>

              <!-- Notes -->
              <div class="mt-6">
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-1">
                  Notas
                </label>
                <textarea
                  id="notes"
                  v-model="form.notes"
                  rows="3"
                  class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                  :class="{ 'border-red-500': form.errors.notes }"
                  placeholder="Información adicional sobre el lead..."
                />
                <p v-if="form.errors.notes" class="mt-1 text-sm text-red-600">
                  {{ form.errors.notes }}
                </p>
              </div>
            </div>

            <!-- Interests -->
            <div class="pb-6">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Intereses</h3>
              
              <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                <label v-for="interest in availableInterests" :key="interest" class="flex items-center">
                  <input
                    v-model="selectedInterests"
                    :value="interest"
                    type="checkbox"
                    class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                  />
                  <span class="ml-2 text-sm text-gray-700">{{ interest }}</span>
                </label>
              </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
              <Link
                :href="route('leads.index')"
                class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
              >
                Cancelar
              </Link>
              <button
                type="submit"
                :disabled="form.processing"
                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 disabled:opacity-50"
              >
                <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                {{ form.processing ? 'Creando...' : 'Crear Lead' }}
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
  statuses: Object,
  sources: Object,
  priorities: Object,
  campaigns: Array,
  agents: Array,
  campaign_id: [String, Number]
})

// Form data
const form = useForm({
  first_name: '',
  last_name: '',
  email: '',
  phone: '',
  status: 'new',
  source: '',
  priority: 'medium',
  campaign_id: props.campaign_id || '',
  assigned_agent_id: '',
  budget_min: '',
  notes: '',
  interests: []
})

// Selected interests
const selectedInterests = ref([])

// Available interests
const availableInterests = [
  'Propiedades Residenciales',
  'Propiedades Comerciales',
  'Apartamentos',
  'Casas',
  'Fincas',
  'Locales Comerciales',
  'Oficinas',
  'Bodegas',
  'Lotes',
  'Proyectos Nuevos',
  'Propiedades de Lujo',
  'Primera Vivienda',
  'Inversión Inmobiliaria'
]

// Watch for changes to update form data
watch(selectedInterests, (newValue) => {
  form.interests = newValue
}, { deep: true })

// Submit form
const submit = () => {
  form.post(route('leads.store'))
}
</script>