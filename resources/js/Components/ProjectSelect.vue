<template>
  <div class="space-y-4">
    <!-- Project Selection -->
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
            class="block w-full rounded-l-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
            :class="{ 'border-red-300': error }"
            :required="required"
          >
            <option value="">Selecciona un proyecto</option>
            <option
              v-for="project in projects"
              :key="project.id"
              :value="project.id"
            >
              {{ project.name }} ({{ project.type }})
            </option>
          </select>
        </div>
        
        <button
          type="button"
          @click="showModal = true"
          class="relative -ml-px inline-flex items-center space-x-2 rounded-r-md border border-gray-300 bg-gray-50 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500"
          title="Crear nuevo proyecto"
        >
          <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
          </svg>
          <span class="hidden sm:inline">Nuevo</span>
        </button>
      </div>
      
      <p v-if="error" class="mt-1 text-sm text-red-600">{{ error }}</p>
    </div>

    <!-- Quick Create Project Modal -->
    <Modal :show="showModal" @close="closeModal" max-width="lg">
      <div class="px-6 py-4">
        <div class="flex items-center justify-between pb-4 border-b border-gray-200">
          <h3 class="text-lg font-medium text-gray-900">Crear Nuevo Proyecto</h3>
          <button
            @click="closeModal"
            class="text-gray-400 hover:text-gray-600"
          >
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
            </svg>
          </button>
        </div>

        <form @submit.prevent="createProject" class="mt-6 space-y-4">
          <!-- Project Name -->
          <div>
            <label for="project_name" class="block text-sm font-medium text-gray-700">
              Nombre del proyecto *
            </label>
            <input
              id="project_name"
              v-model="form.name"
              type="text"
              required
              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
              :class="{ 'border-red-300': form.errors.name }"
              placeholder="Ej: Conjunto Residencial Los Cedros"
            />
            <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">{{ form.errors.name }}</p>
          </div>

          <!-- Project Type -->
          <div>
            <label for="project_type" class="block text-sm font-medium text-gray-700">
              Tipo de proyecto *
            </label>
            <select
              id="project_type"
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

          <!-- Project Status -->
          <div>
            <label for="project_status" class="block text-sm font-medium text-gray-700">
              Estado *
            </label>
            <select
              id="project_status"
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

          <!-- Description -->
          <div>
            <label for="project_description" class="block text-sm font-medium text-gray-700">
              Descripción
            </label>
            <textarea
              id="project_description"
              v-model="form.description"
              rows="3"
              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-green-500 focus:border-green-500"
              :class="{ 'border-red-300': form.errors.description }"
              placeholder="Describe brevemente el proyecto..."
            />
            <p v-if="form.errors.description" class="mt-1 text-sm text-red-600">{{ form.errors.description }}</p>
          </div>

          <!-- Form Actions -->
          <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200">
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
              {{ form.processing ? 'Creando...' : 'Crear Proyecto' }}
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
    default: 'Proyecto'
  },
  modelValue: {
    type: [String, Number],
    default: null
  },
  projects: {
    type: Array,
    required: true
  },
  types: {
    type: Object,
    required: true
  },
  statuses: {
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

const emit = defineEmits(['update:modelValue', 'projectCreated'])

// Modal state
const showModal = ref(false)

// Form state
const form = reactive({
  name: '',
  type: '',
  status: 'Disponible',
  description: '',
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
  form.type = 'Disponible'
  form.status = 'Disponible'
  form.description = ''
  form.processing = false
  form.errors = {}
}

const createProject = async () => {
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

    const response = await fetch(route('properties.projects.quick'), {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken,
        'Accept': 'application/json'
      },
      body: JSON.stringify({
        name: form.name,
        type: form.type,
        status: form.status,
        description: form.description
      })
    })

    const data = await response.json()

    if (response.ok) {
      // Add the new project to the projects list
      props.projects.push(data.project)
      
      // Select the newly created project
      emit('update:modelValue', data.project.id)
      
      // Emit event for parent component
      emit('projectCreated', data.project)
      
      // Close modal and reset form
      closeModal()
      
      // Show success message (optional)
      // You could use a toast notification here
    } else {
      if (data.errors) {
        form.errors = data.errors
      } else {
        form.errors = { general: data.message || 'Error al crear el proyecto' }
      }
    }
  } catch (error) {
    console.error('Error creating project:', error)
    form.errors = { general: 'Error de conexión. Intenta nuevamente.' }
  } finally {
    form.processing = false
  }
}
</script>