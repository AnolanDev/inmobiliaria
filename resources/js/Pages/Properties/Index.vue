<template>
    <Head title="Propiedades" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800 text-balance leading-relaxed">
                        Propiedades
                    </h1>
                    <p class="text-gray-500 text-base leading-relaxed mt-1">
                        Gestiona tu cartera inmobiliaria
                    </p>
                </div>
                <Link
                    :href="route('properties.create')"
                    class="btn-primary"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Nueva Propiedad
                </Link>
            </div>
        </template>

        <!-- Main Content -->
        <div class="py-8 lg:py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <!-- Properties Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <article
                        v-for="property in properties.data"
                        :key="property.id"
                        class="property-card group cursor-pointer"
                        @click="$inertia.visit(route('properties.show', property.id))"
                    >
                        <!-- Property Image Placeholder -->
                        <div class="property-image">
                            <div class="absolute top-4 left-4 z-10 flex flex-wrap gap-2">
                                <span :class="[
                                    'badge-type',
                                    property.type === 'sale' 
                                        ? 'bg-green-500 text-white' 
                                        : 'bg-blue-500 text-white'
                                ]">
                                    {{ property.type === 'sale' ? 'Venta' : 'Alquiler' }}
                                </span>
                                <span :class="[
                                    'badge-category',
                                    getCategoryBadgeClass(property.category)
                                ]">
                                    {{ getCategoryName(property.category) }}
                                </span>
                            </div>
                            
                            <!-- Click to view indicator -->
                            <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-all duration-300 flex items-center justify-center">
                                <div class="bg-white/90 backdrop-blur-sm rounded-full p-3 opacity-0 group-hover:opacity-100 transform scale-75 group-hover:scale-100 transition-all duration-300">
                                    <svg class="w-6 h-6 text-slate-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </div>
                            </div>
                            
                            <div class="w-full h-full bg-gradient-to-br from-slate-50 to-slate-100 flex items-center justify-center">
                                <!-- Property Type Icon -->
                                <div class="text-center">
                                    <svg v-if="property.category === 'house'" class="w-14 h-14 text-slate-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/>
                                    </svg>
                                    <svg v-else-if="property.category === 'apartment'" class="w-14 h-14 text-slate-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.25 21h19.5m-18-18v18m2.25-18v18m13.5-18v18m2.25-18v18M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.75m-.75 3h.75m-.75 3h.75m-3.75-16.5h.75m-.75 3h.75m-.75 3h.75m-3.75-6h.75m-.75 3h.75"/>
                                    </svg>
                                    <svg v-else-if="property.category === 'office'" class="w-14 h-14 text-slate-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.75 21h16.5M4.5 3h15l-.75 18H5.25L4.5 3ZM9 9h6m-6 3h6m-6 3h6M9 17.25h.75a.75.75 0 0 1 .75.75v1.5a.75.75 0 0 1-.75.75h-.75a.75.75 0 0 1-.75-.75v-1.5a.75.75 0 0 1 .75-.75Z"/>
                                    </svg>
                                    <svg v-else class="w-14 h-14 text-slate-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.25 21v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21m0 0h4.5V9.75a.75.75 0 0 1 .75-.75h4.125c1.035 0 1.875.84 1.875 1.875V21H8.25ZM2.25 10.5h5.25a.75.75 0 0 1 .75.75v10.5"/>
                                    </svg>
                                    <p class="text-xs text-slate-500 font-medium">{{ getCategoryName(property.category) }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Property Content -->
                        <div class="p-6 space-y-4">
                            <!-- Price -->
                            <div>
                                <span class="price">
                                    ${{ Number(property.price).toLocaleString() }}
                                    <span class="price-period" v-if="property.type === 'rent'">/mes</span>
                                </span>
                            </div>

                            <!-- Title & Description -->
                            <div class="space-y-3">
                                <h3 class="property-title">
                                    {{ property.title }}
                                </h3>
                                <p class="property-description">
                                    {{ property.description }}
                                </p>
                            </div>

                            <!-- Location -->
                            <div>
                                <div class="flex items-start gap-2 text-slate-600">
                                    <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                    <span class="text-sm leading-relaxed tracking-tight">{{ property.address }}, {{ property.city }}</span>
                                </div>
                            </div>

                            <!-- Property Details -->
                            <div>
                                <div class="flex flex-wrap gap-4 text-sm text-slate-600">
                                    <div class="flex items-center gap-2" v-if="property.bedrooms > 0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"/>
                                        </svg>
                                        <span class="font-medium">{{ property.bedrooms }} hab</span>
                                    </div>
                                    <div class="flex items-center gap-2" v-if="property.bathrooms > 0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 14v3m4-3v3m4-3v3M3 21h18M3 10h18M3 7l9-4 9 4M4 10h16v11H4V10z"/>
                                        </svg>
                                        <span class="font-medium">{{ property.bathrooms }} baños</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 8V6a2 2 0 012-2h12a2 2 0 012 2v2m-6 12V10a2 2 0 00-2-2H8a2 2 0 00-2 2v10m8 0V10h4v10"/>
                                        </svg>
                                        <span class="font-medium">{{ property.area }}m²</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Agent Info -->
                            <div>
                                <div class="flex items-center gap-3 p-3 bg-slate-50/70 rounded-xl border border-slate-100">
                                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                        <span class="text-blue-600 font-semibold text-sm">
                                            {{ property.agent?.name?.charAt(0) }}
                                        </span>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-semibold text-slate-800 truncate leading-relaxed">
                                            {{ property.agent?.name }}
                                        </p>
                                        <p class="text-xs text-slate-500 tracking-tight">
                                            Agente responsable
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <!-- Action Button -->
                            <div class="flex justify-end pt-2" @click.stop>
                                <Link
                                    :href="route('properties.edit', property.id)"
                                    class="btn-outline-sm"
                                    @click.stop
                                    title="Editar propiedad"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                </Link>
                            </div>
                        </div>
                    </article>
                </div>

                <!-- Empty State -->
                <div v-if="!properties.data.length" class="text-center py-16">
                    <div class="max-w-sm mx-auto">
                        <div class="w-16 h-16 bg-slate-100 rounded-2xl flex items-center justify-center mx-auto mb-6">
                            <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"/>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-2">No hay propiedades</h3>
                        <p class="text-gray-500 text-sm leading-relaxed mb-6">
                            Comienza agregando tu primera propiedad
                        </p>
                        <Link
                            :href="route('properties.create')"
                            class="btn-primary"
                        >
                            Nueva Propiedad
                        </Link>
                    </div>
                </div>

                <!-- Pagination -->
                <div class="mt-12" v-if="properties.links.length > 3">
                    <nav class="flex justify-center">
                        <div class="flex gap-2">
                            <Link
                                v-for="link in properties.links"
                                :key="link.label"
                                :href="link.url"
                                v-html="link.label"
                                class="pagination-link"
                                :class="{
                                    'pagination-active': link.active,
                                    'pagination-disabled': !link.url
                                }"
                            />
                        </div>
                    </nav>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    properties: Object,
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

const getCategoryBadgeClass = (category) => {
    const classes = {
        house: 'bg-orange-500 text-white',
        apartment: 'bg-purple-500 text-white',
        office: 'bg-cyan-500 text-white',
        land: 'bg-yellow-500 text-white',
        commercial: 'bg-red-500 text-white'
    };
    return classes[category] || 'bg-slate-600 text-white';
};
</script>

<style scoped>
/* Button Styles */
.btn-primary {
    @apply inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white font-medium text-sm rounded-xl 
           hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 
           transition-all duration-200 ease-in-out transform hover:-translate-y-0.5 hover:shadow-lg;
}

.btn-secondary {
    @apply inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-green-500 text-white font-medium text-sm rounded-xl 
           hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 
           transition-all duration-200 ease-in-out;
}

.btn-outline {
    @apply inline-flex items-center justify-center gap-2 px-3 py-2.5 bg-white border border-slate-200 text-slate-700 font-medium text-sm rounded-xl 
           hover:bg-slate-50 hover:border-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 
           transition-all duration-200 ease-in-out;
}

/* Property Card */
.property-card {
    @apply bg-white rounded-2xl shadow-sm hover:shadow-md border border-slate-100 
           transition-all duration-300 ease-in-out transform hover:-translate-y-1 overflow-hidden
           hover:border-slate-200 hover:bg-slate-50/30 active:transform active:scale-[0.99];
}

/* Button Outline Small */
.btn-outline-sm {
    @apply inline-flex items-center justify-center gap-2 p-2 bg-white/80 backdrop-blur-sm border border-slate-200 text-slate-600 rounded-lg 
           hover:bg-white hover:border-slate-300 hover:text-slate-800 hover:shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 
           transition-all duration-200 ease-in-out opacity-0 group-hover:opacity-100 hover:scale-105;
}

.property-image {
    @apply relative h-48 lg:h-56 overflow-hidden;
}

/* Badges */
.badge-type {
    @apply px-3 py-1.5 text-xs font-bold rounded-full 
           shadow-md border border-white/40 transition-all duration-300 hover:scale-105 hover:shadow-lg;
}

.badge-category {
    @apply px-3 py-1.5 text-xs font-bold rounded-full 
           shadow-md border border-white/40 transition-all duration-300 hover:scale-105 hover:shadow-lg;
}

/* Typography */
.price {
    @apply text-2xl lg:text-3xl font-bold text-green-500 leading-none
           transition-colors duration-300;
}

.property-card:hover .price {
    @apply text-green-600;
}

.price-period {
    @apply text-sm font-normal text-slate-500;
}

.property-title {
    @apply text-lg font-bold text-gray-900 leading-tight text-balance tracking-tight;
}

.property-description {
    @apply text-sm text-slate-600 leading-relaxed line-clamp-2 tracking-tight;
}

/* Pagination */
.pagination-link {
    @apply px-4 py-2 text-sm font-medium rounded-xl border border-slate-200 text-slate-700 
           hover:bg-slate-50 hover:border-slate-300 transition-all duration-200;
}

.pagination-active {
    @apply bg-blue-600 text-white border-blue-600 hover:bg-blue-700 hover:border-blue-700;
}

.pagination-disabled {
    @apply text-slate-400 cursor-not-allowed hover:bg-transparent hover:border-slate-200;
}

/* Utilities */
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.text-balance {
    text-wrap: balance;
}
</style>