<template>
    <div class="p-8">
        <h1 class="text-2xl font-bold mb-4">Debug Usuario</h1>
        
        <div class="bg-white p-6 rounded-lg shadow">
            <h2 class="text-lg font-semibold mb-4">Información del Usuario</h2>
            <pre class="bg-gray-100 p-4 rounded text-sm overflow-auto">{{ JSON.stringify(user, null, 2) }}</pre>
        </div>

        <div class="bg-white p-6 rounded-lg shadow mt-6">
            <h2 class="text-lg font-semibold mb-4">Verificación de Permisos</h2>
            <div class="space-y-2">
                <div>
                    <strong>Es super admin:</strong> {{ user?.is_super_admin ? 'Sí' : 'No' }}
                </div>
                <div>
                    <strong>Tiene permiso users-view:</strong> 
                    {{ user?.permissions?.some(p => p.slug === 'users-view') ? 'Sí' : 'No' }}
                </div>
                <div>
                    <strong>Tiene permiso roles-view:</strong> 
                    {{ user?.permissions?.some(p => p.slug === 'roles-view') ? 'Sí' : 'No' }}
                </div>
                <div>
                    <strong>Total permisos:</strong> {{ user?.permissions?.length || 0 }}
                </div>
            </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow mt-6">
            <h2 class="text-lg font-semibold mb-4">Permisos (primeros 10)</h2>
            <div class="grid grid-cols-2 gap-2 text-sm">
                <div v-for="permission in (user?.permissions || []).slice(0, 10)" :key="permission.slug">
                    <strong>{{ permission.slug }}</strong> - {{ permission.name }}
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { usePage } from '@inertiajs/vue3'
import { computed } from 'vue'

const page = usePage()
const user = computed(() => page.props.auth?.user)
</script>