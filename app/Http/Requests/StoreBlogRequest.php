<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBlogRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255|unique:blogs,title',
            'slug' => 'nullable|string|max:255|unique:blogs,slug',
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required|string',
            'cover_image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'gallery' => 'nullable|array',
            'gallery.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'author' => 'nullable|string|max:255',
            'category' => 'required|string|in:inmobiliario,mercado,consejos,inversion,legal,financiacion,noticias',
            'tags' => 'nullable|array',
            'tags.*' => 'string|max:50',
            'status' => 'required|in:draft,published,archived',
            'is_public' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
            'published_at' => 'nullable|date|after_or_equal:now',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|array',
            'meta_keywords.*' => 'string|max:50',
        ];
    }

    /**
     * Get custom attribute names for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'title' => 'título',
            'slug' => 'slug',
            'excerpt' => 'resumen',
            'content' => 'contenido',
            'cover_image' => 'imagen de portada',
            'gallery' => 'galería',
            'author' => 'autor',
            'category' => 'categoría',
            'tags' => 'etiquetas',
            'status' => 'estado',
            'is_public' => 'público',
            'sort_order' => 'orden',
            'published_at' => 'fecha de publicación',
            'meta_title' => 'título SEO',
            'meta_description' => 'descripción SEO',
            'meta_keywords' => 'palabras clave SEO',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'title.required' => 'El título es obligatorio.',
            'title.unique' => 'Ya existe un blog con este título.',
            'slug.unique' => 'Ya existe un blog con este slug.',
            'content.required' => 'El contenido es obligatorio.',
            'cover_image.required' => 'La imagen de portada es obligatoria.',
            'cover_image.image' => 'El archivo debe ser una imagen.',
            'cover_image.max' => 'La imagen no puede ser mayor a 5MB.',
            'category.required' => 'La categoría es obligatoria.',
            'category.in' => 'La categoría seleccionada no es válida.',
            'status.required' => 'El estado es obligatorio.',
            'status.in' => 'El estado seleccionado no es válido.',
            'published_at.after_or_equal' => 'La fecha de publicación debe ser posterior o igual a hoy.',
        ];
    }
}
