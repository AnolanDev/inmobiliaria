<template>
  <div class="bg-white shadow-sm rounded-lg overflow-hidden">
    <!-- Header with drag info -->
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
      <div class="flex items-center justify-between">
        <div>
          <h3 class="text-lg font-medium text-gray-900">Ordenar Agentes</h3>
          <p class="text-sm text-gray-500 mt-1">
            Arrastra y suelta las filas para cambiar el orden automáticamente. Usa el toggle para cambiar la visibilidad pública.
          </p>
        </div>
        <div class="text-sm text-gray-500">
          <svg class="w-5 h-5 inline-block mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
          </svg>
          Auto-guardado
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
              Agente
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
        <draggable
          v-model="localAgents"
          tag="tbody"
          v-bind="dragOptions"
          @start="onDragStart"
          @end="onDragEnd"
          item-key="id"
          class="bg-white divide-y divide-gray-200"
        >
            <template #item="{ element: agent, index }">
              <tr 
                :class="[
                  'transition-all duration-200',
                  dragging ? 'cursor-grabbing' : 'cursor-grab hover:bg-gray-50',
                  !agent.is_public ? 'opacity-60' : ''
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

                <!-- Agent Name -->
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <div class="flex-shrink-0 h-10 w-10">
                      <img 
                        class="h-10 w-10 rounded-full object-cover" 
                        :src="agent.profile_picture_url" 
                        :alt="agent.name"
                      >
                    </div>
                    <div class="ml-4">
                      <div class="text-sm font-medium text-gray-900">{{ agent.name }}</div>
                      <div class="text-sm text-gray-500">{{ agent.email }}</div>
                    </div>
                  </div>
                </td>

                <!-- Type -->
                <td class="px-6 py-4 whitespace-nowrap">
                  <span 
                    class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                    :class="getTypeColor(agent.type)"
                  >
                    {{ agent.type }}
                  </span>
                </td>

                <!-- Status -->
                <td class="px-6 py-4 whitespace-nowrap">
                  <span 
                    class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                    :class="getStatusColor(agent.is_active)"
                  >
                    {{ agent.is_active ? 'Activo' : 'Inactivo' }}
                  </span>
                </td>

                <!-- Public Status -->
                <td class="px-6 py-4 whitespace-nowrap">
                  <div class="flex items-center">
                    <button
                      @click="togglePublicStatus(agent)"
                      :disabled="updatingVisibility.has(agent.id)"
                      class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed"
                      :class="agent.is_public ? 'bg-green-600' : 'bg-gray-200'"
                    >
                      <span
                        class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                        :class="agent.is_public ? 'translate-x-5' : 'translate-x-0'"
                      ></span>
                    </button>
                    <span class="ml-2 text-sm text-gray-500">
                      {{ agent.is_public ? 'Público' : 'Privado' }}
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
      </table>
    </div>

    <!-- Auto-save Status -->
    <div v-if="saving" class="px-6 py-3 bg-blue-50 border-t border-blue-200">
      <div class="flex items-center">
        <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-blue-600" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        <p class="text-sm text-blue-700">
          Guardando orden automáticamente...
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { router } from '@inertiajs/vue3'
import draggable from 'vuedraggable'

const props = defineProps({
  agents: {
    type: Array,
    required: true
  }
})

const emit = defineEmits(['orderUpdated'])

// State
const localAgents = ref([...props.agents])
const dragging = ref(false)
const saving = ref(false)
const updatingVisibility = ref(new Set())

const dragOptions = computed(() => ({
  animation: 200,
  group: 'agents',
  disabled: false,
  ghostClass: 'ghost'
}))

// Watch for props changes
watch(
  () => props.agents,
  (newAgents) => {
    localAgents.value = [...newAgents]
  },
  { deep: true }
)

// Methods
const onDragStart = () => {
  dragging.value = true
}

const onDragEnd = () => {
  dragging.value = false
  
  // Auto-save the new order
  saveOrder()
}

const saveOrder = async () => {
  saving.value = true
  
  try {
    const agentsData = localAgents.value.map((agent, index) => ({
      id: agent.id,
      sort_order: index
    }))

    await router.post(route('agents.updateOrder'), {
      agents: agentsData
    }, {
      preserveState: true,
      preserveScroll: true,
      onSuccess: () => {
        emit('orderUpdated')
      }
    })
  } catch (error) {
    console.error('Error updating agent order:', error)
  } finally {
    saving.value = false
  }
}

const getTypeColor = (type) => {
  const colors = {
    'Interno': 'bg-blue-100 text-blue-800',
    'Externo': 'bg-purple-100 text-purple-800'
  }
  return colors[type] || 'bg-gray-100 text-gray-800'
}

const getStatusColor = (isActive) => {
  return isActive 
    ? 'bg-green-100 text-green-800'
    : 'bg-red-100 text-red-800'
}

const togglePublicStatus = async (agent) => {
  const originalStatus = agent.is_public
  
  // Add to updating set
  updatingVisibility.value.add(agent.id)
  
  try {
    // Optimistic update
    agent.is_public = !agent.is_public
    
    await router.patch(route('agents.toggleVisibility', agent.id), {}, {
      preserveState: true,
      preserveScroll: true,
      onError: () => {
        // Revert on error
        agent.is_public = originalStatus
      },
      onFinish: () => {
        // Remove from updating set
        updatingVisibility.value.delete(agent.id)
      }
    })
  } catch (error) {
    // Revert on error
    agent.is_public = originalStatus
    updatingVisibility.value.delete(agent.id)
    console.error('Error updating agent visibility:', error)
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