<template>
  <Head title="Dashboard" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="text-xl font-semibold leading-tight text-gray-800">
          Dashboard
        </h2>
        
        <!-- Period Filter -->
        <div class="flex items-center space-x-2">
          <label class="text-sm text-gray-600">Período:</label>
          <select
            v-model="selectedPeriod"
            @change="changePeriod"
            class="text-sm border-gray-300 rounded-md focus:border-green-500 focus:ring-green-500"
          >
            <option value="7">Últimos 7 días</option>
            <option value="30">Últimos 30 días</option>
            <option value="90">Últimos 90 días</option>
            <option value="180">Últimos 6 meses</option>
            <option value="365">Último año</option>
            <option value="ytd">Año actual</option>
            <option value="mtd">Mes actual</option>
          </select>
        </div>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        
        <!-- Inventario & Propiedades -->
        <div class="mb-8">
          <div class="flex items-center mb-4">
            <div class="flex items-center justify-center w-8 h-8 bg-blue-100 rounded-lg mr-3">
              <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
              </svg>
            </div>
            <h2 class="text-lg font-semibold text-gray-900">Inventario & Propiedades</h2>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <MetricCard
              title="Propiedades Totales"
              :value="metrics.properties.total"
              :change="metrics.properties.change_percentage"
              subtitle="Nuevas este período"
              icon="home"
              color="blue"
              :clickable="true"
              @click="router.get(route('properties.index'))"
            />
            <MetricCard
              title="Proyectos"
              :value="metrics.projects.total"
              icon="building"
              color="indigo"
              :subtitle="`${metrics.projects.disponible} disponibles`"
              :clickable="true"
              @click="router.get(route('projects.index'))"
            />
            <MetricCard
              title="Ventas Totales"
              :value="metrics.sales.total_sales"
              :change="metrics.sales.change_percentage"
              :subtitle="`Transacciones: ${metrics.sales.sales_count}`"
              icon="currency"
              color="green"
              value-type="currency"
              :clickable="true"
              @click="router.get(route('visits.index', { outcome: 'deal_closed' }))"
            />
          </div>
        </div>

        <!-- Clientes & Relaciones -->
        <div class="mb-8">
          <div class="flex items-center mb-4">
            <div class="flex items-center justify-center w-8 h-8 bg-green-100 rounded-lg mr-3">
              <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"/>
              </svg>
            </div>
            <h2 class="text-lg font-semibold text-gray-900">Clientes & Relaciones</h2>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <MetricCard
              title="Clientes Activos"
              :value="metrics.clients.total"
              :change="metrics.clients.change_percentage"
              subtitle="Nuevos este período"
              icon="users"
              color="green"
              :clickable="true"
              @click="router.get(route('clients.index'))"
            />
            <MetricCard
              title="Agentes Activos"
              :value="metrics.agents.active"
              icon="user"
              color="blue"
              :subtitle="`${metrics.agents.total} total`"
              :clickable="true"
              @click="router.get(route('agents.index'))"
            />
            <MetricCard
              title="Tasa Conversión"
              :value="metrics.visits.conversion_rate"
              icon="chart"
              color="green"
              value-type="percentage"
              subtitle="Visitas → Ventas"
              :clickable="true"
              @click="router.get(route('visits.index', { outcome: 'deal_closed' }))"
            />
            <MetricCard
              title="Seguimientos"
              :value="metrics.sales.follow_up_required"
              icon="eye"
              color="yellow"
              subtitle="Pendientes"
              :clickable="true"
              @click="router.get(route('visits.index', { follow_up: true }))"
            />
          </div>
        </div>

        <!-- Actividades & Visitas -->
        <div class="mb-8">
          <div class="flex items-center mb-4">
            <div class="flex items-center justify-center w-8 h-8 bg-purple-100 rounded-lg mr-3">
              <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
              </svg>
            </div>
            <h2 class="text-lg font-semibold text-gray-900">Actividades & Visitas</h2>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 gap-6">
            <MetricCard
              title="Visitas Programadas"
              :value="metrics.visits.scheduled"
              :change="metrics.visits.change_percentage"
              :subtitle="`Hoy: ${metrics.visits.today}`"
              icon="calendar"
              color="purple"
              :clickable="true"
              @click="router.get(route('visits.index', { status: 'scheduled' }))"
            />
            <MetricCard
              title="Visitas Vencidas"
              :value="metrics.visits.overdue"
              icon="clock"
              color="red"
              subtitle="Requieren atención"
              :clickable="true"
              @click="router.get(route('visits.index', { overdue: true }))"
            />
          </div>
        </div>

        <!-- Marketing & Leads -->
        <div class="mb-8">
          <div class="flex items-center mb-4">
            <div class="flex items-center justify-center w-8 h-8 bg-orange-100 rounded-lg mr-3">
              <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
              </svg>
            </div>
            <h2 class="text-lg font-semibold text-gray-900">Marketing & Leads</h2>
          </div>
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <MetricCard
              title="Leads Totales"
              :value="metrics.marketing.leads.total"
              :change="metrics.marketing.leads.change_percentage"
              subtitle="Nuevos este período"
              icon="users"
              color="purple"
              :clickable="true"
              @click="router.get(route('leads.index'))"
            />
            <MetricCard
              title="Campaña Email"
              :value="metrics.marketing.email_campaigns.total"
              :change="metrics.marketing.email_campaigns.change_percentage"
              :subtitle="`${metrics.marketing.email_campaigns.sent} enviadas`"
              icon="mail"
              color="blue"
              :clickable="true"
              @click="router.get(route('email-campaigns.index'))"
            />
            <MetricCard
              title="Tasa Apertura"
              :value="metrics.marketing.email_campaigns.avg_open_rate"
              icon="mail-opened"
              color="green"
              value-type="percentage"
              subtitle="Promedio campañas"
              :clickable="true"
              @click="router.get(route('email-campaigns.index'))"
            />
            <MetricCard
              title="Conversión Leads"
              :value="metrics.marketing.leads.conversion_rate"
              icon="trending-up"
              color="orange"
              value-type="percentage"
              subtitle="Leads → Clientes"
              :clickable="true"
              @click="router.get(route('leads.index', { filter: 'converted' }))"
            />
          </div>
        </div>

        <!-- Charts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
          <ChartWidget
            title="Timeline de Visitas"
            type="line"
            :labels="charts.visits_timeline.labels"
            :data="charts.visits_timeline.data"
            :show-period-filter="true"
            :selected-period="selectedPeriod"
            @period-change="changePeriod"
          />
          <ChartWidget
            title="Propiedades por Tipo"
            type="pie"
            :labels="charts.properties_by_type.labels"
            :data="charts.properties_by_type.data"
          />
        </div>

        <!-- Funnel and Performance Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
          <ChartWidget
            title="Embudo de Ventas"
            type="bar"
            :labels="charts.sales_funnel.labels"
            :data="charts.sales_funnel.data"
            :show-legend="false"
          />
          <ChartWidget
            title="Rendimiento de Agentes"
            type="bar"
            :labels="charts.agent_performance.labels"
            :data="charts.agent_performance.data"
            :show-legend="false"
          />
        </div>

        <!-- Marketing Charts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
          <ChartWidget
            title="Embudo de Leads"
            type="bar"
            :labels="charts.leads_funnel.labels"
            :data="charts.leads_funnel.data"
            :show-legend="false"
          />
          <ChartWidget
            v-if="charts.marketing_performance.labels.length > 0"
            title="Performance Email Marketing"
            type="line"
            :labels="charts.marketing_performance.labels"
            :data="[
              {
                label: 'Tasa Apertura (%)',
                data: charts.marketing_performance.open_rates,
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
              },
              {
                label: 'Tasa Click (%)',
                data: charts.marketing_performance.click_rates,
                borderColor: 'rgb(16, 185, 129)',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
              }
            ]"
            :multi-dataset="true"
          />
        </div>

        <!-- Activity and Alerts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <RecentActivity
            :activity="recentActivity"
            @view-visit="viewVisit"
            @view-client="viewClient"
            @view-property="viewProperty"
          />
          <AlertsWidget
            :alerts="alerts"
            @view-visit="viewVisit"
            @view-property="viewProperty"
            @dismiss-all="dismissAllAlerts"
            @view-all-alerts="viewAllAlerts"
          />
        </div>

      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref } from 'vue'
import { Head, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import MetricCard from '@/Components/Dashboard/MetricCard.vue'
import ChartWidget from '@/Components/Dashboard/ChartWidget.vue'
import RecentActivity from '@/Components/Dashboard/RecentActivity.vue'
import AlertsWidget from '@/Components/Dashboard/AlertsWidget.vue'

const props = defineProps({
  metrics: {
    type: Object,
    required: true
  },
  charts: {
    type: Object,
    required: true
  },
  recentActivity: {
    type: Object,
    required: true
  },
  alerts: {
    type: Object,
    required: true
  },
  currentPeriod: {
    type: String,
    default: '30'
  }
})

const selectedPeriod = ref(props.currentPeriod)

const changePeriod = (period) => {
  if (typeof period === 'string') {
    selectedPeriod.value = period
  } else {
    selectedPeriod.value = period.target.value
  }
  
  router.get(route('dashboard'), { period: selectedPeriod.value }, {
    preserveState: true,
    replace: true
  })
}

const viewVisit = (visit) => {
  router.get(route('visits.show', visit.id))
}

const viewClient = (client) => {
  router.get(route('clients.show', client.id))
}

const viewProperty = (property) => {
  router.get(route('properties.show', property.id))
}

const dismissAllAlerts = () => {
  // Implement dismiss all alerts functionality
  console.log('Dismiss all alerts')
}

const viewAllAlerts = () => {
  // Navigate to alerts page or show modal
  console.log('View all alerts')
}
</script>
