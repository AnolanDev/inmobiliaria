<template>
  <div class="space-y-4">
    <!-- Agent Selection -->
    <div>
      <label :for="id" class="block text-sm font-medium text-gray-700">
        {{ label }}
        <span v-if="required" class="text-red-500">*</span>
      </label>
      
      <div class="mt-1 flex rounded-md shadow-sm">
        <div class="relative flex-1">
          <select
            :id="id"
            :value="modelValue"
            @input="updateValue"
            class="block w-full rounded-l-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
            :class="{ 'border-red-300': error }"
            :required="required"
          >
            <option value="">Selecciona un agente</option>
            <option
              v-for="agent in agents"
              :key="agent.id"
              :value="agent.id"
            >
              {{ agent.name }} ({{ agent.type }}) - {{ agent.email }}
            </option>
          </select>
        </div>
        
        <button
          type="button"
          @click="showModal = true"
          class="relative -ml-px inline-flex items-center space-x-2 rounded-r-md border border-gray-300 bg-gray-50 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500"
          title="Crear nuevo agente"
        >
          <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
          </svg>
          <span class="hidden sm:inline">Nuevo</span>
        </button>
      </div>
      
      <p v-if="error" class="mt-1 text-sm text-red-600">{{ error }}</p>
    </div>

    <!-- Quick Create Agent Modal -->
    <Modal :show="showModal" @close="closeModal" max-width="lg">
      <div class="px-6 py-4">
        <div class="flex items-center justify-between pb-4 border-b border-gray-200">
          <h3 class="text-lg font-medium text-gray-900">Crear Nuevo Agente</h3>
          <button
            @click="closeModal"
            class="text-gray-400 hover:text-gray-600"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>

        <form @submit.prevent="createAgent" class="mt-6 space-y-4">
          <!-- Agent Name -->
          <div>
            <label for="agent_name" class="block text-sm font-medium text-gray-700">
              Nombre completo *
            </label>
            <input
              id="agent_name"
              v-model="form.name"
              type="text"
              required
              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
              :class="{ 'border-red-300': form.errors.name }"
              placeholder="Ej: Juan Carlos Rodríguez"
            />
            <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
          </div>

          <!-- Email -->
          <div>
            <label for="agent_email" class="block text-sm font-medium text-gray-700">
              Correo electrónico *
            </label>
            <input
              id="agent_email"
              v-model="form.email"
              type="email"
              required
              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
              :class="{ 'border-red-300': form.errors.email }"
              placeholder="Ej: juan.rodriguez@inmobiliaria.com"
            />
            <p v-if="form.errors.email" class="mt-1 text-sm text-red-600">{{ form.errors.email }}</p>
          </div>

          <!-- Phone -->
          <div>
            <label for="agent_phone" class="block text-sm font-medium text-gray-700">
              Teléfono *
            </label>
            <input
              id="agent_phone"
              v-model="form.phone"
              type="tel"
              required
              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
              :class="{ 'border-red-300': form.errors.phone }"
              placeholder="Ej: +57 300 123 4567"
            />
            <p v-if="form.errors.phone" class="mt-1 text-sm text-red-600">{{ form.errors.phone }}</p>
          </div>

          <!-- Agent Type -->
          <div>
            <label for="agent_type" class="block text-sm font-medium text-gray-700">
              Tipo de agente *
            </label>
            <select
              id="agent_type"
              v-model="form.type"
              required
              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
              :class="{ 'border-red-300': form.errors.type }"
            >
              <option value="">Selecciona un tipo</option>
              <option value="Interno">Interno</option>
              <option value="Externo">Externo</option>
            </select>
            <p v-if="form.errors.type" class="mt-1 text-sm text-red-600">{{ form.errors.type }}</p>
          </div>

          <!-- General Errors -->
          <div v-if="form.errors.general" class="rounded-md bg-red-50 p-4">
            <div class="flex">
              <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                  <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                </svg>
              </div>
              <div class="ml-3">
                <h3 class="text-sm font-medium text-red-800">
                  {{ form.errors.general }}
                </h3>
              </div>
            </div>
          </div>

          <!-- Form Actions -->
          <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
            <button
              type="button"
              @click="closeModal"
              class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
            >
              Cancelar
            </button>
            <button
              type="submit"
              :disabled="form.processing"
              class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50"
            >
              <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              {{ form.processing ? 'Creando...' : 'Crear Agente' }}
            </button>
          </div>
        </form>
      </div>
    </Modal>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { router } from '@inertiajs/vue3'
import Modal from '@/Components/Modal.vue'

const props = defineProps({
  id: {
    type: String,
    required: true
  },
  label: {
    type: String,
    default: 'Agente'
  },
  modelValue: {
    type: [String, Number],
    default: null
  },
  agents: {
    type: Array,
    required: true
  },
  required: {
    type: Boolean,
    default: false
  },
  error: {
    type: String,
    default: null
  }
})

const emit = defineEmits(['update:modelValue', 'agentCreated'])

// Modal state
const showModal = ref(false)

// Form state
const form = reactive({
  name: '',
  email: '',
  phone: '',
  type: 'Interno',
  processing: false,
  errors: {}
})

const updateValue = (event) => {
  emit('update:modelValue', event.target.value || null)
}

const closeModal = () => {
  showModal.value = false
  resetForm()
}

const resetForm = () => {
  form.name = ''
  form.email = ''
  form.phone = ''
  form.type = 'Interno'
  form.processing = false
  form.errors = {}
}

const createAgent = async () => {
  form.processing = true
  form.errors = {}

  try {
    const response = await fetch(route('agents.quick'), {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
        'Accept': 'application/json'
      },
      body: JSON.stringify({
        name: form.name,
        email: form.email,
        phone: form.phone,
        type: form.type
      })
    })

    const data = await response.json()

    if (response.ok) {
      // Add the new agent to the agents list
      props.agents.push(data.agent)
      
      // Select the newly created agent
      emit('update:modelValue', data.agent.id)
      
      // Emit event for parent component
      emit('agentCreated', data.agent)
      
      // Close modal and reset form
      closeModal()
      
      // Show success message (optional)
      // You could use a toast notification here
    } else {
      if (data.errors) {
        form.errors = data.errors
      } else {
        form.errors = { general: data.message || 'Error al crear el agente' }
      }
    }
  } catch (error) {
    console.error('Error creating agent:', error)
    form.errors = { general: 'Error de conexión. Intenta nuevamente.' }
  } finally {
    form.processing = false
  }
}
</script>