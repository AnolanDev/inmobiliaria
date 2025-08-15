<template>
    <Head title="Configuración Email Marketing" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Configuración de Email Marketing
                </h2>
            </div>
        </template>

        <div class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                
                <!-- Advanced System Status Dashboard -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 border-b border-gray-200">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium text-gray-900">Panel de Control del Sistema</h3>
                            <div class="flex items-center space-x-4">
                                <!-- Overall System Health -->
                                <div class="flex items-center">
                                    <div :class="[
                                        'w-3 h-3 rounded-full mr-2 animate-pulse',
                                        systemHealthColor
                                    ]"></div>
                                    <span class="text-sm font-medium" :class="systemHealthTextColor">
                                        {{ systemHealthStatus }}
                                    </span>
                                </div>
                                <!-- Auto Refresh Toggle -->
                                <label class="flex items-center cursor-pointer">
                                    <input
                                        type="checkbox"
                                        v-model="autoRefresh"
                                        @change="toggleAutoRefresh"
                                        class="sr-only"
                                    />
                                    <div :class="[
                                        'relative inline-flex h-6 w-11 items-center rounded-full transition-colors',
                                        autoRefresh ? 'bg-green-600' : 'bg-gray-200'
                                    ]">
                                        <span :class="[
                                            'inline-block h-4 w-4 transform rounded-full bg-white transition-transform',
                                            autoRefresh ? 'translate-x-6' : 'translate-x-1'
                                        ]"></span>
                                    </div>
                                    <span class="ml-2 text-sm text-gray-700">Auto-refresh</span>
                                </label>
                            </div>
                        </div>

                        <!-- Advanced Status Grid -->
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
                            <!-- Mail System -->
                            <div class="bg-gradient-to-r from-green-50 to-green-100 p-4 rounded-lg border border-green-200">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <div class="flex items-center mb-2">
                                            <div :class="[
                                                'w-3 h-3 rounded-full mr-3',
                                                system_status.mail_configured ? 'bg-green-500 animate-pulse' : 'bg-red-500'
                                            ]"></div>
                                            <span class="text-sm font-medium text-gray-800">Sistema de Correo</span>
                                        </div>
                                        <p class="text-xs text-gray-600" v-if="system_status.mail_configured">
                                            Proveedor: {{ form.provider.toUpperCase() }}
                                        </p>
                                        <p class="text-xs text-red-600" v-else>
                                            Requiere configuración
                                        </p>
                                    </div>
                                    <div :class="[
                                        'text-2xl',
                                        system_status.mail_configured ? 'text-green-600' : 'text-red-600'
                                    ]">
                                        <svg v-if="system_status.mail_configured" fill="currentColor" viewBox="0 0 20 20" class="w-6 h-6">
                                            <path d="M2.94 6.412A2 2 0 002 8.108V16a2 2 0 002 2h12a2 2 0 002-2V8.108a2 2 0 00-.94-1.696l-6-3.75a2 2 0 00-2.12 0l-6 3.75z"/>
                                        </svg>
                                        <svg v-else fill="currentColor" viewBox="0 0 20 20" class="w-6 h-6">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <!-- Queue System -->
                            <div class="bg-gradient-to-r from-purple-50 to-purple-100 p-4 rounded-lg border border-purple-200">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <div class="flex items-center mb-2">
                                            <div :class="[
                                                'w-3 h-3 rounded-full mr-3',
                                                system_status.queue_configured ? 'bg-green-500 animate-pulse' : 'bg-red-500'
                                            ]"></div>
                                            <span class="text-sm font-medium text-gray-800">Sistema de Colas</span>
                                        </div>
                                        <p class="text-xs text-gray-600" v-if="system_status.queue_configured">
                                            Workers activos
                                        </p>
                                        <p class="text-xs text-red-600" v-else>
                                            Sin workers ejecutándose
                                        </p>
                                    </div>
                                    <div :class="[
                                        'text-2xl',
                                        system_status.queue_configured ? 'text-green-600' : 'text-red-600'
                                    ]">
                                        <svg fill="currentColor" viewBox="0 0 20 20" class="w-6 h-6">
                                            <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <!-- Database -->
                            <div class="bg-gradient-to-r from-green-50 to-green-100 p-4 rounded-lg border border-green-200">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <div class="flex items-center mb-2">
                                            <div :class="[
                                                'w-3 h-3 rounded-full mr-3',
                                                system_status.database_accessible ? 'bg-green-500 animate-pulse' : 'bg-red-500'
                                            ]"></div>
                                            <span class="text-sm font-medium text-gray-800">Base de Datos</span>
                                        </div>
                                        <p class="text-xs text-gray-600" v-if="system_status.database_accessible">
                                            Conexión estable
                                        </p>
                                        <p class="text-xs text-red-600" v-else>
                                            Error de conexión
                                        </p>
                                    </div>
                                    <div :class="[
                                        'text-2xl',
                                        system_status.database_accessible ? 'text-green-600' : 'text-red-600'
                                    ]">
                                        <svg fill="currentColor" viewBox="0 0 20 20" class="w-6 h-6">
                                            <path d="M3 12v3c0 1.657 3.134 3 7 3s7-1.343 7-3v-3c0 1.657-3.134 3-7 3s-7-1.343-7-3z"/>
                                            <path d="M3 7v3c0 1.657 3.134 3 7 3s7-1.343 7-3V7c0 1.657-3.134 3-7 3S3 8.657 3 7z"/>
                                            <path d="M17 5c0 1.657-3.134 3-7 3S3 6.657 3 5s3.134-3 7-3 7 1.343 7 3z"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <!-- Cache System -->
                            <div class="bg-gradient-to-r from-orange-50 to-orange-100 p-4 rounded-lg border border-orange-200">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <div class="flex items-center mb-2">
                                            <div :class="[
                                                'w-3 h-3 rounded-full mr-3',
                                                system_status.cache_working ? 'bg-green-500 animate-pulse' : 'bg-red-500'
                                            ]"></div>
                                            <span class="text-sm font-medium text-gray-800">Sistema de Cache</span>
                                        </div>
                                        <p class="text-xs text-gray-600" v-if="system_status.cache_working">
                                            Funcionando optimamente
                                        </p>
                                        <p class="text-xs text-red-600" v-else>
                                            Cache no disponible
                                        </p>
                                    </div>
                                    <div :class="[
                                        'text-2xl',
                                        system_status.cache_working ? 'text-green-600' : 'text-red-600'
                                    ]">
                                        <svg fill="currentColor" viewBox="0 0 20 20" class="w-6 h-6">
                                            <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- System Alerts -->
                        <div v-if="systemAlerts.length > 0" class="mb-6">
                            <h4 class="text-sm font-medium text-gray-900 mb-3 flex items-center">
                                <svg class="w-4 h-4 mr-2 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                Alertas del Sistema
                            </h4>
                            <div class="space-y-2">
                                <div 
                                    v-for="alert in systemAlerts" 
                                    :key="alert.id"
                                    :class="[
                                        'p-3 rounded-lg border-l-4 flex items-center justify-between',
                                        alert.type === 'error' ? 'bg-red-50 border-red-400' : 
                                        alert.type === 'warning' ? 'bg-yellow-50 border-yellow-400' : 
                                        'bg-green-50 border-green-400'
                                    ]"
                                >
                                    <div class="flex items-center">
                                        <span :class="[
                                            'text-sm font-medium mr-2',
                                            alert.type === 'error' ? 'text-red-800' : 
                                            alert.type === 'warning' ? 'text-yellow-800' : 
                                            'text-green-800'
                                        ]">{{ alert.title }}</span>
                                        <span :class="[
                                            'text-sm',
                                            alert.type === 'error' ? 'text-red-600' : 
                                            alert.type === 'warning' ? 'text-yellow-600' : 
                                            'text-green-600'
                                        ]">{{ alert.message }}</span>
                                    </div>
                                    <button 
                                        @click="dismissAlert(alert.id)"
                                        :class="[
                                            'text-sm px-2 py-1 rounded',
                                            alert.type === 'error' ? 'text-red-600 hover:bg-red-100' : 
                                            alert.type === 'warning' ? 'text-yellow-600 hover:bg-yellow-100' : 
                                            'text-green-600 hover:bg-green-100'
                                        ]"
                                    >
                                        ×
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Advanced Analytics Dashboard -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <div class="flex justify-between items-center mb-6">
                            <h3 class="text-lg font-medium text-gray-900">Analytics en Tiempo Real</h3>
                            <div class="flex items-center space-x-2">
                                <select v-model="selectedPeriod" @change="updateCharts" class="text-sm border-gray-300 rounded-md">
                                    <option value="24h">Últimas 24h</option>
                                    <option value="7d">Última semana</option>
                                    <option value="30d">Último mes</option>
                                </select>
                            </div>
                        </div>

                        <!-- KPI Cards -->
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-8">
                            <!-- Total Emails -->
                            <div class="bg-gradient-to-r from-green-500 to-green-600 p-6 rounded-lg text-white">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-green-100 text-sm font-medium">Total Enviados</p>
                                        <p class="text-3xl font-bold">{{ formatNumber(stats.total_emails_sent || 0) }}</p>
                                        <div class="flex items-center mt-2 text-sm">
                                            <svg class="w-4 h-4 mr-1" :class="emailsTrend >= 0 ? 'text-green-300' : 'text-red-300'" fill="currentColor" viewBox="0 0 20 20">
                                                <path v-if="emailsTrend >= 0" fill-rule="evenodd" d="M3.293 9.707a1 1 0 010-1.414l6-6a1 1 0 011.414 0l6 6a1 1 0 01-1.414 1.414L10 4.414 4.707 9.707a1 1 0 01-1.414 0z" clip-rule="evenodd"/>
                                                <path v-else fill-rule="evenodd" d="M16.707 10.293a1 1 0 010 1.414l-6 6a1 1 0 01-1.414 0l-6-6a1 1 0 111.414-1.414L10 15.586l5.293-5.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                            </svg>
                                            <span :class="emailsTrend >= 0 ? 'text-green-300' : 'text-red-300'">
                                                {{ Math.abs(emailsTrend) }}% vs período anterior
                                            </span>
                                        </div>
                                    </div>
                                    <div class="text-4xl opacity-80">
                                        <svg fill="currentColor" viewBox="0 0 20 20" class="w-12 h-12">
                                            <path d="M2.94 6.412A2 2 0 002 8.108V16a2 2 0 002 2h12a2 2 0 002-2V8.108a2 2 0 00-.94-1.696l-6-3.75a2 2 0 00-2.12 0l-6 3.75z"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <!-- Delivery Rate -->
                            <div class="bg-gradient-to-r from-green-500 to-green-600 p-6 rounded-lg text-white">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-green-100 text-sm font-medium">Tasa de Entrega</p>
                                        <p class="text-3xl font-bold">{{ stats.delivery_rate || '0%' }}</p>
                                        <div class="mt-2">
                                            <div class="w-full bg-green-400 rounded-full h-2">
                                                <div 
                                                    class="bg-white h-2 rounded-full transition-all duration-500"
                                                    :style="{ width: stats.delivery_rate || '0%' }"
                                                ></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-4xl opacity-80">
                                        <svg fill="currentColor" viewBox="0 0 20 20" class="w-12 h-12">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <!-- Open Rate -->
                            <div class="bg-gradient-to-r from-purple-500 to-purple-600 p-6 rounded-lg text-white">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-purple-100 text-sm font-medium">Tasa de Apertura</p>
                                        <p class="text-3xl font-bold">{{ stats.open_rate || '0%' }}</p>
                                        <div class="mt-2">
                                            <div class="w-full bg-purple-400 rounded-full h-2">
                                                <div 
                                                    class="bg-white h-2 rounded-full transition-all duration-500"
                                                    :style="{ width: stats.open_rate || '0%' }"
                                                ></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-4xl opacity-80">
                                        <svg fill="currentColor" viewBox="0 0 20 20" class="w-12 h-12">
                                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                                            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <!-- Click Rate -->
                            <div class="bg-gradient-to-r from-orange-500 to-orange-600 p-6 rounded-lg text-white">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-orange-100 text-sm font-medium">Tasa de Clicks</p>
                                        <p class="text-3xl font-bold">{{ stats.click_rate || '0%' }}</p>
                                        <div class="mt-2">
                                            <div class="w-full bg-orange-400 rounded-full h-2">
                                                <div 
                                                    class="bg-white h-2 rounded-full transition-all duration-500"
                                                    :style="{ width: stats.click_rate || '0%' }"
                                                ></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-4xl opacity-80">
                                        <svg fill="currentColor" viewBox="0 0 20 20" class="w-12 h-12">
                                            <path fill-rule="evenodd" d="M12.586 4.586a2 2 0 112.828 2.828l-3 3a2 2 0 01-2.828 0 1 1 0 00-1.414 1.414 4 4 0 005.656 0l3-3a4 4 0 00-5.656-5.656l-1.5 1.5a1 1 0 101.414 1.414l1.5-1.5zm-5 5a2 2 0 012.828 0 1 1 0 101.414-1.414 4 4 0 00-5.656 0l-3 3a4 4 0 105.656 5.656l1.5-1.5a1 1 0 10-1.414-1.414l-1.5 1.5a2 2 0 11-2.828-2.828l3-3z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Charts Section -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                            <!-- Email Volume Chart -->
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h4 class="text-sm font-medium text-gray-900 mb-4">Volumen de Emails (Últimas 24h)</h4>
                                <div class="relative h-64">
                                    <canvas ref="emailVolumeChart"></canvas>
                                </div>
                            </div>

                            <!-- Performance Chart -->
                            <div class="bg-gray-50 p-4 rounded-lg">
                                <h4 class="text-sm font-medium text-gray-900 mb-4">Rendimiento por Hora</h4>
                                <div class="relative h-64">
                                    <canvas ref="performanceChart"></canvas>
                                </div>
                            </div>
                        </div>

                        <!-- Queue Status -->
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <div class="flex justify-between items-center mb-4">
                                <h4 class="text-sm font-medium text-gray-900">Estado de la Cola de Envíos</h4>
                                <div class="flex items-center space-x-4">
                                    <div class="flex items-center">
                                        <div class="w-2 h-2 bg-green-500 rounded-full mr-2 animate-pulse"></div>
                                        <span class="text-sm text-gray-600">Procesando: {{ queueStats.processing || 0 }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <div class="w-2 h-2 bg-yellow-500 rounded-full mr-2"></div>
                                        <span class="text-sm text-gray-600">Pendientes: {{ queueStats.pending || 0 }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <div class="w-2 h-2 bg-red-500 rounded-full mr-2"></div>
                                        <span class="text-sm text-gray-600">Fallidos: {{ queueStats.failed || 0 }}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Queue Progress Bar -->
                            <div class="w-full bg-gray-200 rounded-full h-3 mb-2">
                                <div class="bg-gradient-to-r from-green-500 to-green-500 h-3 rounded-full transition-all duration-500" 
                                     :style="{ width: queueProgressPercentage + '%' }"></div>
                            </div>
                            <div class="flex justify-between text-xs text-gray-500">
                                <span>Progreso de envíos</span>
                                <span>{{ queueProgressPercentage }}% completado</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Configuration Form -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <form @submit.prevent="saveConfiguration" class="p-6 space-y-6">
                        <div class="border-b border-gray-200 pb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Configuración General</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Email Marketing Enabled -->
                                <div>
                                    <label class="flex items-center">
                                        <input
                                            type="checkbox"
                                            v-model="form.enabled"
                                            class="rounded border-gray-300 text-green-600 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50"
                                        />
                                        <span class="ml-2 text-sm text-gray-700">Habilitar Email Marketing</span>
                                    </label>
                                </div>

                                <!-- Email Provider -->
                                <div>
                                    <label for="provider" class="block text-sm font-medium text-gray-700">Proveedor de Email</label>
                                    <select
                                        id="provider"
                                        v-model="form.provider"
                                        @change="onProviderChange"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50"
                                    >
                                        <option v-for="(label, value) in providers" :key="value" :value="value">
                                            {{ label }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Provider-specific Configuration -->
                        <div class="border-b border-gray-200 pb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Configuración del Proveedor</h3>
                            
                            <!-- SMTP Configuration -->
                            <div v-if="form.provider === 'smtp'" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="smtp_host" class="block text-sm font-medium text-gray-700">Host SMTP</label>
                                    <input
                                        type="text"
                                        id="smtp_host"
                                        v-model="form.smtp_config.host"
                                        placeholder="ej: smtp.gmail.com"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50"
                                    />
                                </div>
                                <div>
                                    <label for="smtp_port" class="block text-sm font-medium text-gray-700">Puerto</label>
                                    <select
                                        id="smtp_port"
                                        v-model="form.smtp_config.port"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50"
                                    >
                                        <option value="25">25 (Sin cifrado)</option>
                                        <option value="587">587 (TLS)</option>
                                        <option value="465">465 (SSL)</option>
                                        <option value="2525">2525 (Alternativo)</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="smtp_username" class="block text-sm font-medium text-gray-700">Usuario</label>
                                    <input
                                        type="email"
                                        id="smtp_username"
                                        v-model="form.smtp_config.username"
                                        placeholder="tu-email@gmail.com"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50"
                                    />
                                </div>
                                <div>
                                    <label for="smtp_password" class="block text-sm font-medium text-gray-700">Contraseña</label>
                                    <input
                                        type="password"
                                        id="smtp_password"
                                        v-model="form.smtp_config.password"
                                        placeholder="Contraseña o App Password"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50"
                                    />
                                </div>
                                <div>
                                    <label for="smtp_encryption" class="block text-sm font-medium text-gray-700">Cifrado</label>
                                    <select
                                        id="smtp_encryption"
                                        v-model="form.smtp_config.encryption"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50"
                                    >
                                        <option value="">Sin cifrado</option>
                                        <option value="tls">TLS</option>
                                        <option value="ssl">SSL</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="smtp_from_address" class="block text-sm font-medium text-gray-700">Email Remitente</label>
                                    <input
                                        type="email"
                                        id="smtp_from_address"
                                        v-model="form.smtp_config.from_address"
                                        placeholder="noreply@tudominio.com"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50"
                                    />
                                </div>
                            </div>

                            <!-- SendGrid Configuration -->
                            <div v-if="form.provider === 'sendgrid'" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="md:col-span-2">
                                    <label for="sendgrid_api_key" class="block text-sm font-medium text-gray-700">API Key de SendGrid</label>
                                    <input
                                        type="password"
                                        id="sendgrid_api_key"
                                        v-model="form.sendgrid_config.api_key"
                                        placeholder="SG.xxxxxxxxxx"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50"
                                    />
                                    <p class="mt-1 text-sm text-gray-500">
                                        <a href="https://app.sendgrid.com/settings/api_keys" target="_blank" class="text-green-600 hover:text-green-800">
                                            Obtener API Key en SendGrid →
                                        </a>
                                    </p>
                                </div>
                                <div>
                                    <label for="sendgrid_from_address" class="block text-sm font-medium text-gray-700">Email Remitente</label>
                                    <input
                                        type="email"
                                        id="sendgrid_from_address"
                                        v-model="form.sendgrid_config.from_address"
                                        placeholder="noreply@tudominio.com"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50"
                                    />
                                </div>
                                <div>
                                    <label for="sendgrid_from_name" class="block text-sm font-medium text-gray-700">Nombre Remitente</label>
                                    <input
                                        type="text"
                                        id="sendgrid_from_name"
                                        v-model="form.sendgrid_config.from_name"
                                        placeholder="Tu Empresa"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50"
                                    />
                                </div>
                            </div>

                            <!-- Mailgun Configuration -->
                            <div v-if="form.provider === 'mailgun'" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="mailgun_domain" class="block text-sm font-medium text-gray-700">Dominio Mailgun</label>
                                    <input
                                        type="text"
                                        id="mailgun_domain"
                                        v-model="form.mailgun_config.domain"
                                        placeholder="ej: mail.tudominio.com"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50"
                                    />
                                </div>
                                <div>
                                    <label for="mailgun_secret" class="block text-sm font-medium text-gray-700">API Key</label>
                                    <input
                                        type="password"
                                        id="mailgun_secret"
                                        v-model="form.mailgun_config.secret"
                                        placeholder="key-xxxxxxxxxx"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50"
                                    />
                                </div>
                                <div>
                                    <label for="mailgun_endpoint" class="block text-sm font-medium text-gray-700">Endpoint</label>
                                    <select
                                        id="mailgun_endpoint"
                                        v-model="form.mailgun_config.endpoint"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50"
                                    >
                                        <option value="api.mailgun.net">US - api.mailgun.net</option>
                                        <option value="api.eu.mailgun.net">EU - api.eu.mailgun.net</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="mailgun_from_address" class="block text-sm font-medium text-gray-700">Email Remitente</label>
                                    <input
                                        type="email"
                                        id="mailgun_from_address"
                                        v-model="form.mailgun_config.from_address"
                                        placeholder="noreply@tudominio.com"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50"
                                    />
                                </div>
                            </div>

                            <!-- Amazon SES Configuration -->
                            <div v-if="form.provider === 'ses'" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="ses_key" class="block text-sm font-medium text-gray-700">Access Key ID</label>
                                    <input
                                        type="text"
                                        id="ses_key"
                                        v-model="form.ses_config.key"
                                        placeholder="AKIA..."
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50"
                                    />
                                </div>
                                <div>
                                    <label for="ses_secret" class="block text-sm font-medium text-gray-700">Secret Access Key</label>
                                    <input
                                        type="password"
                                        id="ses_secret"
                                        v-model="form.ses_config.secret"
                                        placeholder="Secret Key"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50"
                                    />
                                </div>
                                <div>
                                    <label for="ses_region" class="block text-sm font-medium text-gray-700">Región</label>
                                    <select
                                        id="ses_region"
                                        v-model="form.ses_config.region"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50"
                                    >
                                        <option value="us-east-1">US East (Virginia)</option>
                                        <option value="us-west-2">US West (Oregon)</option>
                                        <option value="eu-west-1">Europe (Ireland)</option>
                                        <option value="ap-southeast-2">Asia Pacific (Sydney)</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="ses_from_address" class="block text-sm font-medium text-gray-700">Email Remitente</label>
                                    <input
                                        type="email"
                                        id="ses_from_address"
                                        v-model="form.ses_config.from_address"
                                        placeholder="noreply@tudominio.com"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50"
                                    />
                                </div>
                            </div>

                            <!-- Postmark Configuration -->
                            <div v-if="form.provider === 'postmark'" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="md:col-span-2">
                                    <label for="postmark_token" class="block text-sm font-medium text-gray-700">Server Token</label>
                                    <input
                                        type="password"
                                        id="postmark_token"
                                        v-model="form.postmark_config.token"
                                        placeholder="xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50"
                                    />
                                </div>
                                <div>
                                    <label for="postmark_from_address" class="block text-sm font-medium text-gray-700">Email Remitente</label>
                                    <input
                                        type="email"
                                        id="postmark_from_address"
                                        v-model="form.postmark_config.from_address"
                                        placeholder="noreply@tudominio.com"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50"
                                    />
                                </div>
                                <div>
                                    <label for="postmark_from_name" class="block text-sm font-medium text-gray-700">Nombre Remitente</label>
                                    <input
                                        type="text"
                                        id="postmark_from_name"
                                        v-model="form.postmark_config.from_name"
                                        placeholder="Tu Empresa"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50"
                                    />
                                </div>
                            </div>

                            <!-- Mailtrap Configuration -->
                            <div v-if="form.provider === 'mailtrap'" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label for="mailtrap_username" class="block text-sm font-medium text-gray-700">Username</label>
                                    <input
                                        type="text"
                                        id="mailtrap_username"
                                        v-model="form.mailtrap_config.username"
                                        placeholder="Username de Mailtrap"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50"
                                    />
                                </div>
                                <div>
                                    <label for="mailtrap_password" class="block text-sm font-medium text-gray-700">Password</label>
                                    <input
                                        type="password"
                                        id="mailtrap_password"
                                        v-model="form.mailtrap_config.password"
                                        placeholder="Password de Mailtrap"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50"
                                    />
                                </div>
                                <div>
                                    <label for="mailtrap_from_address" class="block text-sm font-medium text-gray-700">Email Remitente</label>
                                    <input
                                        type="email"
                                        id="mailtrap_from_address"
                                        v-model="form.mailtrap_config.from_address"
                                        placeholder="test@example.com"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50"
                                    />
                                </div>
                            </div>
                        </div>

                        <!-- Rate Limiting -->
                        <div class="border-b border-gray-200 pb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Limitación de Envío</h3>
                            
                            <div class="mb-4">
                                <label class="flex items-center">
                                    <input
                                        type="checkbox"
                                        v-model="form.rate_limiting.enabled"
                                        class="rounded border-gray-300 text-green-600 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50"
                                    />
                                    <span class="ml-2 text-sm text-gray-700">Habilitar Limitación de Envío</span>
                                </label>
                            </div>

                            <div v-if="form.rate_limiting.enabled" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                                <div>
                                    <label for="emails_per_minute" class="block text-sm font-medium text-gray-700">Emails por Minuto</label>
                                    <input
                                        type="number"
                                        id="emails_per_minute"
                                        v-model.number="form.rate_limiting.emails_per_minute"
                                        min="1"
                                        max="100"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50"
                                    />
                                </div>
                                <div>
                                    <label for="emails_per_hour" class="block text-sm font-medium text-gray-700">Emails por Hora</label>
                                    <input
                                        type="number"
                                        id="emails_per_hour"
                                        v-model.number="form.rate_limiting.emails_per_hour"
                                        min="1"
                                        max="5000"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50"
                                    />
                                </div>
                                <div>
                                    <label for="emails_per_day" class="block text-sm font-medium text-gray-700">Emails por Día</label>
                                    <input
                                        type="number"
                                        id="emails_per_day"
                                        v-model.number="form.rate_limiting.emails_per_day"
                                        min="1"
                                        max="50000"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50"
                                    />
                                </div>
                                <div>
                                    <label for="delay_between_emails" class="block text-sm font-medium text-gray-700">Delay entre Emails (seg)</label>
                                    <input
                                        type="number"
                                        id="delay_between_emails"
                                        v-model.number="form.rate_limiting.delay_between_emails"
                                        min="0"
                                        max="60"
                                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50"
                                    />
                                </div>
                            </div>
                        </div>

                        <!-- Tracking Configuration -->
                        <div class="border-b border-gray-200 pb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Configuración de Tracking</h3>
                            
                            <div class="space-y-3">
                                <label class="flex items-center">
                                    <input
                                        type="checkbox"
                                        v-model="form.tracking.enabled"
                                        class="rounded border-gray-300 text-green-600 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50"
                                    />
                                    <span class="ml-2 text-sm text-gray-700">Habilitar Tracking</span>
                                </label>
                                
                                <div v-if="form.tracking.enabled" class="ml-6 space-y-2">
                                    <label class="flex items-center">
                                        <input
                                            type="checkbox"
                                            v-model="form.tracking.open_tracking"
                                            class="rounded border-gray-300 text-green-600 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50"
                                        />
                                        <span class="ml-2 text-sm text-gray-700">Tracking de Apertura</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input
                                            type="checkbox"
                                            v-model="form.tracking.click_tracking"
                                            class="rounded border-gray-300 text-green-600 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50"
                                        />
                                        <span class="ml-2 text-sm text-gray-700">Tracking de Clicks</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <!-- Compliance Configuration -->
                        <div class="border-b border-gray-200 pb-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Configuración de Cumplimiento</h3>
                            
                            <div class="space-y-3">
                                <label class="flex items-center">
                                    <input
                                        type="checkbox"
                                        v-model="form.compliance.require_double_opt_in"
                                        class="rounded border-gray-300 text-green-600 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50"
                                    />
                                    <span class="ml-2 text-sm text-gray-700">Requerir Double Opt-in</span>
                                </label>
                                <label class="flex items-center">
                                    <input
                                        type="checkbox"
                                        v-model="form.compliance.include_physical_address"
                                        class="rounded border-gray-300 text-green-600 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50"
                                    />
                                    <span class="ml-2 text-sm text-gray-700">Incluir Dirección Física</span>
                                </label>
                                <label class="flex items-center">
                                    <input
                                        type="checkbox"
                                        v-model="form.compliance.unsubscribe_footer"
                                        class="rounded border-gray-300 text-green-600 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50"
                                    />
                                    <span class="ml-2 text-sm text-gray-700">Footer de Unsubscribe</span>
                                </label>
                                <label class="flex items-center">
                                    <input
                                        type="checkbox"
                                        v-model="form.compliance.list_unsubscribe_header"
                                        class="rounded border-gray-300 text-green-600 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50"
                                    />
                                    <span class="ml-2 text-sm text-gray-700">Header List-Unsubscribe</span>
                                </label>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex justify-between items-center pt-6">
                            <div class="space-x-4">
                                <button
                                    type="submit"
                                    :disabled="form.processing"
                                    class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150"
                                >
                                    <span v-if="form.processing" class="mr-2">
                                        <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    </span>
                                    {{ form.processing ? 'Guardando...' : 'Guardar Configuración' }}
                                </button>
                            </div>

                            <!-- Test Email Section -->
                            <div class="flex items-center space-x-3">
                                <input
                                    type="email"
                                    v-model="testEmail"
                                    placeholder="Email para prueba"
                                    class="rounded-md border-gray-300 shadow-sm focus:border-green-300 focus:ring focus:ring-green-200 focus:ring-opacity-50"
                                />
                                <button
                                    type="button"
                                    @click="sendTestEmail"
                                    :disabled="!testEmail || testingEmail"
                                    class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-50"
                                >
                                    <span v-if="testingEmail" class="mr-2">
                                        <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                    </span>
                                    {{ testingEmail ? 'Enviando...' : 'Enviar Prueba' }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3'
import { useForm } from '@inertiajs/vue3'
import { ref, computed, onMounted, onUnmounted, nextTick } from 'vue'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import { Chart, registerables } from 'chart.js'
import 'chartjs-adapter-date-fns'

Chart.register(...registerables)

const props = defineProps({
    config: Object,
    providers: Object,
    stats: Object,
    system_status: Object
})

// Initialize form with current config
const form = useForm({
    enabled: props.config.enabled ?? true,
    provider: props.config.provider ?? 'smtp',
    // SMTP Configuration
    smtp_config: {
        host: props.config.smtp_config?.host ?? '',
        port: props.config.smtp_config?.port ?? '587',
        username: props.config.smtp_config?.username ?? '',
        password: props.config.smtp_config?.password ?? '',
        encryption: props.config.smtp_config?.encryption ?? 'tls',
        from_address: props.config.smtp_config?.from_address ?? ''
    },
    // SendGrid Configuration
    sendgrid_config: {
        api_key: props.config.sendgrid_config?.api_key ?? '',
        from_address: props.config.sendgrid_config?.from_address ?? '',
        from_name: props.config.sendgrid_config?.from_name ?? ''
    },
    // Mailgun Configuration
    mailgun_config: {
        domain: props.config.mailgun_config?.domain ?? '',
        secret: props.config.mailgun_config?.secret ?? '',
        endpoint: props.config.mailgun_config?.endpoint ?? 'api.mailgun.net',
        from_address: props.config.mailgun_config?.from_address ?? ''
    },
    // Amazon SES Configuration
    ses_config: {
        key: props.config.ses_config?.key ?? '',
        secret: props.config.ses_config?.secret ?? '',
        region: props.config.ses_config?.region ?? 'us-east-1',
        from_address: props.config.ses_config?.from_address ?? ''
    },
    // Postmark Configuration
    postmark_config: {
        token: props.config.postmark_config?.token ?? '',
        from_address: props.config.postmark_config?.from_address ?? '',
        from_name: props.config.postmark_config?.from_name ?? ''
    },
    // Mailtrap Configuration
    mailtrap_config: {
        username: props.config.mailtrap_config?.username ?? '',
        password: props.config.mailtrap_config?.password ?? '',
        from_address: props.config.mailtrap_config?.from_address ?? ''
    },
    rate_limiting: {
        enabled: props.config.rate_limiting?.enabled ?? true,
        emails_per_minute: props.config.rate_limiting?.emails_per_minute ?? 10,
        emails_per_hour: props.config.rate_limiting?.emails_per_hour ?? 500,
        emails_per_day: props.config.rate_limiting?.emails_per_day ?? 5000,
        delay_between_emails: props.config.rate_limiting?.delay_between_emails ?? 1
    },
    bounce_handling: {
        enabled: props.config.bounce_handling?.enabled ?? true,
        max_soft_bounces: props.config.bounce_handling?.max_soft_bounces ?? 5,
        max_hard_bounces: props.config.bounce_handling?.max_hard_bounces ?? 2,
        auto_unsubscribe_on_hard_bounce: props.config.bounce_handling?.auto_unsubscribe_on_hard_bounce ?? true,
        auto_unsubscribe_on_spam: props.config.bounce_handling?.auto_unsubscribe_on_spam ?? true
    },
    tracking: {
        enabled: props.config.tracking?.enabled ?? true,
        open_tracking: props.config.tracking?.open_tracking ?? true,
        click_tracking: props.config.tracking?.click_tracking ?? true
    },
    compliance: {
        require_double_opt_in: props.config.compliance?.require_double_opt_in ?? false,
        include_physical_address: props.config.compliance?.include_physical_address ?? true,
        unsubscribe_footer: props.config.compliance?.unsubscribe_footer ?? true,
        list_unsubscribe_header: props.config.compliance?.list_unsubscribe_header ?? true
    }
})

const testEmail = ref('')
const testingEmail = ref(false)

// Dashboard state
const autoRefresh = ref(true)
const selectedPeriod = ref('24h')
const systemAlerts = ref([])
const refreshInterval = ref(null)
const emailsTrend = ref(15) // Example trend percentage
const queueStats = ref({
    processing: 3,
    pending: 12,
    failed: 0,
    total: 15
})

// Chart refs
const emailVolumeChart = ref(null)
const performanceChart = ref(null)
let volumeChartInstance = null
let performanceChartInstance = null

// Handle provider change to reset configuration fields
const onProviderChange = () => {
    // Reset all provider configs when switching providers
    const resetConfigs = {
        smtp_config: { host: '', port: '587', username: '', password: '', encryption: 'tls', from_address: '' },
        sendgrid_config: { api_key: '', from_address: '', from_name: '' },
        mailgun_config: { domain: '', secret: '', endpoint: 'api.mailgun.net', from_address: '' },
        ses_config: { key: '', secret: '', region: 'us-east-1', from_address: '' },
        postmark_config: { token: '', from_address: '', from_name: '' },
        mailtrap_config: { username: '', password: '', from_address: '' }
    }
    
    // Apply reset configs
    Object.assign(form, resetConfigs)
}

const saveConfiguration = () => {
    form.post(route('email-marketing.config.update'), {
        preserveScroll: true,
        onSuccess: () => {
            // Handle success
        }
    })
}

const sendTestEmail = async () => {
    if (!testEmail.value) return
    
    testingEmail.value = true
    
    try {
        await axios.post(route('email-marketing.config.test'), {
            email: testEmail.value
        })
        
        // Show success message
        alert('Email de prueba enviado exitosamente!')
    } catch (error) {
        // Show error message
        alert('Error al enviar email de prueba: ' + (error.response?.data?.message || error.message))
    } finally {
        testingEmail.value = false
    }
}

// Computed properties for dashboard
const systemHealthColor = computed(() => {
    const healthyCount = [
        props.system_status.mail_configured,
        props.system_status.queue_configured,
        props.system_status.database_accessible,
        props.system_status.cache_working
    ].filter(Boolean).length
    
    if (healthyCount === 4) return 'bg-green-500'
    if (healthyCount >= 3) return 'bg-yellow-500'
    return 'bg-red-500'
})

const systemHealthTextColor = computed(() => {
    const healthyCount = [
        props.system_status.mail_configured,
        props.system_status.queue_configured,
        props.system_status.database_accessible,
        props.system_status.cache_working
    ].filter(Boolean).length
    
    if (healthyCount === 4) return 'text-green-700'
    if (healthyCount >= 3) return 'text-yellow-700'
    return 'text-red-700'
})

const systemHealthStatus = computed(() => {
    const healthyCount = [
        props.system_status.mail_configured,
        props.system_status.queue_configured,
        props.system_status.database_accessible,
        props.system_status.cache_working
    ].filter(Boolean).length
    
    if (healthyCount === 4) return 'Sistema Óptimo'
    if (healthyCount >= 3) return 'Sistema Estable'
    if (healthyCount >= 2) return 'Problemas Menores'
    return 'Sistema Crítico'
})

const queueProgressPercentage = computed(() => {
    const total = queueStats.value.total || 1
    const completed = total - queueStats.value.pending - queueStats.value.processing
    return Math.round((completed / total) * 100)
})

// Dashboard methods
const formatNumber = (num) => {
    if (num >= 1000000) return (num / 1000000).toFixed(1) + 'M'
    if (num >= 1000) return (num / 1000).toFixed(1) + 'K'
    return num.toString()
}

const toggleAutoRefresh = () => {
    if (autoRefresh.value) {
        startAutoRefresh()
    } else {
        stopAutoRefresh()
    }
}

const startAutoRefresh = () => {
    refreshInterval.value = setInterval(() => {
        updateSystemStatus()
        updateCharts()
    }, 30000) // Refresh every 30 seconds
}

const stopAutoRefresh = () => {
    if (refreshInterval.value) {
        clearInterval(refreshInterval.value)
        refreshInterval.value = null
    }
}

const updateSystemStatus = async () => {
    try {
        const response = await axios.get(route('email-marketing.config'), {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        
        // Update stats and queue info
        Object.assign(props.stats, response.data.stats)
        Object.assign(queueStats.value, response.data.queueStats || queueStats.value)
        
        checkForAlerts()
    } catch (error) {
        console.error('Error updating system status:', error)
    }
}

const checkForAlerts = () => {
    const alerts = []
    
    if (!props.system_status.mail_configured) {
        alerts.push({
            id: 'mail-not-configured',
            type: 'error',
            title: 'Email no configurado',
            message: 'Configure un proveedor de email para enviar correos'
        })
    }
    
    if (!props.system_status.queue_configured) {
        alerts.push({
            id: 'queue-not-running',
            type: 'warning',
            title: 'Cola no activa',
            message: 'Inicie el queue worker para procesar envíos masivos'
        })
    }
    
    if (queueStats.value.failed > 0) {
        alerts.push({
            id: 'queue-failures',
            type: 'warning',
            title: 'Jobs fallidos',
            message: `${queueStats.value.failed} trabajos han fallado`
        })
    }
    
    if (props.stats.delivery_rate && parseInt(props.stats.delivery_rate) < 95) {
        alerts.push({
            id: 'low-delivery-rate',
            type: 'warning',
            title: 'Tasa de entrega baja',
            message: 'La tasa de entrega está por debajo del 95%'
        })
    }
    
    systemAlerts.value = alerts
}

const dismissAlert = (alertId) => {
    systemAlerts.value = systemAlerts.value.filter(alert => alert.id !== alertId)
}

const updateCharts = async () => {
    await generateEmailVolumeChart()
    await generatePerformanceChart()
}

const generateEmailVolumeChart = async () => {
    if (!emailVolumeChart.value) return
    
    // Destroy existing chart
    if (volumeChartInstance) {
        volumeChartInstance.destroy()
    }
    
    // Generate sample data for demonstration
    const hours = []
    const emailsSent = []
    const emailsDelivered = []
    
    for (let i = 23; i >= 0; i--) {
        const hour = new Date()
        hour.setHours(hour.getHours() - i)
        hours.push(hour.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' }))
        
        // Generate realistic sample data
        const baseVolume = Math.floor(Math.random() * 100) + 20
        emailsSent.push(baseVolume)
        emailsDelivered.push(Math.floor(baseVolume * (0.85 + Math.random() * 0.1)))
    }
    
    const ctx = emailVolumeChart.value.getContext('2d')
    volumeChartInstance = new Chart(ctx, {
        type: 'line',
        data: {
            labels: hours,
            datasets: [
                {
                    label: 'Emails Enviados',
                    data: emailsSent,
                    borderColor: '#3B82F6',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    fill: true,
                    tension: 0.4
                },
                {
                    label: 'Emails Entregados',
                    data: emailsDelivered,
                    borderColor: '#10B981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    fill: true,
                    tension: 0.4
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                },
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: '#F3F4F6'
                    }
                },
                x: {
                    grid: {
                        color: '#F3F4F6'
                    }
                }
            }
        }
    })
}

const generatePerformanceChart = async () => {
    if (!performanceChart.value) return
    
    // Destroy existing chart
    if (performanceChartInstance) {
        performanceChartInstance.destroy()
    }
    
    // Generate sample performance data
    const hours = []
    const openRates = []
    const clickRates = []
    const bounceRates = []
    
    for (let i = 23; i >= 0; i--) {
        const hour = new Date()
        hour.setHours(hour.getHours() - i)
        hours.push(hour.toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' }))
        
        openRates.push(Math.floor(Math.random() * 20) + 15) // 15-35%
        clickRates.push(Math.floor(Math.random() * 8) + 2)   // 2-10%
        bounceRates.push(Math.floor(Math.random() * 5) + 1)  // 1-6%
    }
    
    const ctx = performanceChart.value.getContext('2d')
    performanceChartInstance = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: hours,
            datasets: [
                {
                    label: 'Tasa de Apertura (%)',
                    data: openRates,
                    backgroundColor: 'rgba(147, 51, 234, 0.8)',
                    borderColor: '#9333EA',
                    borderWidth: 1
                },
                {
                    label: 'Tasa de Clicks (%)',
                    data: clickRates,
                    backgroundColor: 'rgba(249, 115, 22, 0.8)',
                    borderColor: '#F97316',
                    borderWidth: 1
                },
                {
                    label: 'Tasa de Rebote (%)',
                    data: bounceRates,
                    backgroundColor: 'rgba(239, 68, 68, 0.8)',
                    borderColor: '#EF4444',
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                },
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 40,
                    grid: {
                        color: '#F3F4F6'
                    }
                },
                x: {
                    grid: {
                        color: '#F3F4F6'
                    }
                }
            }
        }
    })
}

// Lifecycle hooks
onMounted(async () => {
    await nextTick()
    
    // Initialize charts
    setTimeout(() => {
        updateCharts()
    }, 100)
    
    // Check for initial alerts
    checkForAlerts()
    
    // Start auto refresh if enabled
    if (autoRefresh.value) {
        startAutoRefresh()
    }
})

onUnmounted(() => {
    stopAutoRefresh()
    if (volumeChartInstance) volumeChartInstance.destroy()
    if (performanceChartInstance) performanceChartInstance.destroy()
})
</script>