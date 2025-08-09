<template>
  <AuthenticatedLayout :title="client.name">
    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
              <Link
                :href="route('clients.index')"
                class="text-gray-500 hover:text-gray-700"
              >
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
              </Link>
              <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ client.name }}</h1>
                <p class="text-gray-600 mt-1">Información detallada del cliente</p>
              </div>
            </div>
            
            <div class="flex items-center gap-3">
              <!-- Quick Actions -->
              <div class="flex items-center gap-2">
                <a
                  v-if="client.phone"
                  :href="`tel:${client.phone}`"
                  class="bg-green-100 text-green-700 hover:bg-green-200 p-2 rounded-lg transition-colors"
                  title="Llamar"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                  </svg>
                </a>

                <a
                  v-if="client.email"
                  :href="`mailto:${client.email}`"
                  class="bg-blue-100 text-blue-700 hover:bg-blue-200 p-2 rounded-lg transition-colors"
                  title="Enviar email"
                >
                  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                  </svg>
                </a>

                <a
                  v-if="client.phone"
                  :href="`https://wa.me/${client.phone.replace(/[^\d]/g, '')}`"
                  target="_blank"
                  class="bg-green-500 text-white hover:bg-green-600 p-2 rounded-lg transition-colors"
                  title="WhatsApp"
                >
                  <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"/>
                  </svg>
                </a>
              </div>

              <Link
                :href="route('clients.edit', client.id)"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium shadow-sm transition-colors inline-flex items-center gap-2"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Editar
              </Link>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
          <!-- Main Content -->
          <div class="lg:col-span-2 space-y-8">
            <!-- Basic Information -->
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
              <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Información Personal</h3>
              </div>
              <div class="px-6 py-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                  <div>
                    <dt class="text-sm font-medium text-gray-500">Nombre completo</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ client.name }}</dd>
                  </div>
                  
                  <div>
                    <dt class="text-sm font-medium text-gray-500">Correo electrónico</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                      <a :href="`mailto:${client.email}`" class="text-blue-600 hover:text-blue-800">
                        {{ client.email }}
                      </a>
                    </dd>
                  </div>
                  
                  <div v-if="client.document_number">
                    <dt class="text-sm font-medium text-gray-500">Documento</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                      {{ getDocumentType(client.document_type) }}: {{ client.document_number }}
                    </dd>
                  </div>
                  
                  <div v-if="client.phone">
                    <dt class="text-sm font-medium text-gray-500">Teléfono principal</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                      <a :href="`tel:${client.phone}`" class="text-blue-600 hover:text-blue-800">
                        {{ client.phone }}
                      </a>
                    </dd>
                  </div>
                  
                  <div v-if="client.secondary_phone">
                    <dt class="text-sm font-medium text-gray-500">Teléfono secundario</dt>
                    <dd class="mt-1 text-sm text-gray-900">
                      <a :href="`tel:${client.secondary_phone}`" class="text-blue-600 hover:text-blue-800">
                        {{ client.secondary_phone }}
                      </a>
                    </dd>
                  </div>
                  
                  <div v-if="client.birth_date">
                    <dt class="text-sm font-medium text-gray-500">Fecha de nacimiento</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ formatDate(client.birth_date) }}</dd>
                  </div>
                  
                  <div v-if="client.occupation">
                    <dt class="text-sm font-medium text-gray-500">Ocupación</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ client.occupation }}</dd>
                  </div>
                  
                  <div v-if="client.address" class="md:col-span-2">
                    <dt class="text-sm font-medium text-gray-500">Dirección</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ client.address }}</dd>
                  </div>
                </div>
              </div>
            </div>

            <!-- Properties Interest -->
            <div v-if="client.properties && client.properties.length > 0" class="bg-white shadow-sm rounded-lg overflow-hidden">
              <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Propiedades de Interés</h3>
              </div>
              <div class="px-6 py-4">
                <div class="space-y-4">
                  <div
                    v-for="property in client.properties"
                    :key="property.id"
                    class="flex items-center justify-between p-4 border border-gray-200 rounded-lg"
                  >
                    <div class="flex items-center gap-4">
                      <img
                        v-if="property.cover_image_url"
                        :src="property.cover_image_url"
                        :alt="property.title"
                        class="w-16 h-16 object-cover rounded-lg"
                      />
                      <div
                        v-else
                        class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center"
                      >
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"/>
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v3H8V5z"/>
                        </svg>
                      </div>
                      
                      <div>
                        <h4 class="font-medium text-gray-900">{{ property.title }}</h4>
                        <p class="text-sm text-gray-500">{{ property.address }}</p>
                        <div class="flex items-center gap-4 mt-1">
                          <span class="text-lg font-semibold text-green-600">
                            ${{ Number(property.price).toLocaleString() }}
                          </span>
                          <span
                            :class="[
                              'px-2 py-1 text-xs font-medium rounded-full',
                              getInterestStatusColor(property.pivot.status)
                            ]"
                          >
                            {{ getInterestStatusLabel(property.pivot.status) }}
                          </span>
                        </div>
                      </div>
                    </div>
                    
                    <Link
                      :href="route('properties.show', property.id)"
                      class="text-blue-600 hover:text-blue-800 p-2 rounded-lg hover:bg-blue-50"
                    >
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-4M14 4v4M7 10h4"/>
                      </svg>
                    </Link>
                  </div>
                </div>
              </div>
            </div>

            <!-- Visits History -->
            <div v-if="client.visits && client.visits.length > 0" class="bg-white shadow-sm rounded-lg overflow-hidden">
              <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Historial de Visitas</h3>
              </div>
              <div class="px-6 py-4">
                <div class="space-y-4">
                  <div
                    v-for="visit in client.visits"
                    :key="visit.id"
                    class="flex items-center justify-between p-4 border border-gray-200 rounded-lg"
                  >
                    <div>
                      <div class="flex items-center gap-2">
                        <h4 class="font-medium text-gray-900">{{ visit.property.title }}</h4>
                        <span
                          :class="[
                            'px-2 py-1 text-xs font-medium rounded-full',
                            getVisitStatusColor(visit.status)
                          ]"
                        >
                          {{ visit.status }}
                        </span>
                      </div>
                      <p class="text-sm text-gray-500 mt-1">
                        {{ formatDateTime(visit.scheduled_date) }} - Agente: {{ visit.agent.name }}
                      </p>
                      <p v-if="visit.notes" class="text-sm text-gray-600 mt-2">{{ visit.notes }}</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Attachments -->
            <div v-if="client.attachments && client.attachments.length > 0" class="bg-white shadow-sm rounded-lg overflow-hidden">
              <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Archivos Adjuntos</h3>
              </div>
              <div class="px-6 py-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <a
                    v-for="(attachment, index) in client.attachments"
                    :key="index"
                    :href="`/storage/${attachment.path}`"
                    target="_blank"
                    class="flex items-center gap-3 p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors"
                  >
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                    </svg>
                    <div>
                      <p class="text-sm font-medium text-gray-900">{{ attachment.name }}</p>
                      <p class="text-xs text-gray-500">{{ formatFileSize(attachment.size) }}</p>
                    </div>
                  </a>
                </div>
              </div>
            </div>

            <!-- Notes -->
            <div v-if="client.notes" class="bg-white shadow-sm rounded-lg overflow-hidden">
              <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Notas Internas</h3>
              </div>
              <div class="px-6 py-4">
                <p class="text-sm text-gray-700 whitespace-pre-line">{{ client.notes }}</p>
              </div>
            </div>
          </div>

          <!-- Sidebar -->
          <div class="space-y-6">
            <!-- Profile Image and Status -->
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
              <div class="px-6 py-4 text-center">
                <div class="mx-auto w-24 h-24 mb-4">
                  <img
                    v-if="client.profile_image_url"
                    :src="client.profile_image_url"
                    :alt="client.name"
                    class="w-24 h-24 rounded-full object-cover mx-auto"
                  />
                  <div
                    v-else
                    class="w-24 h-24 rounded-full bg-blue-100 flex items-center justify-center mx-auto"
                  >
                    <span class="text-2xl font-semibold text-blue-600">
                      {{ getInitials(client.name) }}
                    </span>
                  </div>
                </div>
                
                <h3 class="text-lg font-medium text-gray-900">{{ client.name }}</h3>
                
                <div class="mt-3 space-y-2">
                  <span
                    :class="[
                      'inline-flex px-3 py-1 text-sm font-medium rounded-full',
                      client.status_color || 'bg-gray-100 text-gray-800'
                    ]"
                  >
                    {{ getStatusLabel(client.status) }}
                  </span>
                  
                  <br>
                  
                  <span
                    :class="[
                      'inline-flex px-3 py-1 text-sm font-medium rounded-full',
                      client.interest_level_color || 'bg-gray-100 text-gray-800'
                    ]"
                  >
                    {{ getInterestLevelLabel(client.interest_level) }}
                  </span>
                </div>
              </div>
            </div>

            <!-- Client Stats -->
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
              <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Estadísticas</h3>
              </div>
              <div class="px-6 py-4 space-y-4">
                <div class="flex justify-between">
                  <span class="text-sm text-gray-500">Propiedades de interés</span>
                  <span class="text-sm font-medium text-gray-900">
                    {{ client.properties ? client.properties.length : 0 }}
                  </span>
                </div>
                
                <div class="flex justify-between">
                  <span class="text-sm text-gray-500">Visitas realizadas</span>
                  <span class="text-sm font-medium text-gray-900">
                    {{ client.visits ? client.visits.length : 0 }}
                  </span>
                </div>
                
                <div class="flex justify-between">
                  <span class="text-sm text-gray-500">Cliente desde</span>
                  <span class="text-sm font-medium text-gray-900">
                    {{ formatDate(client.created_at) }}
                  </span>
                </div>
                
                <div v-if="client.last_contact_date" class="flex justify-between">
                  <span class="text-sm text-gray-500">Último contacto</span>
                  <span class="text-sm font-medium text-gray-900">
                    {{ formatDate(client.last_contact_date) }}
                  </span>
                </div>
              </div>
            </div>

            <!-- Contact Preferences -->
            <div class="bg-white shadow-sm rounded-lg overflow-hidden">
              <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-900">Preferencias</h3>
              </div>
              <div class="px-6 py-4 space-y-4">
                <div>
                  <span class="text-sm text-gray-500">Método de contacto preferido</span>
                  <p class="text-sm font-medium text-gray-900 mt-1">
                    {{ getContactMethodLabel(client.preferred_contact_method) }}
                  </p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Link } from '@inertiajs/vue3'

const props = defineProps({
  client: {
    type: Object,
    required: true
  },
  documentTypes: {
    type: Object,
    required: true
  },
  statuses: {
    type: Object,
    required: true
  },
  interestLevels: {
    type: Object,
    required: true
  },
  contactMethods: {
    type: Object,
    required: true
  }
})

const getInitials = (name) => {
  return name.split(' ')
    .map(word => word[0])
    .join('')
    .substring(0, 2)
    .toUpperCase()
}

const getDocumentType = (type) => {
  return props.documentTypes[type] || type
}

const getStatusLabel = (status) => {
  return props.statuses[status] || status
}

const getInterestLevelLabel = (level) => {
  return props.interestLevels[level] || level
}

const getContactMethodLabel = (method) => {
  return props.contactMethods[method] || method
}

const getInterestStatusLabel = (status) => {
  const labels = {
    interesado: 'Interesado',
    contactado: 'Contactado',
    visitado: 'Visitado',
    negociando: 'Negociando',
    cerrado: 'Cerrado',
    descartado: 'Descartado'
  }
  return labels[status] || status
}

const getInterestStatusColor = (status) => {
  const colors = {
    interesado: 'bg-blue-100 text-blue-800',
    contactado: 'bg-yellow-100 text-yellow-800',
    visitado: 'bg-purple-100 text-purple-800',
    negociando: 'bg-orange-100 text-orange-800',
    cerrado: 'bg-green-100 text-green-800',
    descartado: 'bg-red-100 text-red-800'
  }
  return colors[status] || 'bg-gray-100 text-gray-800'
}

const getVisitStatusColor = (status) => {
  const colors = {
    scheduled: 'bg-blue-100 text-blue-800',
    completed: 'bg-green-100 text-green-800',
    cancelled: 'bg-red-100 text-red-800',
    no_show: 'bg-gray-100 text-gray-800'
  }
  return colors[status] || 'bg-gray-100 text-gray-800'
}

const formatDate = (date) => {
  if (!date) return ''
  return new Date(date).toLocaleDateString('es-ES')
}

const formatDateTime = (date) => {
  if (!date) return ''
  return new Date(date).toLocaleString('es-ES')
}

const formatFileSize = (bytes) => {
  if (bytes === 0) return '0 Bytes'
  const k = 1024
  const sizes = ['Bytes', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}
</script>