# Guía de Colores Estandarizados - InmoApp

## Color Principal
**Verde Esmeralda: `#00bf63`**
- Este es el color principal de la aplicación
- Se usa para elementos activos, exitosos y destacados

## Sistema de Colores para Etiquetas

### 1. Estados Positivos/Activos/Exitosos
- **Color:** Verde `#00bf63`
- **Aplicar a:**
  - `active`, `published`, `new`, `available`, `qualified`, `sent`, `scheduled`
  - Prioridad `low` (baja prioridad = verde)
  - Estados completados exitosos

### 2. Estados Completados/Aprobados 
- **Color:** Verde oscuro `#059669`
- **Aplicar a:**
  - `completed`, `approved`, `converted`, `sold`, `rented`
  - Estados finalizados con éxito

### 3. Estados Pendientes/En Proceso
- **Color:** Amarillo `#d97706`
- **Fondo:** `#fef3c7`
- **Aplicar a:**
  - `pending`, `contacted`, `sending`, `draft`, `paused`
  - Prioridad `medium`
  - Estados que requieren atención

### 4. Estados de Error/Cancelado/Rechazado
- **Color:** Rojo `#dc2626`
- **Fondo:** `#fee2e2`
- **Aplicar a:**
  - `cancelled`, `rejected`, `lost`, `failed`
  - Prioridad `high`, `urgent`
  - Estados de error

### 5. Estados Informativos
- **Color:** Azul `#2563eb`
- **Fondo:** `#dbeafe`
- **Aplicar a:**
  - `call`, `phone`, `rented`, `info`
  - Estados informativos neutrales

### 6. Estados Especiales/Premium
- **Color:** Púrpura `#7c3aed`
- **Fondo:** `#ede9fe`
- **Aplicar a:**
  - `meeting`, `social`, `premium`, `vip`
  - Estados especiales o de alta categoría

### 7. Estados Inactivos/Borrador
- **Color:** Gris `#6b7280`
- **Fondo:** `#f3f4f6`
- **Aplicar a:**
  - `inactive`, `draft`, `neutral`, `walk_in`, `print`
  - Estados sin definir o neutrales

## Tipos de Actividades

| Tipo | Color Principal | Fondo |
|------|----------------|-------|
| `email` | Verde `#00bf63` | Verde claro |
| `call` | Azul | Azul claro |
| `meeting` | Púrpura | Púrpura claro |
| `task` | Gris | Gris claro |
| `sms` | Verde `#00bf63` | Verde claro |
| `whatsapp` | Verde `#00bf63` | Verde claro |

## Fuentes de Leads

| Fuente | Color |
|--------|-------|
| `website` | Verde (principal) |
| `email` | Verde (principal) |
| `social` | Púrpura |
| `phone` | Azul |
| `referral` | Amarillo |
| `digital_ads` | Rojo |
| `walk_in` | Gris |
| `print` | Gris |

## Implementación

### Usando el componente StatusTag
```vue
<StatusTag 
  status="completed" 
  icon="check" 
  size="md" 
  variant="default" 
/>
```

### Usando estilos inline (para casos específicos)
```vue
<span 
  class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
  :style="{ backgroundColor: '#00bf63', color: 'white' }"
>
  Activo
</span>
```

### Usando utilidades de JavaScript
```javascript
import { getTagClasses, getTailwindClasses } from '@/Utils/tagColors.js'

// Para estilos inline
const style = getTagClasses('completed', 'solid')

// Para clases Tailwind (compatibilidad)
const classes = getTailwindClasses('completed', 'default')
```

## Cambios Aplicados

✅ **Completado:**
- Sistema de colores creado (`tagColors.js`)
- Componente `StatusTag.vue` creado
- Colores azules cambiados por verde `#00bf63`
- Header y navegación actualizados
- Archivo `Activities/Index.vue` actualizado

🔄 **En progreso:**
- Estandarización de etiquetas en todos los archivos
- Migración a componente `StatusTag` donde sea posible

## Próximos Pasos

1. Reemplazar etiquetas hardcodeadas con el componente `StatusTag`
2. Aplicar estilos inline con `#00bf63` donde corresponda
3. Verificar consistencia en todas las páginas
4. Documentar casos especiales

Este sistema garantiza que todos los colores sean consistentes con el verde `#00bf63` como color principal de la aplicación.