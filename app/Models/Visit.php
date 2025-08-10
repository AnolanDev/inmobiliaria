<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class Visit extends Model
{
    protected $fillable = [
        'property_id',
        'project_id',
        'client_id',
        'agent_id',
        'type',
        'priority',
        'scheduled_at',
        'estimated_duration',
        'actual_duration',
        'completed_at',
        'cancelled_at',
        'status',
        'client_phone',
        'client_email',
        'additional_participants',
        'reminder_sent',
        'reminder_sent_at',
        'reminder_hours_before',
        'outcome',
        'client_feedback',
        'agent_observations',
        'client_rating',
        'offered_price',
        'financing_discussed',
        'attachments',
        'requires_follow_up',
        'follow_up_date',
        'follow_up_notes',
        'metadata',
        'notes',
        'cancellation_reason',
        'cancelled_by',
    ];

    protected $casts = [
        'scheduled_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'reminder_sent_at' => 'datetime',
        'follow_up_date' => 'date',
        'reminder_sent' => 'boolean',
        'requires_follow_up' => 'boolean',
        'estimated_duration' => 'integer',
        'actual_duration' => 'integer',
        'reminder_hours_before' => 'integer',
        'client_rating' => 'integer',
        'offered_price' => 'decimal:2',
        'additional_participants' => 'array',
        'attachments' => 'array',
        'metadata' => 'array',
    ];

    // Relationships
    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    public function cancelledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    // Accessors
    public function getStatusColorAttribute(): string
    {
        $colors = [
            'scheduled' => 'bg-blue-100 text-blue-800',
            'completed' => 'bg-green-100 text-green-800',
            'cancelled' => 'bg-red-100 text-red-800',
            'no_show' => 'bg-gray-100 text-gray-800',
        ];

        return $colors[$this->status] ?? 'bg-gray-100 text-gray-800';
    }

    public function getPriorityColorAttribute(): string
    {
        $colors = [
            'low' => 'bg-green-100 text-green-800',
            'medium' => 'bg-yellow-100 text-yellow-800',
            'high' => 'bg-orange-100 text-orange-800',
            'urgent' => 'bg-red-100 text-red-800',
        ];

        return $colors[$this->priority] ?? 'bg-gray-100 text-gray-800';
    }

    public function getTypeColorAttribute(): string
    {
        $colors = [
            'showing' => 'bg-blue-100 text-blue-800',
            'inspection' => 'bg-purple-100 text-purple-800',
            'evaluation' => 'bg-indigo-100 text-indigo-800',
            'follow_up' => 'bg-yellow-100 text-yellow-800',
            'closing' => 'bg-green-100 text-green-800',
        ];

        return $colors[$this->type] ?? 'bg-gray-100 text-gray-800';
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->status === 'scheduled' && $this->scheduled_at < now();
    }

    public function getIsTodayAttribute(): bool
    {
        return $this->scheduled_at->isToday();
    }

    public function getIsUpcomingAttribute(): bool
    {
        return $this->status === 'scheduled' && $this->scheduled_at > now();
    }

    public function getIsProjectVisitAttribute(): bool
    {
        return !is_null($this->project_id);
    }

    public function getIsPropertyVisitAttribute(): bool
    {
        return !is_null($this->property_id);
    }

    public function getVisitSubjectAttribute(): string
    {
        if ($this->is_project_visit) {
            return $this->project ? $this->project->name : 'Proyecto no disponible';
        }
        
        if ($this->is_property_visit) {
            return $this->property ? $this->property->title : 'Propiedad no disponible';
        }
        
        return 'Sin asignación';
    }

    public function getVisitLocationAttribute(): string
    {
        if ($this->is_project_visit) {
            return $this->project ? $this->project->description : 'Descripción no disponible';
        }
        
        if ($this->is_property_visit) {
            return $this->property ? $this->property->address : 'Dirección no disponible';
        }
        
        return 'Ubicación no disponible';
    }

    // Scopes
    public function scopeScheduled(Builder $query): Builder
    {
        return $query->where('status', 'scheduled');
    }

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', 'completed');
    }

    public function scopeCancelled(Builder $query): Builder
    {
        return $query->where('status', 'cancelled');
    }

    public function scopeOverdue(Builder $query): Builder
    {
        return $query->where('status', 'scheduled')
                    ->where('scheduled_at', '<', now());
    }

    public function scopeToday(Builder $query): Builder
    {
        return $query->whereDate('scheduled_at', today());
    }

    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where('status', 'scheduled')
                    ->where('scheduled_at', '>', now());
    }

    public function scopeRequiresFollowUp(Builder $query): Builder
    {
        return $query->where('requires_follow_up', true);
    }

    public function scopeByAgent(Builder $query, int $agentId): Builder
    {
        return $query->where('agent_id', $agentId);
    }

    public function scopeByProperty(Builder $query, int $propertyId): Builder
    {
        return $query->where('property_id', $propertyId);
    }

    public function scopeByProject(Builder $query, int $projectId): Builder
    {
        return $query->where('project_id', $projectId);
    }

    public function scopeByClient(Builder $query, int $clientId): Builder
    {
        return $query->where('client_id', $clientId);
    }

    public function scopeByPriority(Builder $query, string $priority): Builder
    {
        return $query->where('priority', $priority);
    }

    public function scopeByType(Builder $query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    // Methods
    public function markAsCompleted(int $actualDuration = null): void
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
            'actual_duration' => $actualDuration,
        ]);
    }

    public function markAsCancelled(string $reason = null, int $cancelledBy = null): void
    {
        $this->update([
            'status' => 'cancelled',
            'cancelled_at' => now(),
            'cancellation_reason' => $reason,
            'cancelled_by' => $cancelledBy,
        ]);
    }

    public function markAsNoShow(): void
    {
        $this->update([
            'status' => 'no_show',
            'completed_at' => now(),
        ]);
    }

    public function scheduleFollowUp(Carbon $date, string $notes = null): void
    {
        $this->update([
            'requires_follow_up' => true,
            'follow_up_date' => $date,
            'follow_up_notes' => $notes,
        ]);
    }

    public function sendReminder(): bool
    {
        $this->update([
            'reminder_sent' => true,
            'reminder_sent_at' => now(),
        ]);

        return true;
    }
}
