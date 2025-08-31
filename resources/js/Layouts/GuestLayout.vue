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
            <div class="relative z-10 flex flex-col justify-center w-full px-8 text-white py-8">
                <div class="max-w-sm mx-auto">
                    <!-- Header Section -->
                    <div class="mb-6">
                        <div class="rounded-xl p-2 mb-4 inline-block">
                            <ApplicationLogo class="h-32 w-auto filter brightness-0 invert drop-shadow-lg" />
                        </div>
                        <h1 class="text-2xl font-bold mb-2 leading-tight text-white">Tierra Soñada</h1>
                        <p class="text-base text-white/95 leading-relaxed font-medium">
                            Transformamos sueños en realidad inmobiliaria
                        </p>
                    </div>
                    
                    <!-- Features Section -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-bold mb-3 text-white">¿Por qué elegirnos?</h3>
                        <div class="space-y-3">
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center flex-shrink-0 backdrop-blur-sm">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                                    </svg>
                                </div>
                                <p class="font-medium text-white text-sm">Propiedades exclusivas y únicas</p>
                            </div>
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center flex-shrink-0 backdrop-blur-sm">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                    </svg>
                                </div>
                                <p class="font-medium text-white text-sm">Asesoría personalizada experta</p>
                            </div>
                            <div class="flex items-center space-x-3">
                                <div class="w-8 h-8 bg-white/20 rounded-full flex items-center justify-center flex-shrink-0 backdrop-blur-sm">
                                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <p class="font-medium text-white text-sm">Procesos transparentes y seguros</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Call to Action -->
                    <div class="mt-5 pt-4 border-t border-white/20">
                        <p class="text-xs text-white/90 text-center font-medium">
                            Más de <span class="font-bold text-green-200">1,000 familias</span> confían en nosotros
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Panel - Login Form -->
        <div class="flex-1 flex items-center justify-center bg-gray-50 py-6">
            <div class="w-full max-w-sm px-4 sm:px-6 lg:px-8 flex flex-col items-center">
                <!-- Mobile Logo -->
                <div class="lg:hidden text-center mb-4">
                    <ApplicationLogo class="h-14 w-auto mx-auto" />
                </div>

                <!-- Login Form Container -->
                <div class="bg-white p-5 rounded-xl shadow-lg border border-gray-200">
                    <slot />
                </div>

                <!-- Footer -->
                <div class="mt-3 text-center">
                    <p class="text-xs text-gray-500">
                        Created by: Gustavo Olivera
                    </p>
                    <p class="text-xs text-gray-500 mt-1">
                        © 2025 Tierra Soñada. All rights reserved.
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>
