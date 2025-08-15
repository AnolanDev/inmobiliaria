<template>
    <span 
        :class="[
            'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border transition-all duration-200',
            size === 'sm' ? 'px-2 py-0.5 text-xs' : '',
            size === 'lg' ? 'px-3 py-1 text-sm' : '',
            clickable ? 'cursor-pointer hover:opacity-80' : ''
        ]"
        :style="computedStyle"
        @click="clickable ? $emit('click') : null"
    >
        <!-- Icono opcional -->
        <svg 
            v-if="icon" 
            :class="[
                'mr-1.5',
                size === 'sm' ? 'h-3 w-3' : 'h-4 w-4'
            ]" 
            fill="none" 
            stroke="currentColor" 
            viewBox="0 0 24 24"
        >
            <!-- Icono de éxito/completado -->
            <path 
                v-if="icon === 'check'" 
                stroke-linecap="round" 
                stroke-linejoin="round" 
                stroke-width="2" 
                d="M5 13l4 4L19 7"
            />
            <!-- Icono de advertencia/pendiente -->
            <path 
                v-else-if="icon === 'clock'" 
                stroke-linecap="round" 
                stroke-linejoin="round" 
                stroke-width="2" 
                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"
            />
            <!-- Icono de error/cancelado -->
            <path 
                v-else-if="icon === 'x'" 
                stroke-linecap="round" 
                stroke-linejoin="round" 
                stroke-width="2" 
                d="M6 18L18 6M6 6l12 12"
            />
            <!-- Icono de información -->
            <path 
                v-else-if="icon === 'info'" 
                stroke-linecap="round" 
                stroke-linejoin="round" 
                stroke-width="2" 
                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
            />
            <!-- Icono de usuario -->
            <path 
                v-else-if="icon === 'user'" 
                stroke-linecap="round" 
                stroke-linejoin="round" 
                stroke-width="2" 
                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"
            />
            <!-- Icono de teléfono -->
            <path 
                v-else-if="icon === 'phone'" 
                stroke-linecap="round" 
                stroke-linejoin="round" 
                stroke-width="2" 
                d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"
            />
            <!-- Icono de email -->
            <path 
                v-else-if="icon === 'mail'" 
                stroke-linecap="round" 
                stroke-linejoin="round" 
                stroke-width="2" 
                d="M3 8l7.89 7.89a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"
            />
        </svg>
        
        <!-- Texto de la etiqueta -->
        <slot>{{ formatLabel(status) }}</slot>
    </span>
</template>

<script setup>
import { computed } from 'vue'
import { getTagClasses } from '@/Utils/tagColors.js'

const props = defineProps({
    status: {
        type: String,
        required: true
    },
    variant: {
        type: String,
        default: 'default', // 'default' | 'solid'
        validator: (value) => ['default', 'solid'].includes(value)
    },
    size: {
        type: String,
        default: 'md', // 'sm' | 'md' | 'lg'
        validator: (value) => ['sm', 'md', 'lg'].includes(value)
    },
    icon: {
        type: String,
        default: null // 'check' | 'clock' | 'x' | 'info' | 'user' | 'phone' | 'mail'
    },
    clickable: {
        type: Boolean,
        default: false
    }
})

defineEmits(['click'])

// Estilos computados basados en el sistema de colores
const computedStyle = computed(() => {
    return getTagClasses(props.status, props.variant)
})

// Función para formatear las etiquetas
const formatLabel = (status) => {
    const labels = {
        'active': 'Activo',
        'inactive': 'Inactivo',
        'pending': 'Pendiente',
        'completed': 'Completado',
        'cancelled': 'Cancelado',
        'approved': 'Aprobado',
        'rejected': 'Rechazado',
        'draft': 'Borrador',
        'published': 'Publicado',
        'new': 'Nuevo',
        'contacted': 'Contactado',
        'qualified': 'Calificado',
        'converted': 'Convertido',
        'lost': 'Perdido',
        'sending': 'Enviando',
        'sent': 'Enviado',
        'failed': 'Fallido',
        'scheduled': 'Programado',
        'paused': 'Pausado',
        'call': 'Llamada',
        'email': 'Email',
        'meeting': 'Reunión',
        'task': 'Tarea',
        'event': 'Evento',
        'high': 'Alta',
        'medium': 'Media',
        'low': 'Baja',
        'website': 'Sitio Web',
        'referral': 'Referido',
        'phone': 'Teléfono',
        'social': 'Redes Sociales',
        'walk_in': 'Visita Directa',
        'digital_ads': 'Publicidad Digital',
        'print': 'Impreso',
        'available': 'Disponible',
        'sold': 'Vendido',
        'rented': 'Alquilado',
        'reserved': 'Reservado'
    }
    
    return labels[status] || status.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase())
}
</script>