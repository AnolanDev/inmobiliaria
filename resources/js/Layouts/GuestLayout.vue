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
            <div class="relative z-10 flex flex-col justify-center items-center w-full px-12 text-white">
                <div class="text-center">
                    <ApplicationLogo class="h-32 w-auto mb-8 mx-auto filter brightness-0 invert" />
                    <h1 class="text-4xl font-bold mb-4">Tierra Soñada</h1>
                    <p class="text-xl opacity-90 mb-8 leading-relaxed">
                        Tu hogar perfecto te está esperando
                    </p>
                    
                    <!-- Features -->
                    <div class="space-y-4 text-left max-w-md">
                        <div class="flex items-center space-x-3">
                            <div class="w-2 h-2 bg-white rounded-full"></div>
                            <span class="opacity-90">Propiedades exclusivas y únicas</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <div class="w-2 h-2 bg-white rounded-full"></div>
                            <span class="opacity-90">Asesoría personalizada experta</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <div class="w-2 h-2 bg-white rounded-full"></div>
                            <span class="opacity-90">Procesos transparentes y seguros</span>
                        </div>
                        <div class="flex items-center space-x-3">
                            <div class="w-2 h-2 bg-white rounded-full"></div>
                            <span class="opacity-90">Acompañamiento integral 24/7</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Panel - Login Form -->
        <div class="flex-1 flex flex-col justify-center px-4 sm:px-6 lg:flex-none lg:px-20 xl:px-24 bg-gray-50">
            <div class="mx-auto w-full max-w-md lg:w-96">
                <!-- Mobile Logo -->
                <div class="lg:hidden text-center mb-8">
                    <Link href="/">
                        <ApplicationLogo class="h-16 w-auto mx-auto" />
                    </Link>
                </div>

                <!-- Login Form Container -->
                <div class="bg-white p-8 rounded-2xl shadow-xl border border-gray-200">
                    <slot />
                </div>

                <!-- Footer -->
                <div class="mt-8 text-center">
                    <p class="text-sm text-gray-500">
                        © 2025 Tierra Soñada. Todos los derechos reservados.
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>
