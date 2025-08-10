<template>
  <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg border border-gray-200">
    <div class="px-4 py-5 sm:p-6">
      <div class="flex items-center justify-between mb-4">
        <h3 class="text-lg leading-6 font-medium text-gray-900">{{ title }}</h3>
        <div v-if="showPeriodFilter" class="flex space-x-2">
          <button
            v-for="period in periods"
            :key="period.value"
            @click="$emit('period-change', period.value)"
            :class="[
              'px-3 py-1 text-sm rounded-md transition-colors',
              selectedPeriod === period.value
                ? 'bg-blue-100 text-blue-700'
                : 'text-gray-500 hover:text-gray-700 hover:bg-gray-100'
            ]"
          >
            {{ period.label }}
          </button>
        </div>
      </div>
      
      <div class="mt-4">
        <!-- Simple chart placeholder - in production you'd use Chart.js or similar -->
        <div v-if="type === 'bar'" class="space-y-3">
          <div v-for="(item, index) in chartData" :key="index" class="flex items-center">
            <div class="w-20 text-sm text-gray-600">{{ item.label }}</div>
            <div class="flex-1 mx-3">
              <div class="bg-gray-200 rounded-full h-2">
                <div 
                  :class="['h-2 rounded-full', getBarColor(index)]"
                  :style="{ width: `${(item.value / maxValue) * 100}%` }"
                ></div>
              </div>
            </div>
            <div class="w-16 text-sm font-medium text-gray-900 text-right">{{ item.value }}</div>
          </div>
        </div>
        
        <div v-else-if="type === 'pie'" class="flex items-center justify-center">
          <div class="relative w-48 h-48">
            <!-- Pie chart placeholder -->
            <div class="w-full h-full border-8 border-blue-500 rounded-full"></div>
            <div class="absolute inset-0 flex items-center justify-center">
              <span class="text-2xl font-bold text-gray-900">{{ totalValue }}</span>
            </div>
          </div>
        </div>
        
        <div v-else-if="type === 'line'" class="h-64 flex items-end space-x-2">
          <div
            v-for="(item, index) in chartData"
            :key="index"
            :style="{ height: `${(item.value / maxValue) * 100}%` }"
            :class="['w-8 bg-blue-500 rounded-t', index % 2 === 0 ? 'opacity-75' : '']"
          ></div>
        </div>
        
        <div v-else class="h-32 flex items-center justify-center text-gray-500">
          Chart placeholder for {{ type }}
        </div>
      </div>
      
      <!-- Chart legend -->
      <div v-if="showLegend && chartData.length > 0" class="mt-4 flex flex-wrap gap-4">
        <div v-for="(item, index) in chartData.slice(0, 6)" :key="index" class="flex items-center">
          <div :class="['w-3 h-3 rounded-full mr-2', getBarColor(index)]"></div>
          <span class="text-sm text-gray-600">{{ item.label }}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  title: {
    type: String,
    required: true
  },
  type: {
    type: String,
    default: 'bar',
    validator: (value) => ['bar', 'line', 'pie', 'doughnut'].includes(value)
  },
  data: {
    type: Array,
    default: () => []
  },
  labels: {
    type: Array,
    default: () => []
  },
  showLegend: {
    type: Boolean,
    default: true
  },
  showPeriodFilter: {
    type: Boolean,
    default: false
  },
  selectedPeriod: {
    type: String,
    default: '30'
  }
})

defineEmits(['period-change'])

const periods = [
  { value: '7', label: '7d' },
  { value: '30', label: '30d' },
  { value: '90', label: '90d' },
  { value: '180', label: '6m' },
  { value: '365', label: '1a' }
]

const chartData = computed(() => {
  if (props.labels.length > 0 && props.data.length > 0) {
    return props.labels.map((label, index) => ({
      label,
      value: props.data[index] || 0
    }))
  }
  return props.data || []
})

const maxValue = computed(() => {
  return Math.max(...chartData.value.map(item => item.value), 1)
})

const totalValue = computed(() => {
  return chartData.value.reduce((sum, item) => sum + item.value, 0)
})

const getBarColor = (index) => {
  const colors = [
    'bg-blue-500',
    'bg-green-500',
    'bg-yellow-500',
    'bg-purple-500',
    'bg-red-500',
    'bg-indigo-500',
    'bg-pink-500',
    'bg-gray-500'
  ]
  return colors[index % colors.length]
}
</script>