<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Activity extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'type',
        'subject',
        'description',
        'status',
        'priority',
        'scheduled_at',
        'completed_at',
        'duration',
        'metadata',
        'related_type',
        'related_id',
        'user_id',
        'assigned_to',
        'has_reminder',
        'reminder_at',
        'reminder_sent',
        'is_follow_up',
        'parent_activity_id'
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'completed_at' => 'datetime',
        'reminder_at' => 'datetime',
        'metadata' => 'array',
        'has_reminder' => 'boolean',
        'reminder_sent' => 'boolean',
        'is_follow_up' => 'boolean'
    ];

    // Tipos de actividades disponibles
    const TYPES = [
        'call' => 'Llamada',
        'email' => 'Email',
        'meeting' => 'Reunión',
        'note' => 'Nota',
        'task' => 'Tarea',
        'sms' => 'SMS',
        'whatsapp' => 'WhatsApp',
        'visit' => 'Visita',
        'proposal' => 'Propuesta',
        'contract' => 'Contrato'
    ];

    const STATUSES = [
        'pending' => 'Pendiente',
        'completed' => 'Completada',
        'cancelled' => 'Cancelada'
    ];

    const PRIORITIES = [
        'low' => 'Baja',
        'medium' => 'Media',
        'high' => 'Alta',
        'urgent' => 'Urgente'
    ];

    // Relaciones
    public function related(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function parentActivity(): BelongsTo
    {
        return $this->belongsTo(Activity::class, 'parent_activity_id');
    }

    public function followUpActivities(): HasMany
    {
        return $this->hasMany(Activity::class, 'parent_activity_id');
    }

    // Accessors
    protected function formattedType(): Attribute
    {
        return Attribute::make(
            get: fn () => self::TYPES[$this->type] ?? $this->type
        );
    }

    protected function formattedStatus(): Attribute
    {
        return Attribute::make(
            get: fn () => self::STATUSES[$this->status] ?? $this->status
        );
    }

    protected function formattedPriority(): Attribute
    {
        return Attribute::make(
            get: fn () => self::PRIORITIES[$this->priority] ?? $this->priority
        );
    }

    protected function isOverdue(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->scheduled_at && $this->scheduled_at->isPast() && $this->status === 'pending'
        );
    }

    protected function isDueToday(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->scheduled_at && $this->scheduled_at->isToday()
        );
    }

    protected function isDueTomorrow(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->scheduled_at && $this->scheduled_at->isTomorrow()
        );
    }

    // Métodos de negocio
    public function markAsCompleted(): void
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now()
        ]);
    }

    public function markAsCancelled(): void
    {
        $this->update([
            'status' => 'cancelled'
        ]);
    }

    public function createFollowUp(array $data): self
    {
        return self::create(array_merge($data, [
            'is_follow_up' => true,
            'parent_activity_id' => $this->id,
            'related_type' => $this->related_type,
            'related_id' => $this->related_id
        ]));
    }

    public function setReminder(\DateTime $reminderTime): void
    {
        $this->update([
            'has_reminder' => true,
            'reminder_at' => $reminderTime,
            'reminder_sent' => false
        ]);
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeOverdue($query)
    {
        return $query->where('status', 'pending')
                    ->where('scheduled_at', '<', now());
    }

    public function scopeDueToday($query)
    {
        return $query->where('status', 'pending')
                    ->whereDate('scheduled_at', today());
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where(function ($q) use ($userId) {
            $q->where('user_id', $userId)
              ->orWhere('assigned_to', $userId);
        });
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeNeedsReminder($query)
    {
        return $query->where('has_reminder', true)
                    ->where('reminder_sent', false)
                    ->where('reminder_at', '<=', now());
    }

    public function scopeForLead($query, $leadId)
    {
        return $query->where('related_type', Lead::class)
                    ->where('related_id', $leadId);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
}