<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class EmailCampaign extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'type',
        'status',
        'email_template_id',
        'subject_override',
        'segment_conditions',
        'recipient_filters',
        'estimated_recipients',
        'actual_recipients',
        'scheduled_at',
        'started_at',
        'completed_at',
        'is_ab_test',
        'ab_test_config',
        'winning_variant_id',
        'is_drip_campaign',
        'drip_schedule',
        'drip_sequence_order',
        'parent_drip_campaign_id',
        'emails_sent',
        'emails_delivered',
        'emails_opened',
        'emails_clicked',
        'emails_bounced',
        'emails_unsubscribed',
        'open_rate',
        'click_rate',
        'bounce_rate',
        'marketing_campaign_id',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'segment_conditions' => 'array',
        'recipient_filters' => 'array',
        'ab_test_config' => 'array',
        'drip_schedule' => 'array',
        'is_ab_test' => 'boolean',
        'is_drip_campaign' => 'boolean',
        'scheduled_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'open_rate' => 'decimal:2',
        'click_rate' => 'decimal:2',
        'bounce_rate' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    // Constants
    const TYPES = [
        'single' => 'Envío único',
        'drip' => 'Campaña goteo',
        'newsletter' => 'Newsletter',
        'automated' => 'Automatizada'
    ];

    const STATUSES = [
        'draft' => 'Borrador',
        'scheduled' => 'Programada',
        'sending' => 'Enviando',
        'sent' => 'Enviada',
        'paused' => 'Pausada',
        'cancelled' => 'Cancelada'
    ];

    // Relationships
    public function emailTemplate(): BelongsTo
    {
        return $this->belongsTo(EmailTemplate::class);
    }

    public function marketingCampaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class, 'marketing_campaign_id');
    }

    public function parentDripCampaign(): BelongsTo
    {
        return $this->belongsTo(EmailCampaign::class, 'parent_drip_campaign_id');
    }

    public function dripCampaigns(): HasMany
    {
        return $this->hasMany(EmailCampaign::class, 'parent_drip_campaign_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function emailSends(): HasMany
    {
        return $this->hasMany(EmailSend::class);
    }

    // Accessors
    public function getFormattedTypeAttribute(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }

    public function getFormattedStatusAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function getEffectiveSubjectAttribute(): string
    {
        return $this->subject_override ?: $this->emailTemplate?->subject ?? '';
    }

    public function getIsScheduledAttribute(): bool
    {
        return $this->scheduled_at && $this->scheduled_at->isFuture();
    }

    public function getIsPastDueAttribute(): bool
    {
        return $this->scheduled_at && $this->scheduled_at->isPast() && $this->status === 'scheduled';
    }

    public function getCanBeSentAttribute(): bool
    {
        return in_array($this->status, ['draft', 'scheduled']) && 
               $this->emailTemplate && 
               $this->estimated_recipients > 0;
    }

    public function getCanBePausedAttribute(): bool
    {
        return $this->status === 'sending';
    }

    public function getCanBeResumedAttribute(): bool
    {
        return $this->status === 'paused';
    }

    public function getCanBeCancelledAttribute(): bool
    {
        return in_array($this->status, ['draft', 'scheduled', 'sending', 'paused']);
    }

    // Scopes
    public function scopeActive($query): Builder
    {
        return $query->whereNotIn('status', ['cancelled']);
    }

    public function scopeByType($query, string $type): Builder
    {
        return $query->where('type', $type);
    }

    public function scopeByStatus($query, string $status): Builder
    {
        return $query->where('status', $status);
    }

    public function scopeDue($query): Builder
    {
        return $query->where('status', 'scheduled')
                    ->where('scheduled_at', '<=', now());
    }

    public function scopeDripSequences($query): Builder
    {
        return $query->where('is_drip_campaign', true)
                    ->whereNotNull('parent_drip_campaign_id');
    }

    public function scopeParentDrips($query): Builder
    {
        return $query->where('is_drip_campaign', true)
                    ->whereNull('parent_drip_campaign_id');
    }

    // Methods
    public function calculateRecipients(): int
    {
        $query = $this->buildRecipientsQuery();
        $count = $query->count();
        
        $this->update(['estimated_recipients' => $count]);
        
        return $count;
    }

    public function buildRecipientsQuery(): Builder
    {
        $query = Lead::query();
        
        // Apply segment conditions
        if ($this->segment_conditions) {
            $this->applySegmentConditions($query, $this->segment_conditions);
        }
        
        // Apply recipient filters
        if ($this->recipient_filters) {
            $this->applyRecipientFilters($query, $this->recipient_filters);
        }
        
        return $query;
    }

    private function applySegmentConditions(Builder $query, array $conditions): void
    {
        foreach ($conditions as $condition) {
            $field = $condition['field'];
            $operator = $condition['operator'];
            $value = $condition['value'];
            
            switch ($operator) {
                case 'equals':
                    $query->where($field, $value);
                    break;
                case 'not_equals':
                    $query->where($field, '!=', $value);
                    break;
                case 'contains':
                    $query->where($field, 'like', "%{$value}%");
                    break;
                case 'in':
                    $query->whereIn($field, $value);
                    break;
                case 'not_in':
                    $query->whereNotIn($field, $value);
                    break;
                case 'greater_than':
                    $query->where($field, '>', $value);
                    break;
                case 'less_than':
                    $query->where($field, '<', $value);
                    break;
            }
        }
    }

    private function applyRecipientFilters(Builder $query, array $filters): void
    {
        // Exclude unsubscribed leads
        if ($filters['exclude_unsubscribed'] ?? true) {
            $query->where('unsubscribed', false);
        }
        
        // Only active leads
        if ($filters['only_active'] ?? true) {
            $query->whereNotIn('status', ['lost', 'converted']);
        }
        
        // Date range filters
        if ($filters['created_after'] ?? null) {
            $query->where('created_at', '>=', Carbon::parse($filters['created_after']));
        }
        
        if ($filters['created_before'] ?? null) {
            $query->where('created_at', '<=', Carbon::parse($filters['created_before']));
        }
    }

    public function schedule(Carbon $scheduledAt): bool
    {
        if ($this->status !== 'draft') {
            return false;
        }
        
        $this->update([
            'status' => 'scheduled',
            'scheduled_at' => $scheduledAt
        ]);
        
        return true;
    }

    public function start(): bool
    {
        if (!in_array($this->status, ['scheduled', 'paused'])) {
            return false;
        }
        
        $this->update([
            'status' => 'sending',
            'started_at' => now()
        ]);
        
        return true;
    }

    public function pause(): bool
    {
        if ($this->status !== 'sending') {
            return false;
        }
        
        $this->update(['status' => 'paused']);
        
        return true;
    }

    public function resume(): bool
    {
        if ($this->status !== 'paused') {
            return false;
        }
        
        $this->update(['status' => 'sending']);
        
        return true;
    }

    public function cancel(): bool
    {
        if (!$this->can_be_cancelled) {
            return false;
        }
        
        $this->update(['status' => 'cancelled']);
        
        return true;
    }

    public function complete(): bool
    {
        if ($this->status !== 'sending') {
            return false;
        }
        
        $this->update([
            'status' => 'sent',
            'completed_at' => now()
        ]);
        
        $this->updateMetrics();
        
        return true;
    }

    public function updateMetrics(): void
    {
        $sends = $this->emailSends();
        
        $this->update([
            'actual_recipients' => $sends->count(),
            'emails_sent' => $sends->where('status', 'sent')->count(),
            'emails_delivered' => $sends->whereIn('status', ['sent', 'opened'])->count(),
            'emails_opened' => $sends->where('opened', true)->count(),
            'emails_clicked' => $sends->where('clicked', true)->count(),
            'emails_bounced' => $sends->where('status', 'bounced')->count(),
            'emails_unsubscribed' => $sends->where('unsubscribed', true)->count()
        ]);
        
        // Calculate rates
        $delivered = $this->emails_delivered;
        if ($delivered > 0) {
            $this->update([
                'open_rate' => round(($this->emails_opened / $delivered) * 100, 2),
                'click_rate' => round(($this->emails_clicked / $delivered) * 100, 2)
            ]);
        }
        
        $sent = $this->emails_sent;
        if ($sent > 0) {
            $this->update([
                'bounce_rate' => round(($this->emails_bounced / $sent) * 100, 2)
            ]);
        }
    }

    public function duplicate(string $newName): static
    {
        $duplicate = $this->replicate();
        $duplicate->name = $newName;
        $duplicate->status = 'draft';
        $duplicate->scheduled_at = null;
        $duplicate->started_at = null;
        $duplicate->completed_at = null;
        $duplicate->actual_recipients = 0;
        $duplicate->emails_sent = 0;
        $duplicate->emails_delivered = 0;
        $duplicate->emails_opened = 0;
        $duplicate->emails_clicked = 0;
        $duplicate->emails_bounced = 0;
        $duplicate->emails_unsubscribed = 0;
        $duplicate->open_rate = 0;
        $duplicate->click_rate = 0;
        $duplicate->bounce_rate = 0;
        $duplicate->created_by = auth()->id();
        $duplicate->updated_by = null;
        $duplicate->save();

        return $duplicate;
    }
}