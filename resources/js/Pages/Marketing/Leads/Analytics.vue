<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center justify-between">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Analytics de Leads
                </h2>
                <div class="flex items-center gap-4">
                    <select 
                        v-model="selectedPeriod" 
                        @change="refreshData"
                        class="rounded-lg border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500"
                    >
                        <option value="7">Últimos 7 días</option>
                        <option value="30">Últimos 30 días</option>
                        <option value="90">Últimos 90 días</option>
                    </select>
                </div>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Metrics Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-gray-100">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">
                                            Total Leads
                                        </dt>
                                        <dd class="text-2xl font-bold text-gray-900">
                                            {{ metrics.total_leads }}
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-gray-100">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                                        <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">
                                            Leads Nuevos
                                        </dt>
                                        <dd class="text-2xl font-bold text-gray-900">
                                            {{ metrics.new_leads }}
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-gray-100">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                                        <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">
                                            Calificados
                                        </dt>
                                        <dd class="text-2xl font-bold text-gray-900">
                                            {{ metrics.qualified_leads }}
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-gray-100">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                        </svg>
                                    </div>
                                </div>
                                <div class="ml-5 w-0 flex-1">
                                    <dl>
                                        <dt class="text-sm font-medium text-gray-500 truncate">
                                            Tasa Conversión
                                        </dt>
                                        <dd class="text-2xl font-bold text-gray-900">
                                            {{ metrics.conversion_rate }}%
                                        </dd>
                                    </dl>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Charts Section -->
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <!-- Leads by Status -->
                    <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-gray-100">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">
                                Leads por Estado
                            </h3>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4">
                                <div v-for="item in leads_by_status" :key="item.status" class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="w-3 h-3 rounded-full mr-3 bg-green-500"></div>
                                        <span class="text-sm font-medium text-gray-900 capitalize">
                                            {{ formatStatus(item.status) }}
                                        </span>
                                    </div>
                                    <span class="text-sm font-bold text-gray-600">
                                        {{ item.count }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Leads by Source -->
                    <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-gray-100">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h3 class="text-lg font-semibold text-gray-900">
                                Leads por Fuente
                            </h3>
                        </div>
                        <div class="p-6">
                            <div class="space-y-4">
                                <div v-for="item in leads_by_source" :key="item.source" class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="w-3 h-3 rounded-full mr-3 bg-green-500"></div>
                                        <span class="text-sm font-medium text-gray-900 capitalize">
                                            {{ formatSource(item.source) }}
                                        </span>
                                    </div>
                                    <span class="text-sm font-bold text-gray-600">
                                        {{ item.count }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Leads Over Time -->
                <div class="mt-8 bg-white overflow-hidden shadow-lg rounded-xl border border-gray-100">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900">
                            Leads en el Tiempo
                        </h3>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            <div v-for="item in leads_over_time" :key="item.date" class="flex items-center justify-between py-2 border-b border-gray-100 last:border-b-0">
                                <span class="text-sm text-gray-600">
                                    {{ formatDate(item.date) }}
                                </span>
                                <span class="text-sm font-semibold text-gray-900">
                                    {{ item.count }} leads
                                </span>
                            </div>
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

const props = defineProps({
    leads_by_status: Array,
    leads_by_source: Array, 
    leads_over_time: Array,
    metrics: Object
})

const selectedPeriod = ref(30)

const refreshData = () => {
    // Implementation for refreshing data based on period
    console.log('Refreshing data for period:', selectedPeriod.value)
}

const formatStatus = (status) => {
    const statusMap = {
        'new': 'Nuevo',
        'contacted': 'Contactado',
        'qualified': 'Calificado',
        'converted': 'Convertido',
        'lost': 'Perdido'
    }
    return statusMap[status] || status
}

const formatSource = (source) => {
    const sourceMap = {
        'website': 'Sitio Web',
        'social_media': 'Redes Sociales',
        'referral': 'Referido',
        'advertising': 'Publicidad',
        'other': 'Otro'
    }
    return sourceMap[source] || source
}

const formatDate = (dateString) => {
    return new Date(dateString).toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'short',
        day: 'numeric'
    })
}
</script>