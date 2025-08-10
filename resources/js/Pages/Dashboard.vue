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
            class="text-sm border-gray-300 rounded-md focus:border-blue-500 focus:ring-blue-500"
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
        
        <!-- Key Metrics Row -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
          <MetricCard
            title="Propiedades Totales"
            :value="metrics.properties.total"
            :change="metrics.properties.change_percentage"
            subtitle="Nuevas este período"
            icon="home"
            color="blue"
          />
          <MetricCard
            title="Clientes Activos"
            :value="metrics.clients.total"
            :change="metrics.clients.change_percentage"
            subtitle="Nuevos este período"
            icon="users"
            color="green"
          />
          <MetricCard
            title="Visitas Programadas"
            :value="metrics.visits.scheduled"
            :change="metrics.visits.change_percentage"
            :subtitle="`Hoy: ${metrics.visits.today}`"
            icon="calendar"
            color="purple"
          />
          <MetricCard
            title="Ventas Totales"
            :value="metrics.sales.total_sales"
            :change="metrics.sales.change_percentage"
            :subtitle="`Transacciones: ${metrics.sales.sales_count}`"
            icon="currency"
            color="green"
            value-type="currency"
          />
        </div>

        <!-- Secondary Metrics Row -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
          <MetricCard
            title="Agentes Activos"
            :value="metrics.agents.active"
            icon="user"
            color="blue"
            :subtitle="`${metrics.agents.total} total`"
          />
          <MetricCard
            title="Proyectos"
            :value="metrics.projects.total"
            icon="building"
            color="indigo"
            :subtitle="`${metrics.projects.disponible} disponibles`"
          />
          <MetricCard
            title="Visitas Vencidas"
            :value="metrics.visits.overdue"
            icon="clock"
            color="red"
            subtitle="Requieren atención"
          />
          <MetricCard
            title="Tasa Conversión"
            :value="metrics.visits.conversion_rate"
            icon="chart"
            color="green"
            value-type="percentage"
            subtitle="Visitas → Ventas"
          />
          <MetricCard
            title="Seguimientos"
            :value="metrics.sales.follow_up_required"
            icon="eye"
            color="yellow"
            subtitle="Pendientes"
          />
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
