<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVisitRequest extends FormRequest
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
            'property_id' => 'required|exists:properties,id',
            'client_id' => 'required|exists:clients,id',
            'agent_id' => 'required|exists:agents,id',
            'scheduled_at' => 'required|date|after:now',
            'status' => 'in:scheduled,completed,cancelled,no_show',
            'notes' => 'nullable|string',
        ];
    }
}
