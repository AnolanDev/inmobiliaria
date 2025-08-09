<template>
  <div class="bg-white rounded-lg shadow-sm border border-gray-200 hover:shadow-md transition-shadow duration-200">
    <div class="p-6">
      <!-- Profile Image and Status -->
      <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-3">
          <div class="relative">
            <img
              v-if="client.profile_image_url"
              :src="client.profile_image_url"
              :alt="client.name"
              class="w-12 h-12 rounded-full object-cover"
            />
            <div
              v-else
              class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center"
            >
              <span class="text-blue-600 font-semibold text-lg">
                {{ getInitials(client.name) }}
              </span>
            </div>
            <!-- Status indicator -->
            <div
              :class="[
                'absolute -bottom-1 -right-1 w-4 h-4 rounded-full border-2 border-white',
                getStatusColor(client.status)
              ]"
            ></div>
          </div>
        </div>
        
        <!-- Interest Level Badge -->
        <span
          :class="[
            'px-2 py-1 rounded-full text-xs font-medium',
            client.interest_level_color || 'bg-gray-100 text-gray-800'
          ]"
        >
          {{ interestLevelLabel(client.interest_level) }}
        </span>
      </div>

      <!-- Client Info -->
      <div class="mb-4">
        <h3 class="text-lg font-semibold text-gray-900 mb-1">{{ client.name }}</h3>
        
        <!-- Contact Info -->
        <div class="space-y-1">
          <div v-if="client.phone" class="flex items-center gap-2 text-sm text-gray-600">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
            </svg>
            {{ client.phone }}
          </div>
          
          <div v-if="client.email" class="flex items-center gap-2 text-sm text-gray-600">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
            </svg>
            {{ client.email }}
          </div>

          <div v-if="client.document_number" class="flex items-center gap-2 text-sm text-gray-600">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V4a2 2 0 112 0v2m-6 4h12"/>
            </svg>
            {{ client.document_number }}
          </div>
        </div>
      </div>

      <!-- Status Badge -->
      <div class="mb-4">
        <span
          :class="[
            'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
            client.status_color || 'bg-gray-100 text-gray-800'
          ]"
        >
          {{ statusLabel(client.status) }}
        </span>
      </div>

      <!-- Last Contact -->
      <div v-if="client.last_contact_date" class="text-xs text-gray-500 mb-4">
        Último contacto: {{ formatDate(client.last_contact_date) }}
      </div>

      <!-- Actions -->
      <div class="flex items-center justify-between pt-4 border-t border-gray-100">
        <div class="flex items-center gap-2">
          <button
            @click="$emit('view', client)"
            class="text-blue-600 hover:text-blue-800 text-sm font-medium"
            title="Ver detalles"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
            </svg>
          </button>

          <button
            @click="$emit('edit', client)"
            class="text-gray-600 hover:text-gray-800 text-sm font-medium"
            title="Editar"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
          </button>

          <button
            @click="$emit('delete', client)"
            class="text-red-600 hover:text-red-800 text-sm font-medium"
            title="Eliminar"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
          </button>
        </div>

        <!-- Quick Contact Actions -->
        <div class="flex items-center gap-1">
          <a
            v-if="client.phone"
            :href="`tel:${client.phone}`"
            class="text-green-600 hover:text-green-800 p-1 rounded"
            title="Llamar"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
            </svg>
          </a>

          <a
            v-if="client.email"
            :href="`mailto:${client.email}`"
            class="text-blue-600 hover:text-blue-800 p-1 rounded"
            title="Enviar email"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
            </svg>
          </a>

          <a
            v-if="client.phone"
            :href="`https://wa.me/${client.phone.replace(/[^\d]/g, '')}`"
            target="_blank"
            class="text-green-500 hover:text-green-700 p-1 rounded"
            title="WhatsApp"
          >
            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
              <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"/>
            </svg>
          </a>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { defineEmits } from 'vue'

defineProps({
  client: {
    type: Object,
    required: true
  }
})

defineEmits(['view', 'edit', 'delete'])

const getInitials = (name) => {
  return name.split(' ')
    .map(word => word[0])
    .join('')
    .substring(0, 2)
    .toUpperCase()
}

const getStatusColor = (status) => {
  const colors = {
    activo: 'bg-green-500',
    inactivo: 'bg-red-500', 
    prospecto: 'bg-yellow-500'
  }
  return colors[status] || 'bg-gray-500'
}

const statusLabel = (status) => {
  const labels = {
    activo: 'Activo',
    inactivo: 'Inactivo',
    prospecto: 'Prospecto'
  }
  return labels[status] || status
}

const interestLevelLabel = (level) => {
  const labels = {
    low: 'Bajo',
    medium: 'Medio', 
    high: 'Alto'
  }
  return labels[level] || level
}

const formatDate = (date) => {
  if (!date) return ''
  return new Date(date).toLocaleDateString('es-ES')
}
</script>