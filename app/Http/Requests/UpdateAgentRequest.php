<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAgentRequest extends FormRequest
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
        $agentId = $this->route('agent')?->id ?? $this->route('agent');

        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:agents,email,' . $agentId,
            'phone' => 'required|string|max:20',
            'type' => 'required|in:Interno,Externo',
            'bio' => 'nullable|string|max:1000',
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'facebook' => 'nullable|url|max:255',
            'instagram' => 'nullable|url|max:255',
            'linkedin' => 'nullable|url|max:255',
            'gallery' => 'nullable|array|max:10',
            'gallery.*' => 'image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'videos' => 'nullable|array|max:5',
            'videos.*' => 'file|mimes:mp4,mov,avi,wmv,webm|max:102400',
            'remove_gallery' => 'nullable|array',
            'remove_gallery.*' => 'string',
            'remove_videos' => 'nullable|array',
            'remove_videos.*' => 'string',
            'is_active' => 'boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre es obligatorio.',
            'name.string' => 'El nombre debe ser una cadena de texto.',
            'name.max' => 'El nombre no puede exceder 255 caracteres.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Debe proporcionar una dirección de correo válida.',
            'email.unique' => 'Este correo electrónico ya está registrado.',
            'phone.required' => 'El teléfono es obligatorio.',
            'phone.string' => 'El teléfono debe ser una cadena de texto.',
            'phone.max' => 'El teléfono no puede exceder 20 caracteres.',
            'type.required' => 'El tipo de agente es obligatorio.',
            'type.in' => 'El tipo debe ser Interno o Externo.',
            'bio.string' => 'La biografía debe ser una cadena de texto.',
            'bio.max' => 'La biografía no puede exceder 1000 caracteres.',
            'profile_picture.image' => 'La foto de perfil debe ser una imagen.',
            'profile_picture.mimes' => 'La foto de perfil debe ser JPEG, PNG, JPG, GIF o WEBP.',
            'profile_picture.max' => 'La foto de perfil no puede exceder 5MB.',
            'facebook.url' => 'El enlace de Facebook debe ser una URL válida.',
            'facebook.max' => 'El enlace de Facebook no puede exceder 255 caracteres.',
            'instagram.url' => 'El enlace de Instagram debe ser una URL válida.',
            'instagram.max' => 'El enlace de Instagram no puede exceder 255 caracteres.',
            'linkedin.url' => 'El enlace de LinkedIn debe ser una URL válida.',
            'linkedin.max' => 'El enlace de LinkedIn no puede exceder 255 caracteres.',
            'gallery.array' => 'La galería debe ser un array.',
            'gallery.max' => 'La galería no puede tener más de 10 imágenes.',
            'gallery.*.image' => 'Cada archivo de la galería debe ser una imagen.',
            'gallery.*.mimes' => 'Cada imagen de la galería debe ser JPEG, PNG, JPG, GIF o WEBP.',
            'gallery.*.max' => 'Cada imagen de la galería no puede exceder 5MB.',
            'videos.array' => 'Los videos deben ser un array.',
            'videos.max' => 'No puedes subir más de 5 videos.',
            'videos.*.file' => 'Cada video debe ser un archivo válido.',
            'videos.*.mimes' => 'Cada video debe ser MP4, MOV, AVI, WMV o WEBM.',
            'videos.*.max' => 'Cada video no puede exceder 100MB.',
            'is_active.boolean' => 'El estado activo debe ser verdadero o falso.',
        ];
    }
}
