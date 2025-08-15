<template>
  <div class="space-y-4">
    <!-- Client Selection -->
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
            :required="required"
            class="block w-full rounded-l-md border-gray-300 focus:border-green-500 focus:ring-green-500"
            :class="{ 'border-red-300': error }"
          >
            <option value="">Selecciona un cliente</option>
            <option v-for="client in clients" :key="client.id" :value="client.id">
              {{ client.name }} - {{ client.email }}
            </option>
          </select>
        </div>
        
        <button
          type="button"
          @click="showModal = true"
          class="relative -ml-px inline-flex items-center rounded-r-md border border-gray-300 bg-gray-50 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500"
        >
          Nuevo
        </button>
      </div>
      
      <p v-if="error" class="mt-1 text-sm text-red-600">{{ error }}</p>
    </div>

    <!-- Create Client Modal -->
    <Modal :show="showModal" @close="closeModal" max-width="2xl">
      <div class="px-6 py-4">
        <div class="flex items-center justify-between mb-4">
          <h3 class="text-lg font-medium text-gray-900">Crear Cliente</h3>
          <button
            @click="closeModal"
            class="text-gray-400 hover:text-gray-600 transition-colors"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>

        <form @submit.prevent="createClient" class="space-y-4">
          <!-- Name -->
          <div>
            <label for="client_name" class="block text-sm font-medium text-gray-700">
              Nombre completo *
            </label>
            <input
              id="client_name"
              v-model="form.name"
              type="text"
              required
              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
              :class="{ 'border-red-300': form.errors.name }"
            />
            <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
          </div>

          <!-- Email -->
          <div>
            <label for="client_email" class="block text-sm font-medium text-gray-700">
              Correo electrónico *
            </label>
            <input
              id="client_email"
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
            <label for="client_phone" class="block text-sm font-medium text-gray-700">
              Teléfono
            </label>
            <input
              id="client_phone"
              v-model="form.phone"
              type="tel"
              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
              :class="{ 'border-red-300': form.errors.phone }"
            />
            <p v-if="form.errors.phone" class="mt-1 text-sm text-red-600">{{ form.errors.phone }}</p>
          </div>

          <!-- Document Type -->
          <div>
            <label for="client_document_type" class="block text-sm font-medium text-gray-700">
              Tipo de documento *
            </label>
            <select
              id="client_document_type"
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
            <label for="client_document_number" class="block text-sm font-medium text-gray-700">
              Número de documento
            </label>
            <input
              id="client_document_number"
              v-model="form.document_number"
              type="text"
              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
              :class="{ 'border-red-300': form.errors.document_number }"
            />
            <p v-if="form.errors.document_number" class="mt-1 text-sm text-red-600">{{ form.errors.document_number }}</p>
          </div>

          <!-- Error message -->
          <div v-if="form.errors.general" class="rounded-md bg-red-50 p-4">
            <div class="flex">
              <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
              <div class="ml-3">
                <p class="text-sm text-red-800">{{ form.errors.general }}</p>
              </div>
            </div>
          </div>

          <!-- Actions -->
          <div class="flex justify-end gap-3 pt-4 border-t">
            <button
              type="button"
              @click="closeModal"
              class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
            >
              Cancelar
            </button>
            <button
              type="submit"
              :disabled="form.processing"
              class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50"
            >
              <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              {{ form.processing ? 'Creando...' : 'Crear Cliente' }}
            </button>
          </div>
        </form>
      </div>
    </Modal>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { router, usePage } from '@inertiajs/vue3'
import Modal from '@/Components/Modal.vue'

const props = defineProps({
  id: {
    type: String,
    required: true
  },
  label: {
    type: String,
    default: 'Cliente'
  },
  modelValue: {
    type: [String, Number],
    default: null
  },
  clients: {
    type: Array,
    required: true
  },
  documentTypes: {
    type: Object,
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

const emit = defineEmits(['update:modelValue', 'clientCreated'])

// Modal state
const showModal = ref(false)

// Form state
const form = reactive({
  name: '',
  email: '',
  phone: '',
  document_type: 'cedula',
  document_number: '',
  processing: false,
  errors: {}
})

const updateValue = (event) => {
  emit('update:modelValue', event.target.value ? parseInt(event.target.value) : null)
}

const closeModal = () => {
  showModal.value = false
  resetForm()
}

const resetForm = () => {
  form.name = ''
  form.email = ''
  form.phone = ''
  form.document_type = 'cedula'
  form.document_number = ''
  form.processing = false
  form.errors = {}
}

const createClient = async () => {
  form.processing = true
  form.errors = {}

  try {
    // Get CSRF token from page props or meta tag
    const csrfToken = usePage().props.csrf_token || 
      document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || 
      document.head.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
    
    if (!csrfToken) {
      console.error('CSRF token not found')
      form.errors = { general: 'Error de seguridad. Recarga la página e intenta nuevamente.' }
      return
    }

    const response = await fetch(route('clients.quick'), {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken,
        'Accept': 'application/json'
      },
      body: JSON.stringify({
        name: form.name,
        email: form.email,
        phone: form.phone,
        document_type: form.document_type,
        document_number: form.document_number
      })
    })

    const data = await response.json()

    if (response.ok) {
      // Add the new client to the clients list
      props.clients.push(data.client)
      
      // Select the newly created client
      emit('update:modelValue', data.client.id)
      
      // Emit event for parent component
      emit('clientCreated', data.client)
      
      // Close modal and reset form
      closeModal()
      
    } else {
      if (data.errors) {
        form.errors = data.errors
      } else {
        form.errors = { general: data.message || 'Error al crear el cliente' }
      }
    }
  } catch (error) {
    console.error('Error creating client:', error)
    form.errors = { general: 'Error de conexión. Intenta nuevamente.' }
  } finally {
    form.processing = false
  }
}
</script>