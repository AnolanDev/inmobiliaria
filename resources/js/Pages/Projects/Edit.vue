<template>
  <Head title="Editar Proyecto" />

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
        <div class="flex-1">
          <div class="flex items-center justify-between">
            <div>
              <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Editar Proyecto
              </h2>
              <p class="text-sm text-gray-600 mt-1">
                {{ project.name }}
              </p>
            </div>
            <div class="flex items-center space-x-2">
              <Link
                :href="route('projects.show', project.id)"
                class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
              >
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                Ver proyecto
              </Link>
            </div>
          </div>
        </div>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <ProjectForm
          :project="project"
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
  project: {
    type: Object,
    required: true
  },
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