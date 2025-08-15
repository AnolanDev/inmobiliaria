<template>
  <Head :title="`Editar: ${campaign.name}`" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center space-x-4">
        <Link :href="route('campaigns.show', campaign.id)" class="text-gray-500 hover:text-gray-700">
          <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
          </svg>
        </Link>
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Editar: {{ campaign.name }}
        </h2>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <form @submit.prevent="submit" class="p-6 space-y-6">
            <!-- Basic Information -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
              <!-- Name -->
              <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                  Nombre de la Campaña *
                </label>
                <input
                  id="name"
                  v-model="form.name"
                  type="text"
                  class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-green-500 focus:border-green-500"
                  :class="{ 'border-red-500': form.errors.name }"
                  required
                />
                <p v-if="form.errors.name" class="mt-1 text-sm text-red-600">
                  {{ form.errors.name }}
                </p>
              </div>

              <!-- Type -->
              <div>
                <label for="type" class="block text-sm font-medium text-gray-700 mb-1">
                  Tipo de Campaña *
                </label>
                <select
                  id="type"
                  v-model="form.type"
                  class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-green-500 focus:border-green-500"
                  :class="{ 'border-red-500': form.errors.type }"
                  required
                >
                  <option v-for="(label, value) in types" :key="value" :value="value">
                    {{ label }}
                  </option>
                </select>
                <p v-if="form.errors.type" class="mt-1 text-sm text-red-600">
                  {{ form.errors.type }}
                </p>
              </div>

              <!-- Status -->
              <div>
                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">
                  Estado *
                </label>
                <select
                  id="status"
                  v-model="form.status"
                  class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-green-500 focus:border-green-500"
                  :class="{ 'border-red-500': form.errors.status }"
                  required
                >
                  <option v-for="(label, value) in statuses" :key="value" :value="value">
                    {{ label }}
                  </option>
                </select>
                <p v-if="form.errors.status" class="mt-1 text-sm text-red-600">
                  {{ form.errors.status }}
                </p>
              </div>

              <!-- Budget -->
              <div>
                <label for="budget" class="block text-sm font-medium text-gray-700 mb-1">
                  Presupuesto
                </label>
                <div class="relative">
                  <span class="absolute left-3 top-2 text-gray-500">$</span>
                  <input
                    id="budget"
                    v-model="form.budget"
                    type="number"
                    step="0.01"
                    min="0"
                    class="w-full border border-gray-300 rounded-md pl-8 pr-3 py-2 focus:ring-green-500 focus:border-green-500"
                    :class="{ 'border-red-500': form.errors.budget }"
                  />
                </div>
                <p v-if="form.errors.budget" class="mt-1 text-sm text-red-600">
                  {{ form.errors.budget }}
                </p>
              </div>

              <!-- Spent -->
              <div>
                <label for="spent" class="block text-sm font-medium text-gray-700 mb-1">
                  Gastado
                </label>
                <div class="relative">
                  <span class="absolute left-3 top-2 text-gray-500">$</span>
                  <input
                    id="spent"
                    v-model="form.spent"
                    type="number"
                    step="0.01"
                    min="0"
                    class="w-full border border-gray-300 rounded-md pl-8 pr-3 py-2 focus:ring-green-500 focus:border-green-500"
                    :class="{ 'border-red-500': form.errors.spent }"
                  />
                </div>
                <p v-if="form.errors.spent" class="mt-1 text-sm text-red-600">
                  {{ form.errors.spent }}
                </p>
              </div>

              <!-- Start Date -->
              <div>
                <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">
                  Fecha de Inicio
                </label>
                <input
                  id="start_date"
                  v-model="form.start_date"
                  type="date"
                  class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-green-500 focus:border-green-500"
                  :class="{ 'border-red-500': form.errors.start_date }"
                />
                <p v-if="form.errors.start_date" class="mt-1 text-sm text-red-600">
                  {{ form.errors.start_date }}
                </p>
              </div>

              <!-- End Date -->
              <div>
                <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">
                  Fecha de Finalización
                </label>
                <input
                  id="end_date"
                  v-model="form.end_date"
                  type="date"
                  class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-green-500 focus:border-green-500"
                  :class="{ 'border-red-500': form.errors.end_date }"
                  :min="form.start_date"
                />
                <p v-if="form.errors.end_date" class="mt-1 text-sm text-red-600">
                  {{ form.errors.end_date }}
                </p>
              </div>
            </div>

            <!-- Description -->
            <div>
              <label for="description" class="block text-sm font-medium text-gray-700 mb-1">
                Descripción
              </label>
              <textarea
                id="description"
                v-model="form.description"
                rows="3"
                class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-green-500 focus:border-green-500"
                :class="{ 'border-red-500': form.errors.description }"
              />
              <p v-if="form.errors.description" class="mt-1 text-sm text-red-600">
                {{ form.errors.description }}
              </p>
            </div>

            <!-- Metrics -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 pt-4 border-t border-gray-200">
              <div>
                <label for="impressions" class="block text-sm font-medium text-gray-700 mb-1">
                  Impresiones
                </label>
                <input
                  id="impressions"
                  v-model="form.impressions"
                  type="number"
                  min="0"
                  class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-green-500 focus:border-green-500"
                />
              </div>

              <div>
                <label for="clicks" class="block text-sm font-medium text-gray-700 mb-1">
                  Clics
                </label>
                <input
                  id="clicks"
                  v-model="form.clicks"
                  type="number"
                  min="0"
                  class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-green-500 focus:border-green-500"
                />
              </div>

              <div>
                <label for="conversions" class="block text-sm font-medium text-gray-700 mb-1">
                  Conversiones
                </label>
                <input
                  id="conversions"
                  v-model="form.conversions"
                  type="number"
                  min="0"
                  class="w-full border border-gray-300 rounded-md px-3 py-2 focus:ring-green-500 focus:border-green-500"
                />
              </div>

              <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                  Tasa de Conversión
                </label>
                <div class="w-full border border-gray-300 rounded-md px-3 py-2 bg-gray-50 text-gray-600">
                  {{ conversionRate }}%
                </div>
              </div>
            </div>

            <!-- Form Actions -->
            <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-200">
              <Link
                :href="route('campaigns.show', campaign.id)"
                class="inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50"
              >
                Cancelar
              </Link>
              <button
                type="submit"
                :disabled="form.processing"
                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 disabled:opacity-50"
              >
                {{ form.processing ? 'Guardando...' : 'Guardar Cambios' }}
              </button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { computed } from 'vue'
import { Head, Link, useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
  campaign: Object,
  types: Object,
  statuses: Object
})

// Form data
const form = useForm({
  name: props.campaign.name,
  type: props.campaign.type,
  status: props.campaign.status,
  description: props.campaign.description,
  budget: props.campaign.budget,
  spent: props.campaign.spent,
  start_date: props.campaign.start_date,
  end_date: props.campaign.end_date,
  impressions: props.campaign.impressions,
  clicks: props.campaign.clicks,
  conversions: props.campaign.conversions
})

// Computed conversion rate
const conversionRate = computed(() => {
  if (!form.clicks || form.clicks === 0) return 0
  return ((form.conversions / form.clicks) * 100).toFixed(2)
})

// Submit form
const submit = () => {
  form.patch(route('campaigns.update', props.campaign.id))
}
</script>