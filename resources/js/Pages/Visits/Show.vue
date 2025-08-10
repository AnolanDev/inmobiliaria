<template>
  <AuthenticatedLayout :title="`Visita - ${visit.property?.title || 'Visita'}`">
    <div class="py-12">
      <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex justify-between items-start mb-8">
          <div>
            <h1 class="text-3xl font-bold text-gray-900">
              Detalles de la Visita
            </h1>
            <p class="text-gray-600 mt-2">
              {{ formatDateTime(visit.scheduled_at) }}
            </p>
          </div>
          
          <div class="flex items-center gap-3">
            <!-- Status Badge -->
            <span
              :class="[
                'px-3 py-1 rounded-full text-sm font-medium',
                visit.status_color || 'bg-gray-100 text-gray-800'
              ]"
            >
              {{ statusLabel(visit.status) }}
            </span>
            
            <!-- Actions Menu -->
            <div class="flex items-center gap-2">
              <Link
                v-if="visit.status === 'scheduled'"
                :href="route('visits.edit', visit.id)"
                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium transition-colors"
              >
                Editar Visita
              </Link>
              
              <Link
                :href="route('visits.index')"
                class="text-gray-600 hover:text-gray-900 flex items-center gap-2"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Volver a Visitas
              </Link>
            </div>
          </div>
        </div>

        <!-- Main Content -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
          <!-- Left Column - Visit Details -->
          <div class="lg:col-span-2 space-y-6">
            <!-- Basic Information -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
              <h3 class="text-lg font-semibold text-gray-900 mb-4">Información General</h3>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label class="block text-sm font-medium text-gray-600 mb-1">Tipo de Visita</label>
                  <span
                    :class="[
                      'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                      visit.type_color || 'bg-gray-100 text-gray-800'
                    ]"
                  >
                    {{ typeLabel(visit.type) }}
                  </span>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-600 mb-1">Prioridad</label>
                  <span
                    :class="[
                      'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                      visit.priority_color || 'bg-gray-100 text-gray-800'
                    ]"
                  >
                    {{ priorityLabel(visit.priority) }}
                  </span>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-600 mb-1">Fecha y Hora</label>
                  <p class="text-gray-900">{{ formatDateTime(visit.scheduled_at) }}</p>
                </div>

                <div v-if="visit.estimated_duration">
                  <label class="block text-sm font-medium text-gray-600 mb-1">Duración Estimada</label>
                  <p class="text-gray-900">{{ visit.estimated_duration }} minutos</p>
                </div>

                <div v-if="visit.actual_duration">
                  <label class="block text-sm font-medium text-gray-600 mb-1">Duración Real</label>
                  <p class="text-gray-900">{{ visit.actual_duration }} minutos</p>
                </div>

                <div v-if="visit.completed_at">
                  <label class="block text-sm font-medium text-gray-600 mb-1">Completada en</label>
                  <p class="text-gray-900">{{ formatDateTime(visit.completed_at) }}</p>
                </div>

                <div v-if="visit.cancelled_at">
                  <label class="block text-sm font-medium text-gray-600 mb-1">Cancelada en</label>
                  <p class="text-gray-900">{{ formatDateTime(visit.cancelled_at) }}</p>
                </div>

                <div v-if="visit.cancellation_reason">
                  <label class="block text-sm font-medium text-gray-600 mb-1">Motivo de Cancelación</label>
                  <p class="text-gray-900">{{ visit.cancellation_reason }}</p>
                </div>
              </div>
            </div>

            <!-- Visit Subject Information -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
              <h3 class="text-lg font-semibold text-gray-900 mb-4">
                {{ visit.is_project_visit ? 'Proyecto' : 'Propiedad' }}
              </h3>
              
              <!-- Property Info -->
              <div v-if="visit.is_property_visit" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label class="block text-sm font-medium text-gray-600 mb-1">Título</label>
                  <p class="text-gray-900 font-medium">{{ visit.property?.title || 'No disponible' }}</p>
                </div>

                <div>
                  <label class="block text-sm font-medium text-gray-600 mb-1">Dirección</label>
                  <p class="text-gray-900">{{ visit.property?.address || 'No disponible' }}</p>
                </div>

                <div v-if="visit.property?.price">
                  <label class="block text-sm font-medium text-gray-600 mb-1">Precio</label>
                  <p class="text-gray-900 font-medium">
                    ${{ Number(visit.property.price).toLocaleString() }}
                  </p>
                </div>
              </div>

              <!-- Project Info -->
              <div v-if="visit.is_project_visit" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <label class="block text-sm font-medium text-gray-600 mb-1">Nombre</label>
                  <p class="text-gray-900 font-medium">{{ visit.project?.name || 'No disponible' }}</p>
                </div>

                <div class="md:col-span-2">
                  <label class="block text-sm font-medium text-gray-600 mb-1">Descripción</label>
                  <p class="text-gray-900">{{ visit.project?.description || 'No disponible' }}</p>
                </div>
              </div>
            </div>

            <!-- Participants -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
              <h3 class="text-lg font-semibold text-gray-900 mb-4">Participantes</h3>
              
              <!-- Client -->
              <div class="mb-6">
                <h4 class="text-sm font-medium text-gray-600 mb-2">Cliente Principal</h4>
                <div class="bg-gray-50 rounded-lg p-4">
                  <div class="flex items-center gap-4">
                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center">
                      <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                      </svg>
                    </div>
                    <div class="flex-1">
                      <p class="font-medium text-gray-900">{{ visit.client?.name || 'No disponible' }}</p>
                      <div class="text-sm text-gray-600 space-y-1">
                        <p v-if="visit.client_email">{{ visit.client_email }}</p>
                        <p v-if="visit.client_phone">{{ visit.client_phone }}</p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Agent -->
              <div class="mb-6">
                <h4 class="text-sm font-medium text-gray-600 mb-2">Agente Responsable</h4>
                <div class="bg-gray-50 rounded-lg p-4">
                  <div class="flex items-center gap-4">
                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                      <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 00-2 2h-4a2 2 0 00-2-2V6m8 0h2a2 2 0 012 2v6a2 2 0 01-2-2H6a2 2 0 01-2-2V8a2 2 0 012-2h2"/>
                      </svg>
                    </div>
                    <div class="flex-1">
                      <p class="font-medium text-gray-900">{{ visit.agent?.name || 'No disponible' }}</p>
                      <p v-if="visit.agent?.email" class="text-sm text-gray-600">{{ visit.agent.email }}</p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Additional Participants -->
              <div v-if="visit.additional_participants && visit.additional_participants.length">
                <h4 class="text-sm font-medium text-gray-600 mb-2">Participantes Adicionales</h4>
                <div class="space-y-3">
                  <div 
                    v-for="(participant, index) in visit.additional_participants" 
                    :key="index"
                    class="bg-gray-50 rounded-lg p-4"
                  >
                    <div class="flex items-center gap-4">
                      <div class="w-8 h-8 bg-gray-200 rounded-full flex items-center justify-center">
                        <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                      </div>
                      <div class="flex-1">
                        <p class="font-medium text-gray-900">{{ participant.name }}</p>
                        <div class="text-sm text-gray-600">
                          <p v-if="participant.role">{{ participant.role }}</p>
                          <p v-if="participant.phone">{{ participant.phone }}</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Results and Feedback (if completed) -->
            <div v-if="visit.status === 'completed'" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
              <h3 class="text-lg font-semibold text-gray-900 mb-4">Resultado de la Visita</h3>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div v-if="visit.outcome">
                  <label class="block text-sm font-medium text-gray-600 mb-1">Resultado</label>
                  <span
                    :class="[
                      'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                      getOutcomeColor(visit.outcome)
                    ]"
                  >
                    {{ outcomeLabel(visit.outcome) }}
                  </span>
                </div>

                <div v-if="visit.client_rating">
                  <label class="block text-sm font-medium text-gray-600 mb-1">Calificación del Cliente</label>
                  <div class="flex items-center gap-1">
                    <template v-for="n in 5" :key="n">
                      <svg 
                        :class="n <= visit.client_rating ? 'text-yellow-400' : 'text-gray-300'"
                        class="w-5 h-5" 
                        fill="currentColor" 
                        viewBox="0 0 20 20"
                      >
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                      </svg>
                    </template>
                  </div>
                </div>

                <div v-if="visit.offered_price" class="md:col-span-2">
                  <label class="block text-sm font-medium text-gray-600 mb-1">Precio Ofrecido</label>
                  <p class="text-gray-900 font-medium">
                    ${{ Number(visit.offered_price).toLocaleString() }}
                  </p>
                </div>

                <div v-if="visit.client_feedback" class="md:col-span-2">
                  <label class="block text-sm font-medium text-gray-600 mb-1">Feedback del Cliente</label>
                  <p class="text-gray-900 bg-gray-50 p-3 rounded-lg">{{ visit.client_feedback }}</p>
                </div>

                <div v-if="visit.agent_observations" class="md:col-span-2">
                  <label class="block text-sm font-medium text-gray-600 mb-1">Observaciones del Agente</label>
                  <p class="text-gray-900 bg-gray-50 p-3 rounded-lg">{{ visit.agent_observations }}</p>
                </div>

                <div v-if="visit.financing_discussed" class="md:col-span-2">
                  <label class="block text-sm font-medium text-gray-600 mb-1">Financiación Discutida</label>
                  <p class="text-gray-900 bg-gray-50 p-3 rounded-lg">{{ visit.financing_discussed }}</p>
                </div>
              </div>
            </div>

            <!-- Notes -->
            <div v-if="visit.notes" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
              <h3 class="text-lg font-semibold text-gray-900 mb-4">Notas</h3>
              <p class="text-gray-900 whitespace-pre-wrap">{{ visit.notes }}</p>
            </div>

            <!-- Follow-up Information -->
            <div v-if="visit.requires_follow_up" class="bg-yellow-50 rounded-lg border border-yellow-200 p-6">
              <h3 class="text-lg font-semibold text-yellow-800 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
                Seguimiento Requerido
              </h3>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div v-if="visit.follow_up_date">
                  <label class="block text-sm font-medium text-yellow-700 mb-1">Fecha de Seguimiento</label>
                  <p class="text-yellow-900">{{ formatDate(visit.follow_up_date) }}</p>
                </div>
                
                <div v-if="visit.follow_up_notes">
                  <label class="block text-sm font-medium text-yellow-700 mb-1">Notas de Seguimiento</label>
                  <p class="text-yellow-900">{{ visit.follow_up_notes }}</p>
                </div>
              </div>
            </div>

            <!-- Attachments -->
            <div v-if="visit.attachments && visit.attachments.length" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
              <h3 class="text-lg font-semibold text-gray-900 mb-4">Archivos Adjuntos</h3>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div 
                  v-for="(attachment, index) in visit.attachments" 
                  :key="index"
                  class="flex items-center gap-3 p-3 border border-gray-200 rounded-lg"
                >
                  <div class="flex-shrink-0">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                  </div>
                  <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 truncate">{{ attachment.original_name }}</p>
                    <p class="text-xs text-gray-500">{{ formatFileSize(attachment.size) }}</p>
                  </div>
                  <a 
                    :href="`/storage/${attachment.path}`" 
                    target="_blank"
                    class="text-blue-600 hover:text-blue-800"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                  </a>
                </div>
              </div>
            </div>
          </div>

          <!-- Right Column - Actions -->
          <div class="space-y-6">
            <!-- Quick Actions -->
            <div v-if="visit.status === 'scheduled'" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
              <h3 class="text-lg font-semibold text-gray-900 mb-4">Acciones Rápidas</h3>
              
              <div class="space-y-3">
                <button
                  @click="showCompleteModal = true"
                  class="w-full flex items-center justify-center gap-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg transition-colors"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                  Marcar como Completada
                </button>

                <button
                  @click="showCancelModal = true"
                  class="w-full flex items-center justify-center gap-2 bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition-colors"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                  </svg>
                  Cancelar Visita
                </button>

                <button
                  @click="markAsNoShow"
                  class="w-full flex items-center justify-center gap-2 bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg transition-colors"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                  </svg>
                  No Asistió
                </button>

                <button
                  @click="sendReminder"
                  class="w-full flex items-center justify-center gap-2 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors"
                >
                  <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM4 7h8M4 11h8M4 15h8"/>
                  </svg>
                  Enviar Recordatorio
                </button>
              </div>
            </div>

            <!-- Reminder Info -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
              <h3 class="text-lg font-semibold text-gray-900 mb-4">Recordatorio</h3>
              
              <div class="space-y-3">
                <div>
                  <label class="block text-sm font-medium text-gray-600 mb-1">Configurado para</label>
                  <p class="text-gray-900">{{ visit.reminder_hours_before }} horas antes</p>
                </div>
                
                <div v-if="visit.reminder_sent">
                  <label class="block text-sm font-medium text-gray-600 mb-1">Estado</label>
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    ✓ Enviado {{ formatDateTime(visit.reminder_sent_at) }}
                  </span>
                </div>
                <div v-else>
                  <label class="block text-sm font-medium text-gray-600 mb-1">Estado</label>
                  <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                    Pendiente
                  </span>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Complete Modal -->
        <div v-if="showCompleteModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
          <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Completar Visita</h3>
            <form @submit.prevent="completeVisit">
              <div class="space-y-4">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Duración Real (minutos)</label>
                  <input
                    v-model.number="completeForm.actual_duration"
                    type="number"
                    min="1"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  />
                </div>
                
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Resultado</label>
                  <select
                    v-model="completeForm.outcome"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  >
                    <option value="">Seleccionar resultado</option>
                    <option value="interested">Interesado</option>
                    <option value="not_interested">No Interesado</option>
                    <option value="needs_follow_up">Requiere Seguimiento</option>
                    <option value="offer_made">Oferta Realizada</option>
                    <option value="deal_closed">Trato Cerrado</option>
                  </select>
                </div>
              </div>
              
              <div class="flex items-center justify-end gap-3 mt-6">
                <button
                  type="button"
                  @click="showCompleteModal = false"
                  class="px-4 py-2 text-gray-600 border border-gray-300 rounded-md hover:bg-gray-50"
                >
                  Cancelar
                </button>
                <button
                  type="submit"
                  class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700"
                >
                  Completar
                </button>
              </div>
            </form>
          </div>
        </div>

        <!-- Cancel Modal -->
        <div v-if="showCancelModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
          <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Cancelar Visita</h3>
            <form @submit.prevent="cancelVisit">
              <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-1">Motivo de la cancelación *</label>
                <textarea
                  v-model="cancelForm.cancellation_reason"
                  rows="3"
                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                  required
                ></textarea>
              </div>
              
              <div class="flex items-center justify-end gap-3">
                <button
                  type="button"
                  @click="showCancelModal = false"
                  class="px-4 py-2 text-gray-600 border border-gray-300 rounded-md hover:bg-gray-50"
                >
                  Cerrar
                </button>
                <button
                  type="submit"
                  class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700"
                >
                  Cancelar Visita
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { Link, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
  visit: {
    type: Object,
    required: true
  }
})

const showCompleteModal = ref(false)
const showCancelModal = ref(false)

const completeForm = reactive({
  actual_duration: props.visit.estimated_duration || 60,
  outcome: ''
})

const cancelForm = reactive({
  cancellation_reason: ''
})

const typeLabel = (type) => {
  const labels = {
    showing: 'Visita',
    inspection: 'Inspección',
    evaluation: 'Evaluación',
    follow_up: 'Seguimiento',
    closing: 'Cierre'
  }
  return labels[type] || type
}

const priorityLabel = (priority) => {
  const labels = {
    low: 'Baja',
    medium: 'Media',
    high: 'Alta',
    urgent: 'Urgente'
  }
  return labels[priority] || priority
}

const statusLabel = (status) => {
  const labels = {
    scheduled: 'Programada',
    completed: 'Completada',
    cancelled: 'Cancelada',
    no_show: 'No Asistió'
  }
  return labels[status] || status
}

const outcomeLabel = (outcome) => {
  const labels = {
    interested: 'Interesado',
    not_interested: 'No Interesado',
    needs_follow_up: 'Requiere Seguimiento',
    offer_made: 'Oferta Realizada',
    deal_closed: 'Trato Cerrado'
  }
  return labels[outcome] || outcome
}

const getOutcomeColor = (outcome) => {
  const colors = {
    interested: 'bg-green-100 text-green-800',
    not_interested: 'bg-red-100 text-red-800',
    needs_follow_up: 'bg-yellow-100 text-yellow-800',
    offer_made: 'bg-blue-100 text-blue-800',
    deal_closed: 'bg-purple-100 text-purple-800'
  }
  return colors[outcome] || 'bg-gray-100 text-gray-800'
}

const formatDateTime = (dateTime) => {
  if (!dateTime) return ''
  const date = new Date(dateTime)
  return date.toLocaleDateString('es-ES', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
    hour12: true
  })
}

const formatDate = (date) => {
  if (!date) return ''
  return new Date(date).toLocaleDateString('es-ES', {
    weekday: 'long',
    year: 'numeric',
    month: 'long',
    day: 'numeric'
  })
}

const formatFileSize = (bytes) => {
  if (!bytes) return '0 Bytes'
  const k = 1024
  const sizes = ['Bytes', 'KB', 'MB', 'GB']
  const i = Math.floor(Math.log(bytes) / Math.log(k))
  return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
}

const completeVisit = () => {
  router.patch(route('visits.complete', props.visit.id), completeForm, {
    onSuccess: () => {
      showCompleteModal.value = false
    }
  })
}

const cancelVisit = () => {
  router.patch(route('visits.cancel', props.visit.id), cancelForm, {
    onSuccess: () => {
      showCancelModal.value = false
    }
  })
}

const markAsNoShow = () => {
  if (confirm('¿Marcar como no asistió?')) {
    router.patch(route('visits.no-show', props.visit.id))
  }
}

const sendReminder = () => {
  router.post(route('visits.send-reminder', props.visit.id), {}, {
    onSuccess: () => {
      // Success message handled by flash component
    }
  })
}
</script>