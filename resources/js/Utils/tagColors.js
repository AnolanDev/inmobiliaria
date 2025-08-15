/**
 * Sistema de colores estandarizado para etiquetas y estados
 * Basado en el color principal #00bf63 (verde)
 */

// Paleta de colores principal
export const colors = {
  // Verde principal - Estados positivos/activos/exitosos
  primary: {
    bg: '#00bf6315',     // Verde muy claro
    text: '#00bf63',     // Verde principal
    border: '#00bf6330'  // Verde con transparencia
  },
  
  // Verde oscuro - Estados completados/aprobados
  success: {
    bg: '#059669',       // Verde más oscuro
    text: '#ffffff',     // Blanco
    border: '#059669'
  },
  
  // Amarillo - Estados pendientes/en proceso
  warning: {
    bg: '#fef3c7',       // Amarillo claro
    text: '#d97706',     // Amarillo oscuro
    border: '#fcd34d'    // Amarillo medio
  },
  
  // Rojo - Estados de error/cancelado/rechazado
  danger: {
    bg: '#fee2e2',       // Rojo claro
    text: '#dc2626',     // Rojo oscuro
    border: '#fca5a5'    // Rojo medio
  },
  
  // Azul - Estados informativos
  info: {
    bg: '#dbeafe',       // Azul claro
    text: '#2563eb',     // Azul oscuro
    border: '#93c5fd'    // Azul medio
  },
  
  // Púrpura - Estados especiales/premium
  purple: {
    bg: '#ede9fe',       // Púrpura claro
    text: '#7c3aed',     // Púrpura oscuro
    border: '#c4b5fd'    // Púrpura medio
  },
  
  // Gris - Estados inactivos/borrador
  neutral: {
    bg: '#f3f4f6',       // Gris claro
    text: '#6b7280',     // Gris oscuro
    border: '#d1d5db'    // Gris medio
  }
}

// Mapeo de estados a colores
export const statusColors = {
  // Estados generales
  'active': 'primary',
  'inactive': 'neutral',
  'pending': 'warning',
  'completed': 'success',
  'cancelled': 'danger',
  'approved': 'success',
  'rejected': 'danger',
  'draft': 'neutral',
  'published': 'primary',
  
  // Estados de leads
  'new': 'primary',
  'contacted': 'warning',
  'qualified': 'info',
  'converted': 'success',
  'lost': 'danger',
  
  // Estados de campañas
  'sending': 'warning',
  'sent': 'success',
  'failed': 'danger',
  'scheduled': 'info',
  'paused': 'neutral',
  
  // Estados de actividades
  'call': 'info',
  'email': 'primary',
  'meeting': 'purple',
  'task': 'neutral',
  'event': 'warning',
  
  // Prioridades
  'high': 'danger',
  'medium': 'warning',
  'low': 'primary',
  
  // Fuentes de leads
  'website': 'primary',
  'referral': 'warning',
  'phone': 'info',
  'email': 'primary',
  'social': 'purple',
  'walk_in': 'neutral',
  'digital_ads': 'danger',
  'print': 'neutral',
  
  // Estados de propiedades
  'available': 'primary',
  'sold': 'success',
  'rented': 'info',
  'reserved': 'warning'
}

// Función para obtener las clases CSS de una etiqueta
export function getTagClasses(status, variant = 'default') {
  const colorKey = statusColors[status] || 'neutral'
  const color = colors[colorKey]
  
  if (variant === 'solid') {
    return {
      backgroundColor: color.text,
      color: '#ffffff',
      borderColor: color.text
    }
  }
  
  return {
    backgroundColor: color.bg,
    color: color.text,
    borderColor: color.border
  }
}

// Función para obtener clases Tailwind (para compatibilidad)
export function getTailwindClasses(status, variant = 'default') {
  const colorKey = statusColors[status] || 'neutral'
  
  const tailwindMap = {
    primary: variant === 'solid' ? 'bg-green-600 text-white border-green-600' : 'bg-green-50 text-green-700 border-green-200',
    success: variant === 'solid' ? 'bg-green-700 text-white border-green-700' : 'bg-green-100 text-green-800 border-green-300',
    warning: variant === 'solid' ? 'bg-yellow-600 text-white border-yellow-600' : 'bg-yellow-50 text-yellow-700 border-yellow-200',
    danger: variant === 'solid' ? 'bg-red-600 text-white border-red-600' : 'bg-red-50 text-red-700 border-red-200',
    info: variant === 'solid' ? 'bg-blue-600 text-white border-blue-600' : 'bg-blue-50 text-blue-700 border-blue-200',
    purple: variant === 'solid' ? 'bg-purple-600 text-white border-purple-600' : 'bg-purple-50 text-purple-700 border-purple-200',
    neutral: variant === 'solid' ? 'bg-gray-600 text-white border-gray-600' : 'bg-gray-50 text-gray-700 border-gray-200'
  }
  
  return tailwindMap[colorKey] || tailwindMap.neutral
}

export default {
  colors,
  statusColors,
  getTagClasses,
  getTailwindClasses
}