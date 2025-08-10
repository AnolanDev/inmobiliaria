<template>
  <AuthenticatedLayout :title="`Editar Visita - ${visit.property?.title || 'Visita'}`">
    <div class="py-12">
      <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="mb-8">
          <div class="flex items-center justify-between">
            <div>
              <h1 class="text-3xl font-bold text-gray-900">Editar Visita</h1>
              <p class="text-gray-600 mt-2">Modifica los detalles de la visita</p>
            </div>
            <Link
              :href="route('visits.show', visit.id)"
              class="text-gray-600 hover:text-gray-900 flex items-center gap-2"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
              </svg>
              Volver a Detalles
            </Link>
          </div>
        </div>

        <!-- Form -->
        <div class="bg-white shadow-sm rounded-lg border border-gray-200">
          <form @submit.prevent="submit" class="p-6 space-y-6">
            <!-- Basic Information -->
            <div>
              <h3 class="text-lg font-medium text-gray-900 mb-4">Información Básica</h3>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Visit Subject Type -->
                <div class="md:col-span-2">
                  <label class="block text-sm font-medium text-gray-700 mb-2">
                    Tipo de Destino *
                  </label>
                  <div class="flex gap-4">
                    <label class="flex items-center">
                      <input
                        v-model="visitSubjectType"
                        type="radio"
                        value="property"
                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                      />
                      <span class="ml-2 text-sm text-gray-700">Propiedad Individual</span>
                    </label>
                    <label class="flex items-center">
                      <input
                        v-model="visitSubjectType"
                        type="radio"
                        value="project"
                        class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                      />
                      <span class="ml-2 text-sm text-gray-700">Proyecto</span>
                    </label>
                  </div>
                </div>

                <!-- Property Selection -->
                <div v-if="visitSubjectType === 'property'">
                  <label class="block text-sm font-medium text-gray-700 mb-1">
                    Propiedad *
                  </label>
                  <select
                    v-model="form.property_id"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    :class="{ 'border-red-500': errors.property_id }"
                  >
                    <option value="">Seleccionar propiedad</option>
                    <option v-for="property in properties" :key="property.id" :value="property.id">
                      {{ property.title }} - {{ property.address }}
                    </option>
                  </select>
                  <p v-if="errors.property_id" class="text-red-500 text-sm mt-1">{{ errors.property_id }}</p>
                </div>

                <!-- Project Selection -->
                <div v-if="visitSubjectType === 'project'">
                  <label class="block text-sm font-medium text-gray-700 mb-1">
                    Proyecto *
                  </label>
                  <select
                    v-model="form.project_id"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    :class="{ 'border-red-500': errors.project_id }"
                  >
                    <option value="">Seleccionar proyecto</option>
                    <option v-for="project in projects" :key="project.id" :value="project.id">
                      {{ project.name }}
                    </option>
                  </select>
                  <p v-if="errors.project_id" class="text-red-500 text-sm mt-1">{{ errors.project_id }}</p>
                </div>

                <!-- Client Selection -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">
                    Cliente *
                  </label>
                  <select
                    v-model="form.client_id"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    :class="{ 'border-red-500': errors.client_id }"
                    required
                  >
                    <option value="">Seleccionar cliente</option>
                    <option v-for="client in clients" :key="client.id" :value="client.id">
                      {{ client.name }}
                    </option>
                  </select>
                  <p v-if="errors.client_id" class="text-red-500 text-sm mt-1">{{ errors.client_id }}</p>
                </div>

                <!-- Agent Selection -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">
                    Agente *
                  </label>
                  <select
                    v-model="form.agent_id"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    :class="{ 'border-red-500': errors.agent_id }"
                    required
                  >
                    <option value="">Seleccionar agente</option>
                    <option v-for="agent in agents" :key="agent.id" :value="agent.id">
                      {{ agent.name }}
                    </option>
                  </select>
                  <p v-if="errors.agent_id" class="text-red-500 text-sm mt-1">{{ errors.agent_id }}</p>
                </div>

                <!-- Visit Type -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">
                    Tipo de Visita *
                  </label>
                  <select
                    v-model="form.type"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    :class="{ 'border-red-500': errors.type }"
                    required
                  >
                    <option value="showing">Visita</option>
                    <option value="inspection">Inspección</option>
                    <option value="evaluation">Evaluación</option>
                    <option value="follow_up">Seguimiento</option>
                    <option value="closing">Cierre</option>
                  </select>
                  <p v-if="errors.type" class="text-red-500 text-sm mt-1">{{ errors.type }}</p>
                </div>

                <!-- Status (for completed visits) -->
                <div v-if="visit.status !== 'scheduled'">
                  <label class="block text-sm font-medium text-gray-700 mb-1">
                    Estado
                  </label>
                  <select
                    v-model="form.status"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    :class="{ 'border-red-500': errors.status }"
                  >
                    <option value="scheduled">Programada</option>
                    <option value="completed">Completada</option>
                    <option value="cancelled">Cancelada</option>
                    <option value="no_show">No Asistió</option>
                  </select>
                  <p v-if="errors.status" class="text-red-500 text-sm mt-1">{{ errors.status }}</p>
                </div>
              </div>
            </div>

            <!-- Schedule Information -->
            <div>
              <h3 class="text-lg font-medium text-gray-900 mb-4">Programación</h3>
              <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Scheduled Date & Time -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">
                    Fecha y Hora *
                  </label>
                  <input
                    v-model="form.scheduled_at"
                    type="datetime-local"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    :class="{ 'border-red-500': errors.scheduled_at }"
                    required
                  />
                  <p v-if="errors.scheduled_at" class="text-red-500 text-sm mt-1">{{ errors.scheduled_at }}</p>
                </div>

                <!-- Estimated Duration -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">
                    Duración Estimada (minutos)
                  </label>
                  <input
                    v-model.number="form.estimated_duration"
                    type="number"
                    min="15"
                    max="480"
                    step="15"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    :class="{ 'border-red-500': errors.estimated_duration }"
                  />
                  <p v-if="errors.estimated_duration" class="text-red-500 text-sm mt-1">{{ errors.estimated_duration }}</p>
                </div>

                <!-- Priority -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">
                    Prioridad *
                  </label>
                  <select
                    v-model="form.priority"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    :class="{ 'border-red-500': errors.priority }"
                    required
                  >
                    <option value="low">Baja</option>
                    <option value="medium">Media</option>
                    <option value="high">Alta</option>
                    <option value="urgent">Urgente</option>
                  </select>
                  <p v-if="errors.priority" class="text-red-500 text-sm mt-1">{{ errors.priority }}</p>
                </div>
              </div>
            </div>

            <!-- Contact Information -->
            <div>
              <h3 class="text-lg font-medium text-gray-900 mb-4">Información de Contacto</h3>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Client Phone -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">
                    Teléfono del Cliente
                  </label>
                  <input
                    v-model="form.client_phone"
                    type="tel"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    :class="{ 'border-red-500': errors.client_phone }"
                  />
                  <p v-if="errors.client_phone" class="text-red-500 text-sm mt-1">{{ errors.client_phone }}</p>
                </div>

                <!-- Client Email -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">
                    Email del Cliente
                  </label>
                  <input
                    v-model="form.client_email"
                    type="email"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    :class="{ 'border-red-500': errors.client_email }"
                  />
                  <p v-if="errors.client_email" class="text-red-500 text-sm mt-1">{{ errors.client_email }}</p>
                </div>
              </div>
            </div>

            <!-- Results (if completed) -->
            <div v-if="form.status === 'completed'">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Resultado de la Visita</h3>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Actual Duration -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">
                    Duración Real (minutos)
                  </label>
                  <input
                    v-model.number="form.actual_duration"
                    type="number"
                    min="1"
                    max="480"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    :class="{ 'border-red-500': errors.actual_duration }"
                  />
                  <p v-if="errors.actual_duration" class="text-red-500 text-sm mt-1">{{ errors.actual_duration }}</p>
                </div>

                <!-- Outcome -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">
                    Resultado
                  </label>
                  <select
                    v-model="form.outcome"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    :class="{ 'border-red-500': errors.outcome }"
                  >
                    <option value="">Seleccionar resultado</option>
                    <option value="interested">Interesado</option>
                    <option value="not_interested">No Interesado</option>
                    <option value="needs_follow_up">Requiere Seguimiento</option>
                    <option value="offer_made">Oferta Realizada</option>
                    <option value="deal_closed">Trato Cerrado</option>
                  </select>
                  <p v-if="errors.outcome" class="text-red-500 text-sm mt-1">{{ errors.outcome }}</p>
                </div>

                <!-- Client Rating -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">
                    Calificación del Cliente (1-5)
                  </label>
                  <input
                    v-model.number="form.client_rating"
                    type="number"
                    min="1"
                    max="5"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    :class="{ 'border-red-500': errors.client_rating }"
                  />
                  <p v-if="errors.client_rating" class="text-red-500 text-sm mt-1">{{ errors.client_rating }}</p>
                </div>

                <!-- Offered Price -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">
                    Precio Ofrecido
                  </label>
                  <input
                    v-model.number="form.offered_price"
                    type="number"
                    min="0"
                    step="0.01"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    :class="{ 'border-red-500': errors.offered_price }"
                  />
                  <p v-if="errors.offered_price" class="text-red-500 text-sm mt-1">{{ errors.offered_price }}</p>
                </div>

                <!-- Client Feedback -->
                <div class="md:col-span-2">
                  <label class="block text-sm font-medium text-gray-700 mb-1">
                    Feedback del Cliente
                  </label>
                  <textarea
                    v-model="form.client_feedback"
                    rows="3"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    :class="{ 'border-red-500': errors.client_feedback }"
                  ></textarea>
                  <p v-if="errors.client_feedback" class="text-red-500 text-sm mt-1">{{ errors.client_feedback }}</p>
                </div>

                <!-- Agent Observations -->
                <div class="md:col-span-2">
                  <label class="block text-sm font-medium text-gray-700 mb-1">
                    Observaciones del Agente
                  </label>
                  <textarea
                    v-model="form.agent_observations"
                    rows="3"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    :class="{ 'border-red-500': errors.agent_observations }"
                  ></textarea>
                  <p v-if="errors.agent_observations" class="text-red-500 text-sm mt-1">{{ errors.agent_observations }}</p>
                </div>

                <!-- Financing Discussed -->
                <div class="md:col-span-2">
                  <label class="block text-sm font-medium text-gray-700 mb-1">
                    Financiación Discutida
                  </label>
                  <textarea
                    v-model="form.financing_discussed"
                    rows="2"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    :class="{ 'border-red-500': errors.financing_discussed }"
                  ></textarea>
                  <p v-if="errors.financing_discussed" class="text-red-500 text-sm mt-1">{{ errors.financing_discussed }}</p>
                </div>
              </div>
            </div>

            <!-- Follow-up Settings -->
            <div>
              <h3 class="text-lg font-medium text-gray-900 mb-4">Seguimiento</h3>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Requires Follow-up -->
                <div class="md:col-span-2">
                  <label class="flex items-center">
                    <input
                      v-model="form.requires_follow_up"
                      type="checkbox"
                      class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50"
                    />
                    <span class="ml-2 text-sm text-gray-700">Requiere seguimiento</span>
                  </label>
                </div>

                <div v-if="form.requires_follow_up">
                  <label class="block text-sm font-medium text-gray-700 mb-1">
                    Fecha de Seguimiento
                  </label>
                  <input
                    v-model="form.follow_up_date"
                    type="date"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    :class="{ 'border-red-500': errors.follow_up_date }"
                  />
                  <p v-if="errors.follow_up_date" class="text-red-500 text-sm mt-1">{{ errors.follow_up_date }}</p>
                </div>

                <div v-if="form.requires_follow_up" class="md:col-span-2">
                  <label class="block text-sm font-medium text-gray-700 mb-1">
                    Notas de Seguimiento
                  </label>
                  <textarea
                    v-model="form.follow_up_notes"
                    rows="2"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    :class="{ 'border-red-500': errors.follow_up_notes }"
                  ></textarea>
                  <p v-if="errors.follow_up_notes" class="text-red-500 text-sm mt-1">{{ errors.follow_up_notes }}</p>
                </div>
              </div>
            </div>

            <!-- Reminder Settings -->
            <div v-if="form.status === 'scheduled'">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Recordatorio</h3>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Reminder Hours Before -->
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">
                    Enviar recordatorio (horas antes)
                  </label>
                  <select
                    v-model.number="form.reminder_hours_before"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    :class="{ 'border-red-500': errors.reminder_hours_before }"
                  >
                    <option value="1">1 hora</option>
                    <option value="2">2 horas</option>
                    <option value="4">4 horas</option>
                    <option value="8">8 horas</option>
                    <option value="24">1 día</option>
                    <option value="48">2 días</option>
                    <option value="72">3 días</option>
                    <option value="168">1 semana</option>
                  </select>
                  <p v-if="errors.reminder_hours_before" class="text-red-500 text-sm mt-1">{{ errors.reminder_hours_before }}</p>
                </div>
              </div>
            </div>

            <!-- Additional Participants -->
            <div>
              <h3 class="text-lg font-medium text-gray-900 mb-4">Participantes Adicionales</h3>
              <div v-for="(participant, index) in form.additional_participants" :key="index" class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4 p-4 border border-gray-200 rounded-lg">
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Nombre</label>
                  <input
                    v-model="participant.name"
                    type="text"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  />
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Teléfono</label>
                  <input
                    v-model="participant.phone"
                    type="tel"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  />
                </div>
                <div>
                  <label class="block text-sm font-medium text-gray-700 mb-1">Rol</label>
                  <input
                    v-model="participant.role"
                    type="text"
                    placeholder="Ej: Cónyuge, Asesor"
                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                  />
                </div>
                <div class="flex items-end">
                  <button
                    type="button"
                    @click="removeParticipant(index)"
                    class="text-red-600 hover:text-red-800 p-2"
                  >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                  </button>
                </div>
              </div>
              
              <button
                type="button"
                @click="addParticipant"
                class="flex items-center gap-2 text-blue-600 hover:text-blue-800 text-sm font-medium"
              >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Agregar Participante
              </button>
            </div>

            <!-- Notes -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                Notas
              </label>
              <textarea
                v-model="form.notes"
                rows="4"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                :class="{ 'border-red-500': errors.notes }"
                placeholder="Notas adicionales sobre la visita..."
              ></textarea>
              <p v-if="errors.notes" class="text-red-500 text-sm mt-1">{{ errors.notes }}</p>
            </div>

            <!-- File Attachments -->
            <div>
              <label class="block text-sm font-medium text-gray-700 mb-1">
                Archivos Adjuntos Adicionales
              </label>
              <input
                type="file"
                multiple
                @change="handleFileUpload"
                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.gif"
              />
              <p class="text-xs text-gray-500 mt-1">
                Máximo 10 archivos adicionales, 10MB cada uno. Los archivos existentes se mantendrán.
              </p>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end gap-4 pt-6 border-t border-gray-200">
              <Link
                :href="route('visits.show', visit.id)"
                class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 font-medium"
              >
                Cancelar
              </Link>
              <button
                type="submit"
                :disabled="processing"
                class="px-6 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 font-medium disabled:opacity-50 flex items-center gap-2"
              >
                <svg v-if="processing" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                {{ processing ? 'Guardando...' : 'Actualizar Visita' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, reactive, computed, onMounted, watch } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
  visit: {
    type: Object,
    required: true
  },
  properties: {
    type: Array,
    required: true
  },
  projects: {
    type: Array,
    required: true
  },
  clients: {
    type: Array,
    required: true
  },
  agents: {
    type: Array,
    required: true
  }
})

// Format datetime for input
const formatDateTimeForInput = (dateTime) => {
  if (!dateTime) return ''
  const date = new Date(dateTime)
  date.setMinutes(date.getMinutes() - date.getTimezoneOffset())
  return date.toISOString().slice(0, 16)
}

// Determine initial visit subject type
const visitSubjectType = ref(
  props.visit.property_id ? 'property' : 
  props.visit.project_id ? 'project' : 'property'
)

// Watch for visit subject type changes
watch(visitSubjectType, (newType) => {
  if (newType === 'property') {
    form.project_id = ''
  } else if (newType === 'project') {
    form.property_id = ''
  }
})

const form = useForm({
  property_id: props.visit.property_id,
  project_id: props.visit.project_id,
  client_id: props.visit.client_id,
  agent_id: props.visit.agent_id,
  type: props.visit.type || 'showing',
  priority: props.visit.priority || 'medium',
  status: props.visit.status,
  scheduled_at: formatDateTimeForInput(props.visit.scheduled_at),
  estimated_duration: props.visit.estimated_duration || 60,
  actual_duration: props.visit.actual_duration || null,
  client_phone: props.visit.client_phone || '',
  client_email: props.visit.client_email || '',
  reminder_hours_before: props.visit.reminder_hours_before || 24,
  outcome: props.visit.outcome || '',
  client_feedback: props.visit.client_feedback || '',
  agent_observations: props.visit.agent_observations || '',
  client_rating: props.visit.client_rating || null,
  offered_price: props.visit.offered_price || null,
  financing_discussed: props.visit.financing_discussed || '',
  requires_follow_up: props.visit.requires_follow_up || false,
  follow_up_date: props.visit.follow_up_date || '',
  follow_up_notes: props.visit.follow_up_notes || '',
  additional_participants: props.visit.additional_participants || [],
  notes: props.visit.notes || '',
  attachments: []
})

const { errors, processing } = form

const addParticipant = () => {
  form.additional_participants.push({
    name: '',
    phone: '',
    role: ''
  })
}

const removeParticipant = (index) => {
  form.additional_participants.splice(index, 1)
}

const handleFileUpload = (event) => {
  const files = Array.from(event.target.files)
  form.attachments = files
}

const submit = () => {
  form.patch(route('visits.update', props.visit.id), {
    onSuccess: () => {
      // Success handled by redirect
    },
    onError: (errors) => {
      console.log('Validation errors:', errors)
    }
  })
}
</script>