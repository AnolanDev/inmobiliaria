<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateClientRequest extends FormRequest
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
        $clientId = $this->route('client')->id;
        
        return [
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('clients', 'email')->ignore($clientId)],
            'phone' => 'nullable|string|max:20',
            'secondary_phone' => 'nullable|string|max:20',
            'document_type' => 'required|in:cedula,cedula_extranjeria,pasaporte,nit,tarjeta_identidad',
            'document_number' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'birth_date' => 'nullable|date|before:today',
            'occupation' => 'nullable|string|max:255',
            'notes' => 'nullable|string',
            'interest_level' => 'required|in:low,medium,high',
            'status' => 'required|in:prospecto,activo,inactivo',
            'preferred_contact_method' => 'required|in:phone,email,whatsapp,both',
            
            // File uploads
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'attachments' => 'nullable|array|max:10',
            'attachments.*' => 'file|max:10240', // 10MB max per file
            
            // For removing attachments
            'remove_attachments' => 'nullable|array',
            'remove_attachments.*' => 'integer',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre completo es obligatorio.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'El correo electrónico debe tener un formato válido.',
            'email.unique' => 'Este correo electrónico ya está registrado.',
            'document_type.required' => 'El tipo de documento es obligatorio.',
            'birth_date.before' => 'La fecha de nacimiento debe ser anterior a hoy.',
            'profile_image.image' => 'La foto de perfil debe ser una imagen.',
            'profile_image.mimes' => 'La foto de perfil debe ser de tipo: jpeg, png, jpg, gif o webp.',
            'profile_image.max' => 'La foto de perfil no debe ser mayor a 2MB.',
            'attachments.max' => 'Máximo 10 archivos permitidos.',
            'attachments.*.max' => 'Cada archivo no debe ser mayor a 10MB.',
        ];
    }
}