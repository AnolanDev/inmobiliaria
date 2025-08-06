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
            'agent_id' => 'required|exists:agents,id',
            'status' => 'in:available,sold,rented,pending',
        ];
    }
}
