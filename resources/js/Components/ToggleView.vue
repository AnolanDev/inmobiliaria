<template>
  <div class="flex items-center space-x-2">
    <span class="text-sm text-gray-700">Vista:</span>
    <div class="flex items-center bg-gray-100 rounded-lg p-1">
      <button
        @click="setView('table')"
        :class="[
          'flex items-center px-3 py-1.5 text-sm font-medium rounded-md transition-all duration-200',
          currentView === 'table'
            ? 'bg-white text-gray-900 shadow-sm'
            : 'text-gray-600 hover:text-gray-900'
        ]"
      >
        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 6h18m-9 8h9m-9 4h9m-18-4h.01M3 18h.01"/>
        </svg>
        Tabla
      </button>
      
      <button
        @click="setView('cards')"
        :class="[
          'flex items-center px-3 py-1.5 text-sm font-medium rounded-md transition-all duration-200 ml-1',
          currentView === 'cards'
            ? 'bg-white text-gray-900 shadow-sm'
            : 'text-gray-600 hover:text-gray-900'
        ]"
      >
        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"/>
        </svg>
        Tarjetas
      </button>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ToggleView',
  props: {
    modelValue: {
      type: String,
      default: 'cards'
    }
  },
  emits: ['update:modelValue'],
  computed: {
    currentView() {
      return this.modelValue
    }
  },
  methods: {
    setView(view) {
      this.$emit('update:modelValue', view)
    }
  }
}
</script>