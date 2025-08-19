<template>
  <Head title="Crear Blog" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex items-center space-x-4">
        <Link
          :href="route('blogs.index')"
          class="text-gray-400 hover:text-gray-600 transition-colors duration-200"
        >
          <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
          </svg>
        </Link>
        <div>
          <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Crear Nuevo Blog
          </h2>
          <p class="text-sm text-gray-600 mt-1">
            Completa la información para crear un nuevo artículo de blog
          </p>
        </div>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <BlogForm
          :categories="categories"
          :statuses="statuses"
        />
      </div>
    </div>
  </AuthenticatedLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import BlogForm from '@/Components/Blogs/BlogForm.vue'

defineProps({
  categories: {
    type: Object,
    required: true
  },
  statuses: {
    type: Object,
    required: true
  }
})
</script>