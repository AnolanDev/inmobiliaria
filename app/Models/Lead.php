<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Lead extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'status',
        'source',
        'priority',
        'notes',
        'interests',
        'budget_min',
        'budget_max',
        'preferred_location',
        'contact_preferences',
        'last_contact_date',
        'next_follow_up',
        'campaign_id',
        'assigned_agent_id',
        'converted_client_id',
        'created_by',
        'unsubscribed',
        'unsubscribed_at',
        'unsubscribe_reason',
    ];

    protected $casts = [
        'interests' => 'array',
        'contact_preferences' => 'array',
        'budget_min' => 'decimal:2',
        'budget_max' => 'decimal:2',
        'last_contact_date' => 'datetime',
        'next_follow_up' => 'datetime',
        'unsubscribed' => 'boolean',
        'unsubscribed_at' => 'datetime',
    ];

    const STATUSES = [
        'new' => 'Nuevo',
        'contacted' => 'Contactado',
        'qualified' => 'Calificado',
        'converted' => 'Convertido',
        'lost' => 'Perdido'
    ];

    const SOURCES = [
        'website' => 'Sitio Web',
        'social' => 'Redes Sociales',
        'referral' => 'Referido',
        'campaign' => 'Campaña',
        'phone' => 'Llamada',
        'walk_in' => 'Visita Oficina',
        'other' => 'Otro'
    ];

    const PRIORITIES = [
        'low' => 'Baja',
        'medium' => 'Media',
        'high' => 'Alta'
    ];

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class);
    }

    public function assignedAgent(): BelongsTo
    {
        return $this->belongsTo(Agent::class, 'assigned_agent_id');
    }

    public function convertedClient(): BelongsTo
    {
        return $this->belongsTo(Client::class, 'converted_client_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function activities(): MorphMany
    {
        return $this->morphMany(Activity::class, 'related');
    }

    public function getFullNameAttribute(): string
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function getFormattedStatusAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function getFormattedSourceAttribute(): string
    {
        return self::SOURCES[$this->source] ?? $this->source;
    }

    public function getFormattedPriorityAttribute(): string
    {
        return self::PRIORITIES[$this->priority] ?? $this->priority;
    }

    public function getBudgetRangeAttribute(): string
    {
        if ($this->budget_min && $this->budget_max) {
            return "$" . number_format($this->budget_min) . " - $" . number_format($this->budget_max);
        } elseif ($this->budget_min) {
            return "Desde $" . number_format($this->budget_min);
        } elseif ($this->budget_max) {
            return "Hasta $" . number_format($this->budget_max);
        }
        return 'No especificado';
    }

    public function isOverdue(): bool
    {
        return $this->next_follow_up && $this->next_follow_up < now();
    }

    public function scopeOverdue($query)
    {
        return $query->where('next_follow_up', '<', now());
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeBySource($query, $source)
    {
        return $query->where('source', $source);
    }

    public function convertToClient(): Client
    {
        $client = Client::create([
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'notes' => $this->notes,
            'source' => $this->source,
            'assigned_agent_id' => $this->assigned_agent_id,
        ]);

        $this->update([
            'status' => 'converted',
            'converted_client_id' => $client->id,
        ]);

        return $client;
    }

    // Métodos para actividades
    public function addActivity(array $data): Activity
    {
        return $this->activities()->create(array_merge($data, [
            'user_id' => auth()->id(),
            'assigned_to' => $data['assigned_to'] ?? auth()->id()
        ]));
    }

    public function getRecentActivities($limit = 5)
    {
        return $this->activities()
            ->with(['user', 'assignedUser'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    public function getPendingActivities()
    {
        return $this->activities()
            ->pending()
            ->with(['user', 'assignedUser'])
            ->orderBy('scheduled_at')
            ->get();
    }

    public function getOverdueActivities()
    {
        return $this->activities()
            ->overdue()
            ->with(['user', 'assignedUser'])
            ->orderBy('scheduled_at')
            ->get();
    }

    public function updateLastContact(): void
    {
        $this->update(['last_contact_date' => now()]);
    }

    // Email Marketing methods
    public function unsubscribe($reason = null): void
    {
        $this->update([
            'unsubscribed' => true,
            'unsubscribed_at' => now(),
            'unsubscribe_reason' => $reason
        ]);
    }

    public function resubscribe(): void
    {
        $this->update([
            'unsubscribed' => false,
            'unsubscribed_at' => null,
            'unsubscribe_reason' => null
        ]);
    }

    public function isSubscribed(): bool
    {
        return !$this->unsubscribed;
    }

    public function scopeSubscribed($query)
    {
        return $query->where('unsubscribed', false);
    }

    public function scopeUnsubscribed($query)
    {
        return $query->where('unsubscribed', true);
    }

    public function scopeEmailMarketing($query)
    {
        return $query->where('unsubscribed', false)
                    ->whereNotNull('email')
                    ->where('email', '!=', '');
    }
}
