<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EmailTemplate extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'subject',
        'description',
        'html_content',
        'text_content',
        'variables',
        'category',
        'status',
        'is_system_template',
        'metadata',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'variables' => 'array',
        'metadata' => 'array',
        'is_system_template' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    // Constants
    const CATEGORIES = [
        'welcome' => 'Bienvenida',
        'follow_up' => 'Seguimiento',
        'newsletter' => 'Newsletter',
        'promotion' => 'Promocional',
        'notification' => 'Notificación',
        'transactional' => 'Transaccional',
        'general' => 'General'
    ];

    const STATUSES = [
        'draft' => 'Borrador',
        'active' => 'Activo',
        'archived' => 'Archivado'
    ];

    // Default variables available in all templates
    const DEFAULT_VARIABLES = [
        'recipient_name' => 'Nombre del destinatario',
        'recipient_email' => 'Email del destinatario',
        'company_name' => 'Nombre de la empresa',
        'company_address' => 'Dirección de la empresa',
        'company_phone' => 'Teléfono de la empresa',
        'current_date' => 'Fecha actual',
        'unsubscribe_url' => 'URL de unsubscribe'
    ];

    // Lead-specific variables
    const LEAD_VARIABLES = [
        'lead_first_name' => 'Nombre del lead',
        'lead_last_name' => 'Apellido del lead',
        'lead_full_name' => 'Nombre completo del lead',
        'lead_status' => 'Estado del lead',
        'lead_source' => 'Fuente del lead',
        'lead_budget_min' => 'Presupuesto mínimo',
        'lead_budget_max' => 'Presupuesto máximo',
        'lead_interests' => 'Intereses del lead',
        'assigned_agent_name' => 'Nombre del agente asignado'
    ];

    // Relationships
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function emailCampaigns(): HasMany
    {
        return $this->hasMany(EmailCampaign::class);
    }

    // Accessors
    public function getFormattedCategoryAttribute(): string
    {
        return self::CATEGORIES[$this->category] ?? $this->category;
    }

    public function getFormattedStatusAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function getAvailableVariablesAttribute(): array
    {
        $defaultVars = self::DEFAULT_VARIABLES;
        
        // Add template-specific variables
        if ($this->variables) {
            $customVars = array_flip($this->variables);
            $defaultVars = array_merge($defaultVars, $customVars);
        }

        // Add lead variables for lead-related templates
        if (in_array($this->category, ['welcome', 'follow_up'])) {
            $defaultVars = array_merge($defaultVars, self::LEAD_VARIABLES);
        }

        return $defaultVars;
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    public function scopeSystemTemplates($query)
    {
        return $query->where('is_system_template', true);
    }

    public function scopeUserTemplates($query)
    {
        return $query->where('is_system_template', false);
    }

    // Methods
    public function renderContent(array $variables = []): array
    {
        $mergedVariables = array_merge($this->getDefaultVariableValues(), $variables);
        
        return [
            'subject' => $this->replaceVariables($this->subject, $mergedVariables),
            'html_content' => $this->replaceVariables($this->html_content, $mergedVariables),
            'text_content' => $this->text_content ? $this->replaceVariables($this->text_content, $mergedVariables) : null
        ];
    }

    private function replaceVariables(string $content, array $variables): string
    {
        foreach ($variables as $key => $value) {
            $content = str_replace('{{' . $key . '}}', $value ?? '', $content);
        }
        
        return $content;
    }

    private function getDefaultVariableValues(): array
    {
        return [
            'company_name' => config('app.name', 'InmoApp'),
            'current_date' => now()->format('d/m/Y'),
            'unsubscribe_url' => url('/unsubscribe/{{tracking_token}}')
        ];
    }

    public function duplicate(string $newName): static
    {
        $duplicate = $this->replicate();
        $duplicate->name = $newName;
        $duplicate->status = 'draft';
        $duplicate->is_system_template = false;
        $duplicate->created_by = auth()->id();
        $duplicate->updated_by = null;
        $duplicate->save();

        return $duplicate;
    }

    public function canBeDeleted(): bool
    {
        return !$this->is_system_template && $this->emailCampaigns()->count() === 0;
    }
}