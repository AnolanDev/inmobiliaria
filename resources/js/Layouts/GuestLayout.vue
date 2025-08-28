<script setup>
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="min-h-screen flex">
        <!-- Left Panel - Branding -->
        <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-green-600 via-green-700 to-green-800 relative overflow-hidden">
            <!-- Background Pattern -->
            <div class="absolute inset-0 opacity-10">
                <svg class="w-full h-full" viewBox="0 0 100 100" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <defs>
                        <pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse">
                            <path d="M 10 0 L 0 0 0 10" fill="none" stroke="white" stroke-width="0.5"/>
                        </pattern>
                    </defs>
                    <rect width="100%" height="100%" fill="url(#grid)" />
                </svg>
            </div>
            
            <!-- Content -->
            <div class="relative z-10 flex flex-col justify-center w-full px-10 text-white py-12">
                <div class="max-w-md mx-auto">
                    <!-- Header Section -->
                    <div class="mb-8">
                        <div class="rounded-2xl p-4 mb-6 inline-block">
                            <ApplicationLogo class="h-16 w-auto filter brightness-0 invert drop-shadow-lg" />
                        </div>
                        <h1 class="text-3xl font-bold mb-3 leading-tight text-white">Tierra Soñada</h1>
                        <p class="text-lg text-white/95 leading-relaxed font-medium">
                            Transformamos sueños en realidad inmobiliaria
                        </p>
                    </div>
                    
                    <!-- Features Section -->
                    <div class="space-y-5">
                        <h3 class="text-xl font-bold mb-5 text-white">¿Por qué elegirnos?</h3>
                        <div class="space-y-4">
                            <div class="flex items-start space-x-4">
                                <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5 backdrop-blur-sm">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-bold text-white text-base">Propiedades exclusivas</p>
                                    <p class="text-sm text-white/80 leading-relaxed">Catálogo único y seleccionado con las mejores opciones del mercado</p>
                                </div>
                            </div>
                            <div class="flex items-start space-x-4">
                                <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5 backdrop-blur-sm">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-bold text-white text-base">Asesoría especializada</p>
                                    <p class="text-sm text-white/80 leading-relaxed">Expertos certificados en el mercado inmobiliario local</p>
                                </div>
                            </div>
                            <div class="flex items-start space-x-4">
                                <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5 backdrop-blur-sm">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-bold text-white text-base">Procesos transparentes</p>
                                    <p class="text-sm text-white/80 leading-relaxed">Sin sorpresas ni costos ocultos, total claridad en cada paso</p>
                                </div>
                            </div>
                            <div class="flex items-start space-x-4">
                                <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5 backdrop-blur-sm">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192L5.636 18.364M12 2.25A9.75 9.75 0 002.25 12c0 5.384 4.365 9.75 9.75 9.75s9.75-4.366 9.75-9.75S17.635 2.25 12 2.25zM12 6v12"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-bold text-white text-base">Soporte 24/7</p>
                                    <p class="text-sm text-white/80 leading-relaxed">Te acompañamos en cada momento de tu proceso de compra</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Call to Action -->
                    <div class="mt-8 pt-6 border-t border-white/20">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center flex-shrink-0 backdrop-blur-sm">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                            </div>
                            <p class="text-sm text-white/90 font-medium">
                                Más de <span class="font-bold text-green-200 text-base">1,000 familias</span> ya encontraron su hogar ideal
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Panel - Login Form -->
        <div class="flex-1 flex flex-col justify-center px-4 sm:px-6 lg:flex-none lg:px-10 xl:px-10 bg-gray-50 py-12">
            <div class="w-full max-w-md">
                <!-- Mobile Logo -->
                <div class="lg:hidden text-center mb-6">
                    <ApplicationLogo class="h-12 w-auto mx-auto" />
                </div>

                <!-- Login Form Container -->
                <div class="bg-white p-6 rounded-2xl shadow-xl border border-gray-200">
                    <slot />
                </div>

                <!-- Footer -->
                <div class="mt-6 text-center">
                    <p class="text-sm text-gray-500">
                        © 2025 Tierra Soñada. Todos los derechos reservados.
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>
