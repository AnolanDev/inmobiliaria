<template>
    <Head :title="property.title" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800 text-balance leading-relaxed">
                        {{ property.title }}
                    </h1>
                    <p class="text-gray-500 text-base leading-relaxed mt-1">
                        {{ property.address }}, {{ property.city }}
                    </p>
                </div>
                <div class="flex gap-3">
                    <Link
                        :href="route('properties.edit', property.id)"
                        class="btn-primary"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Editar
                    </Link>
                    <Link
                        :href="route('properties.index')"
                        class="btn-outline"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Volver al listado
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-8 lg:py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Main Content -->
                    <div class="lg:col-span-2">
                        <div class="property-card">
                            <!-- Property Hero -->
                            <div class="property-hero">
                                <!-- Image Placeholder -->
                                <div class="property-hero-image">
                                    <div class="absolute top-6 left-6 z-10 flex flex-wrap gap-2">
                                        <span class="badge-type">
                                            {{ property.type === 'sale' ? 'Venta' : 'Alquiler' }}
                                        </span>
                                        <span class="badge-category">
                                            {{ getCategoryName(property.category) }}
                                        </span>
                                        <span class="badge-status" :class="getStatusClass(property.status)">
                                            {{ getStatusName(property.status) }}
                                        </span>
                                    </div>
                                    <div class="w-full h-full bg-gradient-to-br from-slate-100 to-slate-200 flex items-center justify-center">
                                        <svg class="w-16 h-16 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m15 13-3 3-3-3"/>
                                        </svg>
                                    </div>
                                </div>

                                <!-- Price -->
                                <div class="property-price">
                                    <span class="price">
                                        ${{ Number(property.price).toLocaleString() }}
                                        <span class="price-period" v-if="property.type === 'rent'">/mes</span>
                                    </span>
                                </div>
                            </div>

                            <!-- Property Content -->
                            <div class="property-content">
                                <!-- Description -->
                                <div class="mb-8">
                                    <h2 class="section-title">Descripción</h2>
                                    <p class="text-slate-600 leading-relaxed">{{ property.description }}</p>
                                </div>

                                <!-- Property Details -->
                                <div class="mb-8">
                                    <h3 class="section-title">Características</h3>
                                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-6">
                                        <div class="detail-item" v-if="property.bedrooms > 0">
                                            <div class="detail-icon">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="detail-value">{{ property.bedrooms }}</p>
                                                <p class="detail-label">Habitaciones</p>
                                            </div>
                                        </div>
                                        <div class="detail-item" v-if="property.bathrooms > 0">
                                            <div class="detail-icon">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="detail-value">{{ property.bathrooms }}</p>
                                                <p class="detail-label">Baños</p>
                                            </div>
                                        </div>
                                        <div class="detail-item">
                                            <div class="detail-icon">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 8V6a2 2 0 012-2h12a2 2 0 012 2v2m-6 12V10a2 2 0 00-2-2H8a2 2 0 00-2 2v10m8 0V10h4v10"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="detail-value">{{ property.area }}m²</p>
                                                <p class="detail-label">Área</p>
                                            </div>
                                        </div>
                                        <div class="detail-item" v-if="property.zip_code">
                                            <div class="detail-icon">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                </svg>
                                            </div>
                                            <div>
                                                <p class="detail-value">{{ property.zip_code }}</p>
                                                <p class="detail-label">Código Postal</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Location -->
                                <div class="mb-8">
                                    <h3 class="section-title">Ubicación</h3>
                                    <div class="bg-slate-50 rounded-xl p-6">
                                        <div class="flex items-start gap-3">
                                            <svg class="w-5 h-5 mt-0.5 text-slate-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                            <div>
                                                <p class="font-medium text-slate-800">{{ property.address }}</p>
                                                <p class="text-slate-600">{{ property.city }}, {{ property.state }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Features -->
                                <div v-if="property.features && property.features.length" class="mb-8">
                                    <h3 class="section-title">Características adicionales</h3>
                                    <div class="flex flex-wrap gap-3">
                                        <span
                                            v-for="feature in property.features"
                                            :key="feature"
                                            class="feature-badge"
                                        >
                                            {{ feature }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="space-y-8">
                        <!-- Agent Info -->
                        <div class="agent-card">
                            <h3 class="section-title mb-6">Agente Responsable</h3>
                            <div class="flex items-center gap-4 mb-6">
                                <div class="w-16 h-16 bg-blue-100 rounded-2xl flex items-center justify-center flex-shrink-0">
                                    <span class="text-blue-600 font-bold text-xl">
                                        {{ property.agent?.name?.charAt(0) }}
                                    </span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="font-bold text-slate-800 text-lg">{{ property.agent?.name }}</p>
                                    <p class="text-slate-600 text-sm">Agente inmobiliario</p>
                                </div>
                            </div>
                            
                            <div class="space-y-3">
                                <div class="flex items-center gap-3 text-slate-600">
                                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                    <span class="text-sm">{{ property.agent?.email }}</span>
                                </div>
                                <div class="flex items-center gap-3 text-slate-600" v-if="property.agent?.phone">
                                    <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                                    </svg>
                                    <span class="text-sm">{{ property.agent?.phone }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Visits -->
                        <div v-if="property.visits && property.visits.length" class="visits-card">
                            <h3 class="section-title mb-6">Visitas Programadas</h3>
                            <div class="space-y-4">
                                <div
                                    v-for="visit in property.visits"
                                    :key="visit.id"
                                    class="visit-item"
                                >
                                    <div class="flex justify-between items-start mb-3">
                                        <div>
                                            <p class="font-semibold text-slate-800">{{ visit.client?.name }}</p>
                                            <p class="text-sm text-slate-600">
                                                {{ new Date(visit.scheduled_at).toLocaleString() }}
                                            </p>
                                        </div>
                                        <span class="visit-status" :class="getVisitStatusClass(visit.status)">
                                            {{ getVisitStatusName(visit.status) }}
                                        </span>
                                    </div>
                                    <p v-if="visit.notes" class="text-sm text-slate-600 bg-slate-50 rounded-lg p-3">
                                        {{ visit.notes }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Quick Actions -->
                        <div class="quick-actions">
                            <h3 class="section-title mb-4">Acciones rápidas</h3>
                            <div class="space-y-3">
                                <Link
                                    :href="route('properties.edit', property.id)"
                                    class="action-button action-primary"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    Editar propiedad
                                </Link>
                                <button class="action-button action-secondary" disabled>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.367 2.684 3 3 0 00-5.367-2.684z"/>
                                    </svg>
                                    Compartir propiedad
                                </button>
                                <button class="action-button action-secondary" disabled>
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 0V5a2 2 0 012-2h4a2 2 0 012 2v2m-6 0h8m-6 0v10a2 2 0 002 2h2a2 2 0 002-2V7"/>
                                    </svg>
                                    Programar visita
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    property: Object,
});

const getCategoryName = (category) => {
    const categories = {
        house: 'Casa',
        apartment: 'Apartamento',
        office: 'Oficina',
        land: 'Terreno',
        commercial: 'Comercial'
    };
    return categories[category] || category;
};

const getStatusName = (status) => {
    const statuses = {
        available: 'Disponible',
        pending: 'Pendiente',
        sold: 'Vendida',
        rented: 'Alquilada'
    };
    return statuses[status] || status;
};

const getStatusClass = (status) => {
    const classes = {
        available: 'bg-green-500/90 text-white',
        pending: 'bg-yellow-500/90 text-white',
        sold: 'bg-red-500/90 text-white',
        rented: 'bg-purple-500/90 text-white'
    };
    return classes[status] || 'bg-slate-500/90 text-white';
};

const getVisitStatusName = (status) => {
    const statuses = {
        scheduled: 'Programada',
        completed: 'Completada',
        cancelled: 'Cancelada',
        no_show: 'No asistió'
    };
    return statuses[status] || status;
};

const getVisitStatusClass = (status) => {
    const classes = {
        scheduled: 'bg-blue-100 text-blue-800',
        completed: 'bg-green-100 text-green-800',
        cancelled: 'bg-red-100 text-red-800',
        no_show: 'bg-slate-100 text-slate-800'
    };
    return classes[status] || 'bg-slate-100 text-slate-800';
};
</script>

<style scoped>
/* Button Styles */
.btn-primary {
    @apply inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white font-medium text-sm rounded-xl 
           hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 
           transition-all duration-200 ease-in-out transform hover:-translate-y-0.5 hover:shadow-lg;
}

.btn-outline {
    @apply inline-flex items-center justify-center gap-2 px-6 py-3 bg-white border border-slate-200 text-slate-700 font-medium text-sm rounded-xl 
           hover:bg-slate-50 hover:border-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 
           transition-all duration-200 ease-in-out;
}

/* Property Card */
.property-card {
    @apply bg-white rounded-2xl shadow-md border border-slate-200 overflow-hidden;
}

.property-hero {
    @apply relative;
}

.property-hero-image {
    @apply relative h-64 lg:h-80 overflow-hidden;
}

.property-price {
    @apply absolute bottom-6 left-6 z-10;
}

.property-content {
    @apply p-6 lg:p-8;
}

/* Badges */
.badge-type {
    @apply px-3 py-1 text-xs font-medium rounded-full 
           bg-white/90 backdrop-blur-sm text-slate-700 shadow-sm;
}

.badge-category {
    @apply px-3 py-1 text-xs font-medium rounded-full 
           bg-blue-600/90 backdrop-blur-sm text-white shadow-sm;
}

.badge-status {
    @apply px-3 py-1 text-xs font-medium rounded-full backdrop-blur-sm shadow-sm;
}

/* Typography */
.price {
    @apply text-3xl lg:text-4xl font-bold text-green-500 leading-none 
           bg-white/90 backdrop-blur-sm rounded-xl px-4 py-2 shadow-sm;
}

.price-period {
    @apply text-base font-normal text-slate-600;
}

.section-title {
    @apply text-xl font-bold text-gray-800 mb-4;
}

/* Details */
.detail-item {
    @apply flex items-center gap-3;
}

.detail-icon {
    @apply w-10 h-10 bg-slate-100 rounded-xl flex items-center justify-center text-slate-600 flex-shrink-0;
}

.detail-value {
    @apply text-lg font-bold text-slate-800;
}

.detail-label {
    @apply text-sm text-slate-600;
}

/* Feature Badges */
.feature-badge {
    @apply px-4 py-2 text-sm font-medium text-slate-700 bg-slate-100 rounded-xl;
}

/* Sidebar Cards */
.agent-card, .visits-card, .quick-actions {
    @apply bg-white rounded-2xl shadow-md border border-slate-200 p-6;
}

/* Visit Items */
.visit-item {
    @apply p-4 bg-slate-50 rounded-xl border border-slate-100;
}

.visit-status {
    @apply px-3 py-1 text-xs font-medium rounded-full;
}

/* Quick Actions */
.action-button {
    @apply w-full flex items-center gap-3 px-4 py-3 text-sm font-medium rounded-xl 
           transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed;
}

.action-primary {
    @apply bg-blue-600 text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500;
}

.action-secondary {
    @apply bg-slate-50 text-slate-700 hover:bg-slate-100 border border-slate-200;
}
</style>