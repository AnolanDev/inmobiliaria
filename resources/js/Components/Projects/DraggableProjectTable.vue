<template>
  <div class="bg-white shadow-sm rounded-lg overflow-hidden">
    <!-- Header with drag info -->
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
      <div class="flex items-center justify-between">
        <div>
          <h3 class="text-lg font-medium text-gray-900">Ordenar Proyectos</h3>
          <p class="text-sm text-gray-500 mt-1">
            Arrastra y suelta las filas para cambiar el orden. Usa el toggle para cambiar la visibilidad pública.
          </p>
        </div>
        <div class="text-sm text-gray-500">
          <svg class="w-5 h-5 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16V4m0 0L3 8m4-4l4 4m6 0v12m0 0l4-4m-4 4l-4-4"/>
          </svg>
          Drag & Drop
        </div>
      </div>
    </div>

    <!-- Draggable Table -->
    <div class="overflow-x-auto">
      <table class="min-w-full divide-y divide-gray-200">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-8">
              #
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Proyecto
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Tipo
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Estado
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Visibilidad
            </th>
            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
              Orden
            </th>
          </tr>
        </thead>
        <tbody class="bg-white divide-y divide-gray-200">
          <draggable
            v-model="localProjects"
            :component-data="{ tag: 'tr', type: 'transition-group' }"
            v-bind="dragOptions"
            @start="onDragStart"
            @end="onDragEnd"
            item-key="id"
          >
            <template #item="{ element: project, index }">
              <tr 
                :class="[
                  'transition-all duration-200',
                  dragging ? 'cursor-grabbing' : 'cursor-grab hover:bg-gray-50',
                  !project.is_public ? 'opacity-60' : ''
                ]"
              >
                <!-- Drag Handle -->
                <td class="px-6 py-4 whitespace-nowrap text-center">
                  <div class="flex items-center justify-center">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8h16M4 16h16"/>
                    </svg>
                  </div>
                </td>

                <!-- Project Name -->
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div class="flex-shrink-0 h-10 w-10">
                      <img 
                        class="h-10 w-10 rounded-lg object-cover" 
                        :src="project.cover_image_url" 
                        :alt="project.name"
                      >
                    </div>
                    <div class="ml-4">
                      <div class="text-sm font-medium text-gray-900">{{ project.name }}</div>
                      <div class="text-sm text-gray-500">{{ project.properties_count || 0 }} propiedades</div>
                    </div>
                  </div>
                </td>

                <!-- Type -->
                <td class="px-6 py-4 whitespace-nowrap">
                  <span 
                    class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                    :class="getTypeColor(project.type)"
                  >
                    {{ project.type }}
                  </span>
                </td>

                <!-- Status -->
                <td class="px-6 py-4 whitespace-nowrap">
                  <span 
                    class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                    :class="getStatusColor(project.status)"
                  >
                    {{ project.status }}
                  </span>
                </td>

                <!-- Public Status -->
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <button
                      @click="togglePublicStatus(project)"
                      :disabled="updatingVisibility.has(project.id)"
                      class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed"
                      :class="project.is_public ? 'bg-green-600' : 'bg-gray-200'"
                    >
                      <span
                        class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                        :class="project.is_public ? 'translate-x-5' : 'translate-x-0'"
                      ></span>
                    </button>
                    <span class="ml-2 text-sm text-gray-500">
                      {{ project.is_public ? 'Público' : 'Privado' }}
                    </span>
                  </div>
                </td>

                <!-- Sort Order -->
                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                  {{ index + 1 }}
                </td>
              </tr>
            </template>
          </draggable>
        </tbody>
      </table>
    </div>

    <!-- Save Button -->
    <div v-if="hasChanges" class="px-6 py-4 bg-gray-50 border-t border-gray-200">
      <div class="flex items-center justify-between">
        <p class="text-sm text-gray-600">
          Has modificado el orden. Guarda los cambios para aplicarlos.
        </p>
        <div class="flex space-x-3">
          <button
            @click="resetOrder"
            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
          >
            Cancelar
          </button>
          <button
            @click="saveOrder"
            :disabled="saving"
            class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50"
          >
            <svg v-if="saving" class="animate-spin -ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            {{ saving ? 'Guardando...' : 'Guardar Orden' }}
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import draggable from 'vuedraggable'

const props = defineProps({
  projects: {
    type: Array,
    required: true
  }
})

const emit = defineEmits(['orderUpdated'])

// State
const localProjects = ref([...props.projects])
const originalOrder = ref([...props.projects])
const dragging = ref(false)
const saving = ref(false)
const updatingVisibility = ref(new Set())

// Computed
const hasChanges = computed(() => {
  return JSON.stringify(localProjects.value.map(p => p.id)) !== 
         JSON.stringify(originalOrder.value.map(p => p.id))
})

const dragOptions = computed(() => ({
  animation: 200,
  group: 'projects',
  disabled: false,
  ghostClass: 'ghost'
}))

// Watch for props changes
watch(
  () => props.projects,
  (newProjects) => {
    localProjects.value = [...newProjects]
    originalOrder.value = [...newProjects]
  },
  { deep: true }
)

// Methods
const onDragStart = () => {
  dragging.value = true
}

const onDragEnd = () => {
  dragging.value = false
}

const resetOrder = () => {
  localProjects.value = [...originalOrder.value]
}

const saveOrder = async () => {
  saving.value = true
  
  try {
    const projectsData = localProjects.value.map((project, index) => ({
      id: project.id,
      sort_order: index
    }))

    await router.post(route('projects.updateOrder'), {
      projects: projectsData
    }, {
      preserveState: true,
      preserveScroll: true,
      onSuccess: () => {
        originalOrder.value = [...localProjects.value]
        emit('orderUpdated')
      }
    })
  } catch (error) {
    console.error('Error updating project order:', error)
  } finally {
    saving.value = false
  }
}

const getTypeColor = (type) => {
  const colors = {
    'Campestres': 'bg-green-100 text-green-800',
    'Urbanos': 'bg-blue-100 text-blue-800', 
    'Turísticos': 'bg-purple-100 text-purple-800'
  }
  return colors[type] || 'bg-gray-100 text-gray-800'
}

const getStatusColor = (status) => {
  const colors = {
    'Vendido': 'bg-red-100 text-red-800',
    'Disponible': 'bg-green-100 text-green-800',
    'Reservado': 'bg-yellow-100 text-yellow-800'
  }
  return colors[status] || 'bg-gray-100 text-gray-800'
}

const togglePublicStatus = async (project) => {
  const originalStatus = project.is_public
  
  // Add to updating set
  updatingVisibility.value.add(project.id)
  
  try {
    // Optimistic update
    project.is_public = !project.is_public
    
    await router.patch(route('projects.toggleVisibility', project.id), {}, {
      preserveState: true,
      preserveScroll: true,
      onError: () => {
        // Revert on error
        project.is_public = originalStatus
      },
      onFinish: () => {
        // Remove from updating set
        updatingVisibility.value.delete(project.id)
      }
    })
  } catch (error) {
    // Revert on error
    project.is_public = originalStatus
    updatingVisibility.value.delete(project.id)
    console.error('Error updating project visibility:', error)
  }
}
</script>

<style scoped>
.ghost {
  opacity: 0.5;
  background: #c8ebfb;
}

/* Drag handle hover effect */
tr:hover td:first-child svg {
  color: #6b7280;
}

/* Transition for row changes */
.sortable-chosen {
  background-color: #f3f4f6;
}

.sortable-drag {
  background-color: #ffffff;
  box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
  transform: rotate(2deg);
}
</style>