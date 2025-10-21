<template>
  <Head title="Proyectos" />

  <AuthenticatedLayout>
    <template #header>
      <div class="flex justify-between items-center">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
          Proyectos
        </h2>
        <Link
          :href="route('projects.create')"
          class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring focus:ring-green-300 disabled:opacity-25 transition ease-in-out duration-150"
        >
          <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
          </svg>
          Nuevo Proyecto
        </Link>
      </div>
    </template>

    <div class="py-12">
      <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Filters and Search -->
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg mb-6">
          <div class="p-6 border-b border-gray-200">
            <!-- Primera fila: Búsqueda y botones de vista -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-4">
              <!-- Search -->
              <div class="relative flex-1 max-w-md">
                <input
                  v-model="search"
                  type="text"
                  placeholder="Buscar proyectos..."
                  class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500"
                  @input="performSearch"
                />
                <svg class="absolute left-3 top-2.5 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
              </div>

              <!-- View Toggle -->
              <div class="flex items-center space-x-2 flex-shrink-0">
                <button
                  @click="currentView = 'cards'"
                  :class="[
                    'px-3 py-2 text-sm font-medium rounded-md transition-colors',
                    currentView === 'cards'
                      ? 'bg-green-600 text-white'
                      : 'text-gray-500 hover:text-gray-700'
                  ]"
                >
                  Tarjetas
                </button>
                <button
                  @click="currentView = 'table'"
                  :class="[
                    'px-3 py-2 text-sm font-medium rounded-md transition-colors',
                    currentView === 'table'
                      ? 'bg-green-600 text-white'
                      : 'text-gray-500 hover:text-gray-700'
                  ]"
                >
                  Tabla
                </button>
                <button
                  @click="currentView = 'sort'"
                  :class="[
                    'px-3 py-2 text-sm font-medium rounded-md transition-colors',
                    currentView === 'sort'
                      ? 'bg-green-600 text-white'
                      : 'text-gray-500 hover:text-gray-700'
                  ]"
                >
                  Ordenar
                </button>
              </div>
            </div>

            <!-- Segunda fila: Filtros -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-3">
              <!-- Type Filter -->
              <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Tipo</label>
                <select
                  v-model="selectedType"
                  @change="applyFilters"
                  class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-green-500 focus:border-green-500"
                >
                  <option value="">Todos</option>
                  <option v-for="(label, value) in types" :key="value" :value="value">
                    {{ label }}
                  </option>
                </select>
              </div>

              <!-- Status Filter -->
              <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Estado</label>
                <select
                  v-model="selectedStatus"
                  @change="applyFilters"
                  class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-green-500 focus:border-green-500"
                >
                  <option value="">Todos</option>
                  <option v-for="(label, value) in statuses" :key="value" :value="value">
                    {{ label }}
                  </option>
                </select>
              </div>

              <!-- State Filter -->
              <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Departamento</label>
                <select
                  v-model="selectedState"
                  @change="applyFilters"
                  class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-green-500 focus:border-green-500"
                >
                  <option value="">Todos</option>
                  <option v-for="state in states" :key="state" :value="state">
                    {{ state }}
                  </option>
                </select>
              </div>

              <!-- City Filter -->
              <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Ciudad</label>
                <select
                  v-model="selectedCity"
                  @change="applyFilters"
                  class="w-full border border-gray-300 rounded-md px-3 py-2 text-sm focus:ring-green-500 focus:border-green-500"
                >
                  <option value="">Todas</option>
                  <option v-for="city in cities" :key="city" :value="city">
                    {{ city }}
                  </option>
                </select>
              </div>

              <!-- Clear Filters -->
              <div class="flex items-end">
                <button
                  v-if="hasFilters"
                  @click="clearFilters"
                  class="w-full px-3 py-2 text-sm text-gray-500 hover:text-gray-700 hover:bg-gray-50 border border-gray-300 rounded-md transition-colors"
                >
                  Limpiar filtros
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Projects Display -->
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
          <!-- Sort View (Drag & Drop) -->
          <div v-if="currentView === 'sort'">
            <DraggableProjectTable 
              :projects="projects.data" 
              @order-updated="handleOrderUpdated"
            />
          </div>

          <!-- Table View -->
          <div v-if="currentView === 'table'" class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Proyecto
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Tipo
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Estado
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Propiedades
                  </th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Acciones
                  </th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="project in projects.data" :key="project.id" class="hover:bg-gray-50">
                  <td class="px-6 py-4 whitespace-nowrap">
                    <div class="flex items-center">
                      <div class="flex-shrink-0 h-16 w-16">
                        <img 
                          :src="project.cover_image_url" 
                          :alt="project.name"
                          class="h-16 w-16 rounded-lg object-cover"
                        />
                      </div>
                      <div class="ml-4">
                        <div class="text-sm font-medium text-gray-900">
                          {{ project.name }}
                        </div>
                        <div class="text-sm text-gray-500" v-if="project.description">
                          {{ truncateText(project.description, 50) }}
                        </div>
                      </div>
                    </div>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span :class="['inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium', getTypeColor(project.type)]">
                      {{ project.type }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span :class="['inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium', getStatusColor(project.status)]">
                      {{ project.status }}
                    </span>
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                    {{ project.properties_count || 0 }}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                    <Link
                      :href="route('projects.show', project.id)"
                      class="text-green-600 hover:text-green-900"
                    >
                      Ver
                    </Link>
                    <Link
                      :href="route('projects.edit', project.id)"
                      class="text-indigo-600 hover:text-indigo-900"
                    >
                      Editar
                    </Link>
                    <button
                      @click="confirmDelete(project)"
                      class="text-red-600 hover:text-red-900"
                    >
                      Eliminar
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Cards View -->
          <div v-else class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              <div
                v-for="project in projects.data"
                :key="project.id"
                class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden hover:shadow-lg hover:scale-[1.02] transition-all duration-300 group relative"
              >
                <!-- Clickable area for navigation -->
                <Link
                  :href="route('projects.show', project.id)"
                  class="cursor-pointer block"
                >
                <!-- Image -->
                <div class="aspect-video relative overflow-hidden">
                  <img
                    :src="project.cover_image_url"
                    :alt="project.name"
                    class="w-full h-full object-cover"
                  />
                  <div class="absolute top-4 right-4 flex space-x-2">
                    <span :class="['inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium', getTypeColor(project.type)]">
                      {{ project.type }}
                    </span>
                  </div>
                  <div class="absolute bottom-4 left-4">
                    <span :class="['inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium', getStatusColor(project.status)]">
                      {{ project.status }}
                    </span>
                  </div>
                </div>

                <!-- Content -->
                <div class="p-6">
                  <h3 class="text-lg font-semibold text-gray-900 mb-2">
                    {{ project.name }}
                  </h3>
                  <p v-if="project.description" class="text-gray-600 text-sm mb-4">
                    {{ truncateText(project.description, 100) }}
                  </p>
                  
                  <!-- Stats -->
                  <!-- <div class="flex items-center text-sm text-gray-500 mb-4">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-2m-2 0H7m10 0v-2c0-.553-.447-1-1-1s-1 .447-1 1v2m1-10V9a2 2 0 00-2-2M9 7h3M9 11h3M9 15h3"/>
                    </svg>
                    {{ project.properties_count || 0 }} propiedades
                  </div> -->

                  <!-- View details text -->
                  <div class="text-center">
                    <span class="inline-flex items-center text-green-600 group-hover:text-green-700 font-semibold transition-colors">
                      Ver detalles
                      <svg class="w-4 h-4 ml-2 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                      </svg>
                    </span>
                  </div>
                </div>
                </Link>

                <!-- Actions (outside of clickable area) -->
                <div class="absolute bottom-6 right-6 flex space-x-2 z-10">
                  <Link
                    :href="route('projects.edit', project.id)"
                    class="inline-flex items-center p-2 text-green-600 hover:text-green-800 hover:bg-green-50 rounded-lg transition-all duration-200"
                    title="Editar proyecto"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                  </Link>
                  <button
                    @click="confirmDelete(project)"
                    class="inline-flex items-center p-2 text-red-600 hover:text-red-800 hover:bg-red-50 rounded-lg transition-all duration-200"
                    title="Eliminar proyecto"
                  >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                  </button>
                </div>
              </div>
            </div>
          </div>

          <!-- Empty State -->
          <div v-if="projects.data.length === 0" class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No hay proyectos</h3>
            <p class="mt-1 text-sm text-gray-500">Comienza creando un nuevo proyecto.</p>
            <div class="mt-6">
              <Link
                :href="route('projects.create')"
                class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500"
              >
                <svg class="-ml-1 mr-2 h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z" clip-rule="evenodd"/>
                </svg>
                Nuevo Proyecto
              </Link>
            </div>
          </div>
        </div>

        <!-- Pagination -->
        <div v-if="projects.last_page && projects.last_page > 1" class="mt-6">
          <!-- Pagination Info -->
          <div class="flex justify-center mb-4">
            <p class="text-sm text-gray-700">
              Mostrando
              <span class="font-medium">{{ projects.from }}</span>
              a
              <span class="font-medium">{{ projects.to }}</span>
              de
              <span class="font-medium">{{ projects.total }}</span>
              resultados
            </p>
          </div>
          <!-- Pagination Controls -->
          <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
            <!-- Previous Button -->
            <div class="flex items-center">
              <Link
                v-if="projects.prev_page_url"
                :href="projects.prev_page_url"
                class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-l-md hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-green-500 focus:border-green-500 transition-colors"
                aria-label="Página anterior"
              >
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
                Anterior
              </Link>
              <span
                v-else
                class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-400 bg-gray-100 border border-gray-300 rounded-l-md cursor-not-allowed"
              >
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" />
                </svg>
                Anterior
              </span>
            </div>

            <!-- Page Numbers (Smart pagination) -->
            <nav class="flex items-center space-x-1">
              <!-- First page -->
              <Link
                v-if="props.projects.current_page > 3"
                :href="getPageUrl(1)"
                class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-green-500 focus:border-green-500 transition-colors"
              >
                1
              </Link>
              
              <!-- Dots before current page group -->
              <span v-if="props.projects.current_page > 4" class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-gray-400">
                ...
              </span>

              <!-- Page numbers around current page -->
              <template v-for="page in getPaginationRange()" :key="page">
                <Link
                  v-if="page !== props.projects.current_page"
                  :href="getPageUrl(page)"
                  class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-green-500 focus:border-green-500 transition-colors"
                >
                  {{ page }}
                </Link>
                <span
                  v-else
                  class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-white bg-green-600 border border-green-600 cursor-default"
                  aria-current="page"
                >
                  {{ page }}
                </span>
              </template>

              <!-- Dots after current page group -->
              <span v-if="props.projects.current_page < props.projects.last_page - 3" class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-gray-400">
                ...
              </span>

              <!-- Last page -->
              <Link
                v-if="props.projects.current_page < props.projects.last_page - 2"
                :href="getPageUrl(props.projects.last_page)"
                class="relative inline-flex items-center px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-green-500 focus:border-green-500 transition-colors"
              >
                {{ props.projects.last_page }}
              </Link>
            </nav>

            <!-- Next Button -->
            <div class="flex items-center">
              <Link
                v-if="projects.next_page_url"
                :href="projects.next_page_url"
                class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-r-md hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-green-500 focus:border-green-500 transition-colors"
                aria-label="Página siguiente"
              >
                Siguiente
                <svg class="w-5 h-5 ml-2" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                </svg>
              </Link>
              <span
                v-else
                class="relative inline-flex items-center px-4 py-2 text-sm font-medium text-gray-400 bg-gray-100 border border-gray-300 rounded-r-md cursor-not-allowed"
              >
                Siguiente
                <svg class="w-5 h-5 ml-2" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                </svg>
              </span>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <Modal :show="showDeleteModal" @close="showDeleteModal = false" max-width="2xl">
      <div class="p-6">
        <!-- Header -->
        <div class="flex items-start space-x-4">
          <div class="flex-shrink-0">
            <div class="flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
              <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
              </svg>
            </div>
          </div>
          <div class="flex-1 min-w-0">
            <h3 class="text-lg font-semibold text-gray-900 mb-2">
              ¿Eliminar proyecto?
            </h3>
            <div class="text-sm text-gray-600 space-y-3">
              <p class="font-medium">
                Estás a punto de eliminar permanentemente el proyecto:
              </p>
              <div class="bg-gray-50 rounded-lg p-4 border-l-4 border-red-400">
                <div class="flex items-start space-x-3">
                  <img 
                    v-if="projectToDelete?.cover_image_url" 
                    :src="projectToDelete.cover_image_url" 
                    :alt="projectToDelete.name"
                    class="w-16 h-16 rounded-lg object-cover flex-shrink-0"
                  />
                  <div class="flex-1 min-w-0">
                    <p class="font-semibold text-gray-900 truncate">
                      {{ projectToDelete?.name }}
                    </p>
                    <p class="text-sm text-gray-600 mt-1">
                      Tipo: <span class="font-medium">{{ projectToDelete?.type }}</span>
                    </p>
                    <p class="text-sm text-gray-600">
                      Estado: <span class="font-medium">{{ projectToDelete?.status }}</span>
                    </p>
                    <p v-if="projectToDelete?.properties_count > 0" class="text-sm text-gray-600">
                      Propiedades asociadas: <span class="font-medium text-red-600">{{ projectToDelete.properties_count }}</span>
                    </p>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Warning content -->
        <div class="mt-6">
          <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <div class="flex">
              <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-red-400" fill="currentColor" viewBox="0 0 20 20">
                  <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                </svg>
              </div>
              <div class="ml-3">
                <h4 class="text-sm font-medium text-red-800">
                  ⚠️ Esta acción es irreversible
                </h4>
                <div class="mt-2 text-sm text-red-700">
                  <ul class="list-disc list-inside space-y-1">
                    <li>Se eliminará el proyecto y toda su información</li>
                    <li>Se borrarán todas las imágenes y archivos multimedia</li>
                    <li v-if="projectToDelete?.properties_count > 0">
                      Las <strong>{{ projectToDelete.properties_count }} propiedades</strong> asociadas quedarán sin proyecto
                    </li>
                    <li>Esta acción no se puede deshacer</li>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Confirmation input (optional for extra security) -->
        <div class="mt-6" v-if="projectToDelete?.properties_count > 0">
          <label class="block text-sm font-medium text-gray-700 mb-2">
            Para confirmar, escribe el nombre del proyecto:
          </label>
          <input
            v-model="deleteConfirmationText"
            type="text"
            :placeholder="projectToDelete?.name"
            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-red-500 focus:border-red-500"
          />
        </div>

        <!-- Actions -->
        <div class="mt-8 flex flex-col sm:flex-row-reverse gap-3">
          <button
            @click="deleteProject"
            :disabled="projectToDelete?.properties_count > 0 && deleteConfirmationText !== projectToDelete?.name"
            :class="[
              'w-full sm:w-auto inline-flex justify-center items-center px-6 py-3 text-sm font-semibold rounded-lg transition-all duration-200',
              (projectToDelete?.properties_count > 0 && deleteConfirmationText !== projectToDelete?.name)
                ? 'bg-gray-300 text-gray-500 cursor-not-allowed'
                : 'bg-red-600 text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 hover:shadow-lg'
            ]"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
            </svg>
            Sí, eliminar proyecto
          </button>
          <button
            @click="showDeleteModal = false; deleteConfirmationText = ''"
            class="w-full sm:w-auto inline-flex justify-center items-center px-6 py-3 text-sm font-semibold text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-colors"
          >
            Cancelar
          </button>
        </div>
      </div>
    </Modal>
  </AuthenticatedLayout>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { Head, Link, router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import ToggleView from '@/Components/ToggleView.vue'
import Modal from '@/Components/Modal.vue'
import DraggableProjectTable from '@/Components/Projects/DraggableProjectTable.vue'
import { debounce } from 'lodash'

const props = defineProps({
  projects: Object,
  filters: Object,
  types: Object,
  statuses: Object,
  states: {
    type: Array,
    default: () => []
  },
  cities: {
    type: Array,
    default: () => []
  }
})

// State
const currentView = ref('cards')
const search = ref(props.filters.search || '')
const selectedType = ref(props.filters.type || '')
const selectedStatus = ref(props.filters.status || '')
const selectedState = ref(props.filters.state || '')
const selectedCity = ref(props.filters.city || '')
const showDeleteModal = ref(false)
const projectToDelete = ref(null)
const deleteConfirmationText = ref('')

// Computed
const hasFilters = computed(() => {
  return search.value || selectedType.value || selectedStatus.value || selectedState.value || selectedCity.value
})

// Methods
const performSearch = debounce(() => {
  applyFilters()
}, 300)

const applyFilters = () => {
  router.get(route('projects.index'), {
    search: search.value,
    type: selectedType.value,
    status: selectedStatus.value,
    state: selectedState.value,
    city: selectedCity.value
  }, {
    preserveState: true,
    replace: true
  })
}

const clearFilters = () => {
  search.value = ''
  selectedType.value = ''
  selectedStatus.value = ''
  selectedState.value = ''
  selectedCity.value = ''
  router.get(route('projects.index'))
}

const getTypeColor = (type) => {
  const colors = {
    'Campestres': 'bg-green-100 text-green-800',
    'Urbanos': 'bg-green-100 text-green-800',
    'Turísticos': 'bg-purple-100 text-purple-800'
  }
  return colors[type] || 'bg-gray-100 text-gray-800'
}

const getStatusColor = (status) => {
  const colors = {
    'Vendido': 'bg-red-100 text-red-800',
    'Disponible': 'bg-green-100 text-green-800',
    'Reservado': 'bg-yellow-100 text-yellow-800'
  }
  return colors[status] || 'bg-gray-100 text-gray-800'
}

const truncateText = (text, length) => {
  if (!text) return ''
  return text.length > length ? text.substring(0, length) + '...' : text
}

const confirmDelete = (project) => {
  projectToDelete.value = project
  deleteConfirmationText.value = ''
  showDeleteModal.value = true
}

const deleteProject = () => {
  if (projectToDelete.value) {
    router.delete(route('projects.destroy', projectToDelete.value.id), {
      onSuccess: () => {
        showDeleteModal.value = false
        projectToDelete.value = null
        deleteConfirmationText.value = ''
      }
    })
  }
}

const handleOrderUpdated = () => {
  // Refresh the page to show updated order
  router.reload({ only: ['projects'] })
}

const getPaginationAriaLabel = (link) => {
  if (link.label.includes('Previous')) {
    return 'Ir a la página anterior'
  } else if (link.label.includes('Next')) {
    return 'Ir a la página siguiente'
  } else if (link.active) {
    return `Página actual, página ${link.label}`
  } else if (link.url) {
    return `Ir a la página ${link.label}`
  } else {
    return `Página ${link.label} no disponible`
  }
}

const getPaginationRange = () => {
  const current = props.projects.current_page
  const last = props.projects.last_page
  const range = []
  
  // Show 2 pages before and after current page
  const start = Math.max(1, current - 2)
  const end = Math.min(last, current + 2)
  
  for (let i = start; i <= end; i++) {
    range.push(i)
  }
  
  return range
}

const getPageUrl = (page) => {
  const url = new URL(window.location.href)
  url.searchParams.set('page', page)
  return url.toString()
}
</script>