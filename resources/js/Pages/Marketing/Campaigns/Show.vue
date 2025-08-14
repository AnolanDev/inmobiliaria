<template>
  <Head :title="`Campaña: ${campaign.name}`" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
          <Link :href="route('campaigns.index')" class="text-gray-500 hover:text-gray-700">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
          </Link>
          <div>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
              {{ campaign.name }}
            </h2>
            <p class="text-sm text-gray-600">{{ campaign.formatted_type }} • {{ campaign.formatted_status }}</p>
          </div>
        </div>
        <div class="flex items-center space-x-3">
          <Link
            :href="route('campaigns.edit', campaign.id)"
            class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700"
          >
            Editar
          </Link>
        </div>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <!-- Campaign Info & Metrics -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <!-- Campaign Details -->
          <div class="lg:col-span-2 bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-6">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Detalles de la Campaña</h3>
              
              <div class="space-y-4">
                <div v-if="campaign.description">
                  <dt class="text-sm font-medium text-gray-500">Descripción</dt>
                  <dd class="mt-1 text-sm text-gray-900">{{ campaign.description }}</dd>
                </div>
                
                <div class="grid grid-cols-2 gap-4">
                  <div>
                    <dt class="text-sm font-medium text-gray-500">Tipo</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ campaign.formatted_type }}</dd>
                  </div>
                  <div>
                    <dt class="text-sm font-medium text-gray-500">Estado</dt>
                    <dd class="mt-1">
                      <span :class="getStatusColor(campaign.status)" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                        {{ campaign.formatted_status }}
                      </span>
                    </dd>
                  </div>
                </div>

                <div v-if="campaign.start_date || campaign.end_date" class="grid grid-cols-2 gap-4">
                  <div v-if="campaign.start_date">
                    <dt class="text-sm font-medium text-gray-500">Fecha de Inicio</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ formatDate(campaign.start_date) }}</dd>
                  </div>
                  <div v-if="campaign.end_date">
                    <dt class="text-sm font-medium text-gray-500">Fecha de Fin</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ formatDate(campaign.end_date) }}</dd>
                  </div>
                </div>

                <div v-if="campaign.budget">
                  <dt class="text-sm font-medium text-gray-500">Presupuesto</dt>
                  <dd class="mt-1 text-sm text-gray-900">
                    ${{ formatCurrency(campaign.spent) }} / ${{ formatCurrency(campaign.budget) }}
                    <div class="w-full bg-gray-200 rounded-full h-2 mt-2">
                      <div 
                        class="bg-blue-600 h-2 rounded-full transition-all duration-300"
                        :style="{ width: getBudgetPercentage() + '%' }"
                      ></div>
                    </div>
                  </dd>
                </div>

                <div>
                  <dt class="text-sm font-medium text-gray-500">Creado por</dt>
                  <dd class="mt-1 text-sm text-gray-900">{{ campaign.creator?.name }}</dd>
                </div>
              </div>
            </div>
          </div>

          <!-- Metrics Card -->
          <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
            <div class="p-6">
              <h3 class="text-lg font-medium text-gray-900 mb-4">Métricas</h3>
              
              <div class="space-y-4">
                <div class="text-center p-4 bg-blue-50 rounded-lg">
                  <div class="text-2xl font-bold text-blue-600">{{ metrics.total_leads }}</div>
                  <div class="text-sm text-blue-900">Total Leads</div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                  <div class="text-center p-3 bg-green-50 rounded-lg">
                    <div class="text-lg font-bold text-green-600">{{ metrics.new_leads }}</div>
                    <div class="text-xs text-green-900">Nuevos</div>
                  </div>
                  <div class="text-center p-3 bg-yellow-50 rounded-lg">
                    <div class="text-lg font-bold text-yellow-600">{{ metrics.qualified_leads }}</div>
                    <div class="text-xs text-yellow-900">Calificados</div>
                  </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                  <div class="text-center p-3 bg-purple-50 rounded-lg">
                    <div class="text-lg font-bold text-purple-600">{{ metrics.converted_leads }}</div>
                    <div class="text-xs text-purple-900">Convertidos</div>
                  </div>
                  <div class="text-center p-3 bg-indigo-50 rounded-lg">
                    <div class="text-lg font-bold text-indigo-600">{{ metrics.conversion_rate }}%</div>
                    <div class="text-xs text-indigo-900">Conversión</div>
                  </div>
                </div>

                <div v-if="campaign.budget" class="text-center p-3 bg-gray-50 rounded-lg">
                  <div class="text-lg font-bold text-gray-600">${{ formatCurrency(metrics.remaining_budget) }}</div>
                  <div class="text-xs text-gray-900">Presupuesto Restante</div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Campaign Leads -->
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <div class="p-6">
            <div class="flex items-center justify-between mb-4">
              <h3 class="text-lg font-medium text-gray-900">Leads de la Campaña</h3>
              <Link
                :href="route('leads.create', { campaign_id: campaign.id })"
                class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-blue-700 bg-blue-100 hover:bg-blue-200"
              >
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Agregar Lead
              </Link>
            </div>

            <div v-if="campaign.leads && campaign.leads.length > 0" class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                  <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Lead</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Agente</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <tr v-for="lead in campaign.leads" :key="lead.id" class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                      <div>
                        <div class="text-sm font-medium text-gray-900">{{ lead.full_name }}</div>
                        <div class="text-sm text-gray-500">{{ lead.email }}</div>
                      </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                      <span :class="getLeadStatusColor(lead.status)" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium">
                        {{ lead.formatted_status }}
                      </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                      {{ lead.assigned_agent?.name || 'Sin asignar' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                      {{ formatDate(lead.created_at) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                      <Link
                        :href="route('leads.show', lead.id)"
                        class="text-blue-600 hover:text-blue-900"
                      >
                        Ver
                      </Link>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <div v-else class="text-center py-8">
              <svg class="mx-auto h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
              </svg>
              <h3 class="mt-2 text-sm font-medium text-gray-900">No hay leads</h3>
              <p class="mt-1 text-sm text-gray-500">Esta campaña aún no ha generado leads.</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
  campaign: Object,
  metrics: Object
})

const getStatusColor = (status) => {
  const colors = {
    'draft': 'bg-gray-100 text-gray-800',
    'active': 'bg-green-100 text-green-800',
    'paused': 'bg-yellow-100 text-yellow-800',
    'completed': 'bg-blue-100 text-blue-800'
  }
  return colors[status] || 'bg-gray-100 text-gray-800'
}

const getLeadStatusColor = (status) => {
  const colors = {
    'new': 'bg-blue-100 text-blue-800',
    'contacted': 'bg-yellow-100 text-yellow-800',
    'qualified': 'bg-green-100 text-green-800',
    'converted': 'bg-purple-100 text-purple-800',
    'lost': 'bg-red-100 text-red-800'
  }
  return colors[status] || 'bg-gray-100 text-gray-800'
}

const formatDate = (date) => {
  return new Intl.DateTimeFormat('es-CO', {
    year: 'numeric',
    month: 'short',
    day: 'numeric'
  }).format(new Date(date))
}

const formatCurrency = (amount) => {
  return new Intl.NumberFormat('es-CO').format(amount)
}

const getBudgetPercentage = () => {
  if (!props.campaign.budget || props.campaign.budget === 0) return 0
  return Math.min((props.campaign.spent / props.campaign.budget) * 100, 100)
}
</script>