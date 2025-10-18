<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProjectRequest extends FormRequest
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
            'name' => 'required|string|max:255|unique:projects,name',
            'description' => 'nullable|string',
            'type' => 'required|in:Campestres,Urbanos,Turísticos',
            'status' => 'required|in:Vendido,Disponible,Reservado',
            'property_count' => 'nullable|integer|min:0',
            'is_public' => 'nullable|boolean',
            'sort_order' => 'nullable|integer|min:0',
            'city' => 'required|string|max:255',
            'state' => 'required|string|max:255',
            'cover_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'gallery' => 'nullable|array',
            'gallery.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'videos' => 'nullable|array',
            'videos.*' => 'file|mimes:mp4,mov,avi,wmv,webm|max:102400',
        ];
    }

    /**
     * Get custom validation messages.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'El nombre del proyecto es obligatorio.',
            'name.unique' => 'Ya existe un proyecto con este nombre.',
            'type.required' => 'El tipo de proyecto es obligatorio.',
            'type.in' => 'El tipo de proyecto debe ser Campestres, Urbanos o Turísticos.',
            'status.required' => 'El estado del proyecto es obligatorio.',
            'status.in' => 'El estado debe ser Vendido, Disponible o Reservado.',
            'cover_image.image' => 'El archivo de portada debe ser una imagen.',
            'cover_image.max' => 'La imagen de portada no debe superar los 5MB.',
            'gallery.*.image' => 'Todos los archivos de la galería deben ser imágenes.',
            'gallery.*.max' => 'Las imágenes de la galería no deben superar los 5MB cada una.',
            'videos.*.mimes' => 'Los videos deben ser de formato MP4, MOV, AVI, WMV o WEBM.',
            'videos.*.max' => 'Los videos no deben superar los 100MB cada uno.',
            'city.required' => 'La ciudad es obligatoria.',
            'city.max' => 'La ciudad no debe superar los 255 caracteres.',
            'state.required' => 'El departamento es obligatorio.',
            'state.max' => 'El departamento no debe superar los 255 caracteres.',
        ];
    }
}
