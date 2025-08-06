<template>
    <Head :title="`Editar: ${property.title}`" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800 text-balance leading-relaxed">
                        Editar Propiedad
                    </h1>
                    <p class="text-gray-500 text-base leading-relaxed mt-1">
                        {{ property.title }}
                    </p>
                </div>
                <div class="flex gap-3">
                    <Link
                        :href="route('properties.show', property.id)"
                        class="btn-outline"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        Ver propiedad
                    </Link>
                    <Link
                        :href="route('properties.index')"
                        class="btn-outline"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        Volver al listado
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-8 lg:py-12">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                <form @submit.prevent="submit" class="property-form">
                    <div class="form-card">
                        <!-- Información Básica -->
                        <div class="form-section">
                            <h3 class="form-section-title">Información Básica</h3>
                            
                            <div class="form-grid">
                                <div class="form-group col-span-full">
                                    <label for="title" class="form-label">Título de la propiedad</label>
                                    <input
                                        id="title"
                                        v-model="form.title"
                                        type="text"
                                        class="form-input"
                                        :class="{ 'form-input-error': errors.title }"
                                        placeholder="Ej: Casa familiar en las afueras"
                                    />
                                    <p v-if="errors.title" class="form-error">{{ errors.title }}</p>
                                </div>

                                <div class="form-group col-span-full">
                                    <label for="description" class="form-label">Descripción</label>
                                    <textarea
                                        id="description"
                                        v-model="form.description"
                                        rows="4"
                                        class="form-textarea"
                                        :class="{ 'form-input-error': errors.description }"
                                        placeholder="Describe las características principales de la propiedad..."
                                    ></textarea>
                                    <p v-if="errors.description" class="form-error">{{ errors.description }}</p>
                                </div>

                                <div class="form-group">
                                    <label for="price" class="form-label">Precio</label>
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <span class="text-slate-500 sm:text-sm">$</span>
                                        </div>
                                        <input
                                            id="price"
                                            v-model="form.price"
                                            type="number"
                                            step="0.01"
                                            min="0"
                                            class="form-input pl-7"
                                            :class="{ 'form-input-error': errors.price }"
                                            placeholder="350000"
                                        />
                                    </div>
                                    <p v-if="errors.price" class="form-error">{{ errors.price }}</p>
                                </div>

                                <div class="form-group">
                                    <label for="type" class="form-label">Tipo de operación</label>
                                    <select
                                        id="type"
                                        v-model="form.type"
                                        class="form-select"
                                        :class="{ 'form-input-error': errors.type }"
                                    >
                                        <option value="">Selecciona el tipo</option>
                                        <option value="sale">Venta</option>
                                        <option value="rent">Alquiler</option>
                                    </select>
                                    <p v-if="errors.type" class="form-error">{{ errors.type }}</p>
                                </div>

                                <div class="form-group">
                                    <label for="category" class="form-label">Categoría</label>
                                    <select
                                        id="category"
                                        v-model="form.category"
                                        class="form-select"
                                        :class="{ 'form-input-error': errors.category }"
                                    >
                                        <option value="">Selecciona la categoría</option>
                                        <option value="house">Casa</option>
                                        <option value="apartment">Apartamento</option>
                                        <option value="office">Oficina</option>
                                        <option value="land">Terreno</option>
                                        <option value="commercial">Comercial</option>
                                    </select>
                                    <p v-if="errors.category" class="form-error">{{ errors.category }}</p>
                                </div>

                                <div class="form-group">
                                    <label for="status" class="form-label">Estado</label>
                                    <select
                                        id="status"
                                        v-model="form.status"
                                        class="form-select"
                                        :class="{ 'form-input-error': errors.status }"
                                    >
                                        <option value="available">Disponible</option>
                                        <option value="pending">Pendiente</option>
                                        <option value="sold">Vendida</option>
                                        <option value="rented">Alquilada</option>
                                    </select>
                                    <p v-if="errors.status" class="form-error">{{ errors.status }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Ubicación -->
                        <div class="form-section">
                            <h3 class="form-section-title">Ubicación</h3>
                            
                            <div class="form-grid">
                                <div class="form-group col-span-full">
                                    <label for="address" class="form-label">Dirección</label>
                                    <input
                                        id="address"
                                        v-model="form.address"
                                        type="text"
                                        class="form-input"
                                        :class="{ 'form-input-error': errors.address }"
                                        placeholder="Calle Principal 123"
                                    />
                                    <p v-if="errors.address" class="form-error">{{ errors.address }}</p>
                                </div>

                                <div class="form-group">
                                    <label for="city" class="form-label">Ciudad</label>
                                    <input
                                        id="city"
                                        v-model="form.city"
                                        type="text"
                                        class="form-input"
                                        :class="{ 'form-input-error': errors.city }"
                                        placeholder="Madrid"
                                    />
                                    <p v-if="errors.city" class="form-error">{{ errors.city }}</p>
                                </div>

                                <div class="form-group">
                                    <label for="state" class="form-label">Provincia/Estado</label>
                                    <input
                                        id="state"
                                        v-model="form.state"
                                        type="text"
                                        class="form-input"
                                        :class="{ 'form-input-error': errors.state }"
                                        placeholder="Madrid"
                                    />
                                    <p v-if="errors.state" class="form-error">{{ errors.state }}</p>
                                </div>

                                <div class="form-group">
                                    <label for="zip_code" class="form-label">Código Postal</label>
                                    <input
                                        id="zip_code"
                                        v-model="form.zip_code"
                                        type="text"
                                        class="form-input"
                                        :class="{ 'form-input-error': errors.zip_code }"
                                        placeholder="28001"
                                    />
                                    <p v-if="errors.zip_code" class="form-error">{{ errors.zip_code }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Características -->
                        <div class="form-section">
                            <h3 class="form-section-title">Características</h3>
                            
                            <div class="form-grid">
                                <div class="form-group">
                                    <label for="bedrooms" class="form-label">Habitaciones</label>
                                    <input
                                        id="bedrooms"
                                        v-model="form.bedrooms"
                                        type="number"
                                        min="0"
                                        class="form-input"
                                        :class="{ 'form-input-error': errors.bedrooms }"
                                        placeholder="3"
                                    />
                                    <p v-if="errors.bedrooms" class="form-error">{{ errors.bedrooms }}</p>
                                </div>

                                <div class="form-group">
                                    <label for="bathrooms" class="form-label">Baños</label>
                                    <input
                                        id="bathrooms"
                                        v-model="form.bathrooms"
                                        type="number"
                                        min="0"
                                        class="form-input"
                                        :class="{ 'form-input-error': errors.bathrooms }"
                                        placeholder="2"
                                    />
                                    <p v-if="errors.bathrooms" class="form-error">{{ errors.bathrooms }}</p>
                                </div>

                                <div class="form-group">
                                    <label for="area" class="form-label">Área (m²)</label>
                                    <input
                                        id="area"
                                        v-model="form.area"
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        class="form-input"
                                        :class="{ 'form-input-error': errors.area }"
                                        placeholder="120.50"
                                    />
                                    <p v-if="errors.area" class="form-error">{{ errors.area }}</p>
                                </div>

                                <div class="form-group">
                                    <label for="agent_id" class="form-label">Agente responsable</label>
                                    <select
                                        id="agent_id"
                                        v-model="form.agent_id"
                                        class="form-select"
                                        :class="{ 'form-input-error': errors.agent_id }"
                                    >
                                        <option value="">Selecciona un agente</option>
                                        <option
                                            v-for="agent in agents"
                                            :key="agent.id"
                                            :value="agent.id"
                                        >
                                            {{ agent.name }}
                                        </option>
                                    </select>
                                    <p v-if="errors.agent_id" class="form-error">{{ errors.agent_id }}</p>
                                </div>

                                <div class="form-group col-span-full">
                                    <label for="features" class="form-label">Características adicionales</label>
                                    <div class="flex flex-wrap gap-2 mb-3">
                                        <span
                                            v-for="(feature, index) in form.features"
                                            :key="index"
                                            class="feature-tag"
                                        >
                                            {{ feature }}
                                            <button
                                                type="button"
                                                @click="removeFeature(index)"
                                                class="ml-2 text-slate-400 hover:text-red-500"
                                            >
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                                </svg>
                                            </button>
                                        </span>
                                    </div>
                                    <div class="flex gap-2">
                                        <input
                                            v-model="newFeature"
                                            type="text"
                                            class="form-input flex-1"
                                            placeholder="Ej: Jardín, Garaje, Terraza..."
                                            @keyup.enter="addFeature"
                                        />
                                        <button
                                            type="button"
                                            @click="addFeature"
                                            class="btn-outline"
                                        >
                                            Agregar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Botones de acción -->
                        <div class="form-actions">
                            <Link
                                :href="route('properties.show', property.id)"
                                class="btn-secondary"
                            >
                                Cancelar
                            </Link>
                            <button
                                type="button"
                                @click="showDeleteModal = true"
                                class="btn-danger"
                            >
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1-1H9a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Eliminar
                            </button>
                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="btn-primary"
                                :class="{ 'opacity-50 cursor-not-allowed': form.processing }"
                            >
                                <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                {{ form.processing ? 'Guardando...' : 'Guardar Cambios' }}
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Delete Modal -->
                <div v-if="showDeleteModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
                    <div class="bg-white rounded-2xl shadow-xl max-w-md w-full p-6">
                        <div class="flex items-center gap-4 mb-6">
                            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                                <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16c-.77.833.192 2.5 1.732 2.5z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Eliminar Propiedad</h3>
                                <p class="text-sm text-gray-600 mt-1">Esta acción no se puede deshacer</p>
                            </div>
                        </div>
                        
                        <p class="text-gray-700 mb-6">
                            ¿Estás seguro de que deseas eliminar "<strong>{{ property.title }}</strong>"?
                        </p>
                        
                        <div class="flex gap-3 justify-end">
                            <button
                                @click="showDeleteModal = false"
                                class="btn-secondary"
                            >
                                Cancelar
                            </button>
                            <button
                                @click="deleteProperty"
                                :disabled="deleteForm.processing"
                                class="btn-danger"
                                :class="{ 'opacity-50 cursor-not-allowed': deleteForm.processing }"
                            >
                                {{ deleteForm.processing ? 'Eliminando...' : 'Eliminar' }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    property: Object,
    agents: Array,
    errors: Object,
});

const form = useForm({
    title: props.property.title,
    description: props.property.description,
    price: props.property.price,
    type: props.property.type,
    category: props.property.category,
    address: props.property.address,
    city: props.property.city,
    state: props.property.state,
    zip_code: props.property.zip_code,
    bedrooms: props.property.bedrooms,
    bathrooms: props.property.bathrooms,
    area: props.property.area,
    features: props.property.features || [],
    agent_id: props.property.agent_id,
    status: props.property.status,
});

const deleteForm = useForm({});
const newFeature = ref('');
const showDeleteModal = ref(false);

const addFeature = () => {
    if (newFeature.value.trim() && !form.features.includes(newFeature.value.trim())) {
        form.features.push(newFeature.value.trim());
        newFeature.value = '';
    }
};

const removeFeature = (index) => {
    form.features.splice(index, 1);
};

const submit = () => {
    form.put(route('properties.update', props.property.id));
};

const deleteProperty = () => {
    deleteForm.delete(route('properties.destroy', props.property.id));
};
</script>

<style scoped>
/* Form Styles - Same as Create.vue */
.property-form {
    @apply max-w-none;
}

.form-card {
    @apply bg-white rounded-2xl shadow-md border border-slate-200 overflow-hidden;
}

.form-section {
    @apply p-6 lg:p-8 border-b border-slate-100 last:border-b-0;
}

.form-section-title {
    @apply text-xl font-bold text-gray-800 mb-6 text-balance;
}

.form-grid {
    @apply grid grid-cols-1 md:grid-cols-2 gap-6;
}

.form-group {
    @apply flex flex-col;
}

.form-label {
    @apply block text-sm font-semibold text-slate-700 mb-2;
}

.form-input {
    @apply block w-full px-4 py-3 text-slate-900 bg-white border border-slate-200 rounded-xl
           focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent
           transition-all duration-200 placeholder-slate-400;
}

.form-textarea {
    @apply block w-full px-4 py-3 text-slate-900 bg-white border border-slate-200 rounded-xl
           focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent
           transition-all duration-200 placeholder-slate-400;
    resize: vertical;
}

.form-select {
    @apply block w-full px-4 py-3 text-slate-900 bg-white border border-slate-200 rounded-xl
           focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent
           transition-all duration-200;
}

.form-input-error {
    @apply border-red-300 focus:ring-red-500;
}

.form-error {
    @apply mt-1 text-sm text-red-600;
}

.form-actions {
    @apply flex flex-col sm:flex-row gap-4 justify-end p-6 lg:p-8 bg-slate-50;
}

/* Feature Tags */
.feature-tag {
    @apply inline-flex items-center px-3 py-1 text-sm font-medium text-slate-700 bg-slate-100 rounded-full;
}

/* Button Styles */
.btn-primary {
    @apply inline-flex items-center gap-2 px-6 py-3 bg-blue-600 text-white font-medium text-sm rounded-xl 
           hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 
           transition-all duration-200 ease-in-out transform hover:-translate-y-0.5 hover:shadow-lg;
}

.btn-secondary {
    @apply inline-flex items-center justify-center gap-2 px-6 py-3 bg-white border border-slate-200 text-slate-700 font-medium text-sm rounded-xl 
           hover:bg-slate-50 hover:border-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 
           transition-all duration-200 ease-in-out;
}

.btn-outline {
    @apply inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white border border-slate-200 text-slate-700 font-medium text-sm rounded-xl 
           hover:bg-slate-50 hover:border-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 
           transition-all duration-200 ease-in-out;
}

.btn-danger {
    @apply inline-flex items-center gap-2 px-6 py-3 bg-red-600 text-white font-medium text-sm rounded-xl 
           hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 
           transition-all duration-200 ease-in-out;
}
</style>