<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateVisitRequest extends FormRequest
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
            // Visit subject - must have either property OR project
            'property_id' => 'nullable|exists:properties,id|required_without:project_id',
            'project_id' => 'nullable|exists:projects,id|required_without:property_id',
            
            // Required relationships
            'client_id' => 'sometimes|required|exists:clients,id',
            'agent_id' => 'sometimes|required|exists:agents,id',
            
            // Basic visit information
            'type' => 'sometimes|required|in:showing,inspection,evaluation,follow_up,closing',
            'priority' => 'sometimes|required|in:low,medium,high,urgent',
            'scheduled_at' => 'sometimes|required|date',
            'estimated_duration' => 'sometimes|integer|min:15|max:480',
            'status' => 'sometimes|in:scheduled,completed,cancelled,no_show',
            
            // Contact information
            'client_phone' => 'nullable|string|max:20',
            'client_email' => 'nullable|email|max:255',
            
            // Additional participants
            'additional_participants' => 'nullable|array|max:10',
            'additional_participants.*.name' => 'required|string|max:255',
            'additional_participants.*.phone' => 'nullable|string|max:20',
            'additional_participants.*.role' => 'nullable|string|max:100',
            
            // Reminder settings
            'reminder_hours_before' => 'sometimes|integer|min:1|max:168',
            
            // Completion data
            'actual_duration' => 'nullable|integer|min:1|max:480',
            'completed_at' => 'nullable|date',
            'cancelled_at' => 'nullable|date',
            
            // Outcome and feedback
            'outcome' => 'nullable|in:interested,not_interested,needs_follow_up,offer_made,deal_closed',
            'client_feedback' => 'nullable|string|max:1000',
            'agent_observations' => 'nullable|string|max:1000',
            'client_rating' => 'nullable|integer|min:1|max:5',
            
            // Financial information
            'offered_price' => 'nullable|numeric|min:0|max:999999999999.99',
            'financing_discussed' => 'nullable|string|max:500',
            
            // Follow-up
            'requires_follow_up' => 'sometimes|boolean',
            'follow_up_date' => 'nullable|date|after:today',
            'follow_up_notes' => 'nullable|string|max:500',
            
            // File uploads
            'attachments' => 'nullable|array|max:10',
            'attachments.*' => 'file|max:10240',
            
            // General notes and metadata
            'notes' => 'nullable|string|max:1000',
            'metadata' => 'nullable|array',
            
            // Cancellation
            'cancellation_reason' => 'nullable|string|max:255',
            'cancelled_by' => 'nullable|exists:users,id',
        ];
    }

    public function messages(): array
    {
        return [
            'property_id.required_without' => 'Debe seleccionar una propiedad o un proyecto.',
            'property_id.exists' => 'La propiedad seleccionada no existe.',
            'project_id.required_without' => 'Debe seleccionar un proyecto o una propiedad.',
            'project_id.exists' => 'El proyecto seleccionado no existe.',
            'client_id.required' => 'El cliente es obligatorio.',
            'client_id.exists' => 'El cliente seleccionado no existe.',
            'agent_id.required' => 'El agente es obligatorio.',
            'agent_id.exists' => 'El agente seleccionado no existe.',
            'type.required' => 'El tipo de visita es obligatorio.',
            'type.in' => 'El tipo de visita debe ser uno de los valores permitidos.',
            'priority.required' => 'La prioridad es obligatoria.',
            'priority.in' => 'La prioridad debe ser uno de los valores permitidos.',
            'scheduled_at.required' => 'La fecha y hora de la visita es obligatoria.',
            'scheduled_at.date' => 'La fecha y hora de la visita debe ser una fecha válida.',
            'estimated_duration.min' => 'La duración estimada debe ser de al menos 15 minutos.',
            'estimated_duration.max' => 'La duración estimada no puede exceder 8 horas.',
            'client_email.email' => 'El correo del cliente debe tener un formato válido.',
            'additional_participants.max' => 'Máximo 10 participantes adicionales permitidos.',
            'reminder_hours_before.min' => 'El recordatorio debe ser de al menos 1 hora antes.',
            'reminder_hours_before.max' => 'El recordatorio no puede ser más de 7 días antes.',
            'client_rating.min' => 'La calificación debe ser mínimo 1.',
            'client_rating.max' => 'La calificación debe ser máximo 5.',
            'offered_price.min' => 'El precio ofrecido debe ser mayor a 0.',
            'follow_up_date.after' => 'La fecha de seguimiento debe ser posterior a hoy.',
            'attachments.max' => 'Máximo 10 archivos permitidos.',
            'attachments.*.max' => 'Cada archivo no debe ser mayor a 10MB.',
        ];
    }
}
