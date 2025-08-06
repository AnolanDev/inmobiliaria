<template>
    <Link
        :href="href"
        :class="[
            'group flex items-center gap-3 px-3 py-2.5 text-sm font-medium rounded-xl transition-all duration-200',
            active
                ? 'bg-blue-50 text-blue-700 border border-blue-100'
                : 'text-gray-700 hover:bg-gray-50 hover:text-gray-900'
        ]"
        @click="$emit('click')"
    >
        <div 
            :class="[
                'flex items-center justify-center w-5 h-5 transition-colors',
                active ? 'text-blue-600' : 'text-gray-500 group-hover:text-gray-700'
            ]"
        >
            <!-- Dashboard Icon -->
            <svg v-if="icon === 'dashboard'" fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" 
                      d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z" />
            </svg>

            <!-- Properties Icon -->
            <svg v-else-if="icon === 'properties'" fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" 
                      d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" 
                      d="m15 13-3 3-3-3" />
            </svg>

            <!-- Agents Icon -->
            <svg v-else-if="icon === 'agents'" fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" 
                      d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>

            <!-- Clients Icon -->
            <svg v-else-if="icon === 'clients'" fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" 
                      d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>

            <!-- Visits Icon -->
            <svg v-else-if="icon === 'visits'" fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" 
                      d="M8 7V3a2 2 0 012-2h4a2 2 0 012 2v4m-6 0V5a2 2 0 012-2h4a2 2 0 012 2v2m-6 0h8m-6 0v10a2 2 0 002 2h2a2 2 0 002-2V7" />
            </svg>

            <!-- Statistics Icon -->
            <svg v-else-if="icon === 'statistics'" fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" 
                      d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>

            <!-- Settings Icon -->
            <svg v-else-if="icon === 'settings'" fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" 
                      d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" 
                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
        </div>

        <span :class="['truncate', active ? 'font-semibold' : '']">{{ name }}</span>
        
        <!-- Active Indicator -->
        <div v-if="active" class="ml-auto w-1.5 h-1.5 bg-blue-600 rounded-full"></div>
    </Link>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'

defineProps({
    href: {
        type: String,
        required: true
    },
    active: {
        type: Boolean,
        default: false
    },
    icon: {
        type: String,
        required: true
    },
    name: {
        type: String,
        required: true
    }
})

defineEmits(['click'])
</script>