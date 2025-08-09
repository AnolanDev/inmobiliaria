<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePropertyRequest extends FormRequest
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
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'type' => 'required|in:sale,rent',
            'category' => 'required|in:house,apartment,office,land,commercial',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:100',
            'state' => 'required|string|max:100',
            'zip_code' => 'nullable|string|max:20',
            'bedrooms' => 'integer|min:0',
            'bathrooms' => 'integer|min:0',
            'area' => 'required|numeric|min:0',
            'images' => 'nullable|array',
            'images.*' => 'string',
            'features' => 'nullable|array',
            'features.*' => 'string',
            'agent_id' => 'nullable|exists:agents,id',
            'project_id' => 'nullable|exists:projects,id',
            'status' => 'in:available,sold,rented,pending',
            
            // Media fields
            'cover_image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120', // 5MB max
            'gallery' => 'nullable|array|max:10',
            'gallery.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'videos' => 'nullable|array|max:5',
            'videos.*' => 'mimes:mp4,mov,avi,wmv,webm|max:102400', // 100MB max
            
            // For updates - files to remove
            'remove_gallery' => 'nullable|array',
            'remove_gallery.*' => 'string',
            'remove_videos' => 'nullable|array',
            'remove_videos.*' => 'string',
        ];
    }

    public function messages(): array
    {
        return [
            'cover_image.required' => 'La imagen de portada es obligatoria.',
            'cover_image.image' => 'La imagen de portada debe ser un archivo de imagen.',
            'cover_image.mimes' => 'La imagen de portada debe ser de tipo: jpeg, png, jpg, gif o webp.',
            'cover_image.max' => 'La imagen de portada no debe ser mayor a 5MB.',
            
            'gallery.max' => 'Máximo 10 imágenes permitidas en la galería.',
            'gallery.*.image' => 'Todos los archivos de galería deben ser imágenes.',
            'gallery.*.mimes' => 'Las imágenes de galería deben ser de tipo: jpeg, png, jpg, gif o webp.',
            'gallery.*.max' => 'Cada imagen de galería no debe ser mayor a 5MB.',
            
            'videos.max' => 'Máximo 5 videos permitidos.',
            'videos.*.mimes' => 'Los videos deben ser de tipo: mp4, mov, avi, wmv o webm.',
            'videos.*.max' => 'Cada video no debe ser mayor a 100MB.',
            
            'project_id.exists' => 'El proyecto seleccionado no existe.',
        ];
    }
}
