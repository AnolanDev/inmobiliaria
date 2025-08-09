<template>
  <Head title="Crear Proyecto" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center space-x-4">
        <Link
          :href="route('projects.index')"
          class="text-gray-400 hover:text-gray-600 transition-colors duration-200"
        >
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
          </svg>
        </Link>
        <div>
          <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Crear Nuevo Proyecto
          </h2>
          <p class="text-sm text-gray-600 mt-1">
            Completa la información para crear un nuevo proyecto inmobiliario
          </p>
        </div>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <ProjectForm
          :types="types"
          :statuses="statuses"
        />
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import ProjectForm from '@/Components/Projects/ProjectForm.vue'

defineProps({
  types: {
    type: Object,
    required: true
  },
  statuses: {
    type: Object,
    required: true
  }
})
</script>