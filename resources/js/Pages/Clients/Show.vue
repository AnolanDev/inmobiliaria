<template>
  <AuthenticatedLayout :title="client.name">
    <div class="min-h-screen bg-gray-50">
      <!-- Header Section -->
      <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
          <div class="flex items-center justify-between">
            <!-- Back Button & Title -->
            <div class="flex items-center space-x-4">
              <Link
                :href="route('clients.index')"
                class="inline-flex items-center p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 rounded-lg transition-colors duration-200"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
              </Link>
              <div>
                <h1 class="text-2xl font-bold text-gray-900">Perfil del Cliente</h1>
                <p class="text-sm text-gray-500 mt-1">Vista completa del cliente y sus interacciones</p>
              </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="flex items-center space-x-3">
              <Link
                :href="route('clients.edit', client.id)"
                class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors duration-200 shadow-sm"
              >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Editar Cliente
              </Link>
              
              <button
                @click="showNewInteractionModal = true"
                class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors duration-200 shadow-sm"
              >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Nueva Interacción
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Client Profile Header -->
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
          <div class="px-8 py-6">
            <div class="flex items-start space-x-6">
              <!-- Avatar -->
              <div class="flex-shrink-0">
                <div class="relative">
                  <img
                    v-if="client.profile_image_url"
                    :src="client.profile_image_url"
                    :alt="client.name"
                    class="h-24 w-24 rounded-full object-cover ring-4 ring-white shadow-lg"
                  />
                  <div
                    v-else
                    class="h-24 w-24 rounded-full bg-gradient-to-br from-green-500 to-purple-600 flex items-center justify-center ring-4 ring-white shadow-lg"
                  >
                    <span class="text-2xl font-bold text-white">
                      {{ getInitials(client.name) }}
                    </span>
                  </div>
                  
                  <!-- Status Indicator -->
                  <div class="absolute -bottom-1 -right-1">
                    <div
                      :class="[
                        'h-6 w-6 rounded-full border-2 border-white',
                        client.status === 'activo' ? 'bg-green-500' :
                        client.status === 'prospecto' ? 'bg-yellow-500' : 'bg-gray-400'
                      ]"
                    ></div>
                  </div>
                </div>
              </div>

              <!-- Client Info -->
              <div class="flex-1 min-w-0">
                <div class="flex items-start justify-between">
                  <div>
                    <h2 class="text-3xl font-bold text-gray-900">{{ client.name }}</h2>
                    <div class="mt-2 flex items-center space-x-4">
                      <span
                        :class="[
                          'inline-flex items-center px-3 py-1 rounded-full text-sm font-medium',
                          client.status === 'activo' ? 'bg-green-100 text-green-800' :
                          client.status === 'prospecto' ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-800'
                        ]"
                      >
                        {{ getStatusLabel(client.status) }}
                      </span>
                      
                      <span
                        :class="[
                          'inline-flex items-center px-3 py-1 rounded-full text-sm font-medium',
                          client.interest_level === 'high' ? 'bg-red-100 text-red-800' :
                          client.interest_level === 'medium' ? 'bg-orange-100 text-orange-800' : 'bg-green-100 text-green-800'
                        ]"
                      >
                        Interés {{ getInterestLevelLabel(client.interest_level) }}
                      </span>
                    </div>
                  </div>

                  <!-- Quick Contact Actions -->
                  <div class="flex items-center space-x-2">
                    <a
                      v-if="client.phone"
                      :href="`tel:${client.phone}`"
                      class="inline-flex items-center p-2 bg-green-50 text-green-700 hover:bg-green-100 rounded-lg transition-colors duration-200"
                      title="Llamar"
                    >
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                      </svg>
                    </a>

                    <a
                      v-if="client.email"
                      :href="`mailto:${client.email}`"
                      class="inline-flex items-center p-2 bg-green-50 text-green-700 hover:bg-green-100 rounded-lg transition-colors duration-200"
                      title="Enviar email"
                    >
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                      </svg>
                    </a>

                    <a
                      v-if="client.phone"
                      :href="`https://wa.me/${client.phone.replace(/[^\d]/g, '')}`"
                      target="_blank"
                      class="inline-flex items-center p-2 bg-green-50 text-green-700 hover:bg-green-100 rounded-lg transition-colors duration-200"
                      title="WhatsApp"
                    >
                      <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"/>
                      </svg>
                    </a>
                  </div>
                </div>

                <!-- Contact Information -->
                <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-6">
                  <div class="flex items-center space-x-3">
                    <div class="flex-shrink-0">
                      <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                      </svg>
                    </div>
                    <div>
                      <p class="text-sm font-medium text-gray-900">{{ client.email }}</p>
                      <p class="text-xs text-gray-500">Correo electrónico</p>
                    </div>
                  </div>

                  <div v-if="client.phone" class="flex items-center space-x-3">
                    <div class="flex-shrink-0">
                      <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                      </svg>
                    </div>
                    <div>
                      <p class="text-sm font-medium text-gray-900">{{ client.phone }}</p>
                      <p class="text-xs text-gray-500">Teléfono principal</p>
                    </div>
                  </div>

                  <div v-if="client.last_contact_date" class="flex items-center space-x-3">
                    <div class="flex-shrink-0">
                      <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                      </svg>
                    </div>
                    <div>
                      <p class="text-sm font-medium text-gray-900">{{ formatDate(client.last_contact_date) }}</p>
                      <p class="text-xs text-gray-500">Último contacto</p>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Tab Navigation -->
        <div class="mt-8">
          <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8">
              <button
                @click="activeTab = 'overview'"
                :class="[
                  activeTab === 'overview'
                    ? 'border-green-500 text-green-600'
                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                  'whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm transition-colors duration-200'
                ]"
              >
                <svg class="w-5 h-5 mr-2 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                Información General
              </button>
              
              <button
                @click="activeTab = 'interactions'"
                :class="[
                  activeTab === 'interactions'
                    ? 'border-green-500 text-green-600'
                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                  'whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm transition-colors duration-200'
                ]"
              >
                <svg class="w-5 h-5 mr-2 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                Historial de Interacciones
                <span v-if="getInteractionsCount() > 0" class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                  {{ getInteractionsCount() }}
                </span>
              </button>
              
              <button
                @click="activeTab = 'properties'"
                :class="[
                  activeTab === 'properties'
                    ? 'border-green-500 text-green-600'
                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300',
                  'whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm transition-colors duration-200'
                ]"
              >
                <svg class="w-5 h-5 mr-2 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                Propiedades de Interés
                <span v-if="client.properties && client.properties.length > 0" class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                  {{ client.properties.length }}
                </span>
              </button>
            </nav>
          </div>
        </div>

        <!-- Tab Content -->
        <div class="mt-8">
          <!-- Overview Tab -->
          <div v-show="activeTab === 'overview'" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
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
                        <a :href="`mailto:${client.email}`" class="text-green-600 hover:text-green-800">
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
                        <a :href="`tel:${client.phone}`" class="text-green-600 hover:text-green-800">
                          {{ client.phone }}
                        </a>
                      </dd>
                    </div>
                    
                    <div v-if="client.secondary_phone">
                      <dt class="text-sm font-medium text-gray-500">Teléfono secundario</dt>
                      <dd class="mt-1 text-sm text-gray-900">
                        <a :href="`tel:${client.secondary_phone}`" class="text-green-600 hover:text-green-800">
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

              <!-- Notes -->
              <div v-if="client.notes" class="bg-white shadow-sm rounded-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                  <h3 class="text-lg font-medium text-gray-900">Notas Internas</h3>
                </div>
                <div class="px-6 py-4">
                  <p class="text-sm text-gray-700 whitespace-pre-line">{{ client.notes }}</p>
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
                      <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
            </div>

            <!-- Stats Sidebar -->
            <div class="space-y-6">
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

          <!-- Interactions Tab -->
          <div v-show="activeTab === 'interactions'" class="space-y-6">
            <!-- Interactions Header -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
              <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Historial de Interacciones</h3>
                <button
                  @click="showNewInteractionModal = true"
                  class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors duration-200 shadow-sm"
                >
                  <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                  </svg>
                  Nueva Interacción
                </button>
              </div>

              <!-- Timeline -->
              <div class="flow-root">
                <ul class="-mb-8">
                  <!-- Sample interactions - replace with real data -->
                  <li v-for="(interaction, index) in sampleInteractions" :key="index">
                    <div class="relative pb-8">
                      <span v-if="index !== sampleInteractions.length - 1" class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                      <div class="relative flex space-x-3">
                        <div>
                          <span :class="[
                            'h-8 w-8 rounded-full flex items-center justify-center ring-8 ring-white',
                            interaction.type === 'call' ? 'bg-green-500' :
                            interaction.type === 'email' ? 'bg-green-500' :
                            interaction.type === 'meeting' ? 'bg-purple-500' :
                            interaction.type === 'visit' ? 'bg-orange-500' : 'bg-gray-500'
                          ]">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path v-if="interaction.type === 'call'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                              <path v-else-if="interaction.type === 'email'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                              <path v-else-if="interaction.type === 'meeting'" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                              <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                          </span>
                        </div>
                        <div class="min-w-0 flex-1">
                          <div>
                            <div class="text-sm">
                              <span class="font-medium text-gray-900">{{ interaction.title }}</span>
                            </div>
                            <p class="mt-0.5 text-xs text-gray-500">
                              {{ formatDateTime(interaction.date) }}
                            </p>
                          </div>
                          <div class="mt-2 text-sm text-gray-700">
                            <p>{{ interaction.description }}</p>
                          </div>
                          <div v-if="interaction.outcome" class="mt-2">
                            <span :class="[
                              'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                              interaction.outcome === 'successful' ? 'bg-green-100 text-green-800' :
                              interaction.outcome === 'pending' ? 'bg-yellow-100 text-yellow-800' :
                              'bg-red-100 text-red-800'
                            ]">
                              {{ interaction.outcome === 'successful' ? 'Exitoso' : interaction.outcome === 'pending' ? 'Pendiente' : 'Sin respuesta' }}
                            </span>
                          </div>
                        </div>
                      </div>
                    </div>
                  </li>
                </ul>
              </div>
            </div>
          </div>

          <!-- Properties Tab -->
          <div v-show="activeTab === 'properties'" class="space-y-6">
            <!-- Properties Header -->
            <div class="flex items-center justify-between">
              <h3 class="text-lg font-semibold text-gray-900">Propiedades de Interés</h3>
              <button class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors duration-200 shadow-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Asociar Propiedad
              </button>
            </div>

            <!-- Properties Grid -->
            <div v-if="client.properties && client.properties.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              <div
                v-for="property in client.properties"
                :key="property.id"
                class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-md transition-shadow duration-200"
              >
                <!-- Property Image -->
                <div class="aspect-w-16 aspect-h-9">
                  <img
                    v-if="property.cover_image_url"
                    :src="property.cover_image_url"
                    :alt="property.title"
                    class="w-full h-48 object-cover"
                  />
                  <div
                    v-else
                    class="w-full h-48 bg-gray-200 flex items-center justify-center"
                  >
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                  </div>
                </div>

                <!-- Property Info -->
                <div class="p-6">
                  <div class="flex items-start justify-between">
                    <div class="flex-1">
                      <h4 class="text-lg font-semibold text-gray-900 mb-1">{{ property.title }}</h4>
                      <p class="text-sm text-gray-600 mb-3">{{ property.address }}</p>
                      
                      <div class="flex items-center justify-between">
                        <span class="text-2xl font-bold text-green-600">
                          ${{ Number(property.price).toLocaleString() }}
                        </span>
                        <span
                          :class="[
                            'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                            getInterestStatusColor(property.pivot.status)
                          ]"
                        >
                          {{ getInterestStatusLabel(property.pivot.status) }}
                        </span>
                      </div>
                      
                      <div v-if="property.pivot.interest_type" class="mt-2">
                        <span class="text-xs text-gray-500">Tipo de interés: </span>
                        <span class="text-xs font-medium text-gray-700">{{ property.pivot.interest_type }}</span>
                      </div>
                    </div>
                  </div>

                  <!-- Property Actions -->
                  <div class="mt-4 flex items-center justify-between">
                    <Link
                      :href="route('properties.show', property.id)"
                      class="text-green-600 hover:text-green-800 text-sm font-medium"
                    >
                      Ver Detalles
                    </Link>
                    <button class="text-gray-400 hover:text-gray-600">
                      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                      </svg>
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- Empty State -->
            <div v-else class="bg-white rounded-xl shadow-sm border border-gray-200 p-12">
              <div class="text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No hay propiedades asociadas</h3>
                <p class="mt-1 text-sm text-gray-500">Este cliente aún no tiene propiedades de interés asociadas.</p>
                <div class="mt-6">
                  <button class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                    <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Asociar Primera Propiedad
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- New Interaction Modal (placeholder) -->
      <div v-if="showNewInteractionModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
          <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showNewInteractionModal = false"></div>
          <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Nueva Interacción</h3>
              <p class="text-sm text-gray-500">Funcionalidad en desarrollo...</p>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
              <button
                @click="showNewInteractionModal = false"
                class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm"
              >
                Cerrar
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Link } from '@inertiajs/vue3'

// Active tab state
const activeTab = ref('overview')
const showNewInteractionModal = ref(false)

// Sample interactions data (replace with real data from backend)
const sampleInteractions = ref([
  {
    id: 1,
    type: 'call',
    title: 'Llamada telefónica',
    description: 'Conversación sobre apartamentos disponibles en zona norte. Cliente muestra alto interés.',
    date: '2025-01-08T10:30:00Z',
    outcome: 'successful'
  },
  {
    id: 2,
    type: 'email',
    title: 'Envío de propiedades por email',
    description: 'Se enviaron 3 opciones de apartamentos que coinciden con sus preferencias.',
    date: '2025-01-07T14:15:00Z',
    outcome: 'pending'
  },
  {
    id: 3,
    type: 'meeting',
    title: 'Reunión presencial',
    description: 'Reunión en oficina para revisar documentos y definir presupuesto.',
    date: '2025-01-05T16:00:00Z',
    outcome: 'successful'
  },
  {
    id: 4,
    type: 'visit',
    title: 'Visita a propiedad',
    description: 'Visita guiada al apartamento en Chapinero. Cliente quedó muy interesado.',
    date: '2025-01-03T11:00:00Z',
    outcome: 'successful'
  }
])

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

const getInteractionsCount = () => {
  return sampleInteractions.value.length
}

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
    interesado: 'bg-green-100 text-green-800',
    contactado: 'bg-yellow-100 text-yellow-800',
    visitado: 'bg-purple-100 text-purple-800',
    negociando: 'bg-orange-100 text-orange-800',
    cerrado: 'bg-green-100 text-green-800',
    descartado: 'bg-red-100 text-red-800'
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