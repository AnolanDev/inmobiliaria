<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Str;

class EmailSend extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'email_campaign_id',
        'recipient_type',
        'recipient_id',
        'recipient_email',
        'recipient_name',
        'subject',
        'html_content',
        'text_content',
        'status',
        'error_message',
        'sent_at',
        'failed_at',
        'opened',
        'first_opened_at',
        'last_opened_at',
        'open_count',
        'clicked',
        'first_clicked_at',
        'last_clicked_at',
        'click_count',
        'unsubscribed',
        'unsubscribed_at',
        'tracking_token',
        'unsubscribe_token',
        'ab_variant',
        'activity_id'
    ];

    protected $casts = [
        'opened' => 'boolean',
        'clicked' => 'boolean',
        'unsubscribed' => 'boolean',
        'sent_at' => 'datetime',
        'failed_at' => 'datetime',
        'first_opened_at' => 'datetime',
        'last_opened_at' => 'datetime',
        'first_clicked_at' => 'datetime',
        'last_clicked_at' => 'datetime',
        'unsubscribed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    // Constants
    const STATUSES = [
        'queued' => 'En cola',
        'sending' => 'Enviando',
        'sent' => 'Enviado',
        'failed' => 'Fallido',
        'bounced' => 'Rebotado'
    ];

    // Boot method to generate tokens
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($emailSend) {
            if (empty($emailSend->tracking_token)) {
                $emailSend->tracking_token = Str::random(32);
            }
            if (empty($emailSend->unsubscribe_token)) {
                $emailSend->unsubscribe_token = Str::random(32);
            }
        });
    }

    // Relationships
    public function emailCampaign(): BelongsTo
    {
        return $this->belongsTo(EmailCampaign::class);
    }

    public function recipient(): MorphTo
    {
        return $this->morphTo();
    }

    public function activity(): BelongsTo
    {
        return $this->belongsTo(Activity::class);
    }

    public function emailClicks(): HasMany
    {
        return $this->hasMany(EmailClick::class);
    }

    // Accessors
    public function getFormattedStatusAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function getTrackingUrlAttribute(): string
    {
        return url("/email/track/{$this->tracking_token}");
    }

    public function getUnsubscribeUrlAttribute(): string
    {
        return url("/unsubscribe/{$this->unsubscribe_token}");
    }

    public function getIsDeliveredAttribute(): bool
    {
        return in_array($this->status, ['sent', 'opened']);
    }

    public function getIsFailedAttribute(): bool
    {
        return in_array($this->status, ['failed', 'bounced']);
    }

    public function getEngagementScoreAttribute(): int
    {
        $score = 0;
        
        if ($this->opened) {
            $score += 10;
            $score += min($this->open_count * 2, 20); // Max 20 points for opens
        }
        
        if ($this->clicked) {
            $score += 25;
            $score += min($this->click_count * 5, 25); // Max 25 points for clicks
        }
        
        return min($score, 100); // Max score of 100
    }

    // Scopes
    public function scopeSent($query)
    {
        return $query->where('status', 'sent');
    }

    public function scopeOpened($query)
    {
        return $query->where('opened', true);
    }

    public function scopeClicked($query)
    {
        return $query->where('clicked', true);
    }

    public function scopeFailed($query)
    {
        return $query->whereIn('status', ['failed', 'bounced']);
    }

    public function scopeUnsubscribed($query)
    {
        return $query->where('unsubscribed', true);
    }

    public function scopeByRecipientType($query, string $type)
    {
        return $query->where('recipient_type', $type);
    }

    public function scopeByAbVariant($query, string $variant)
    {
        return $query->where('ab_variant', $variant);
    }

    // Methods
    public function markAsSent(): bool
    {
        if ($this->status !== 'queued') {
            return false;
        }

        return $this->update([
            'status' => 'sent',
            'sent_at' => now()
        ]);
    }

    public function markAsFailed(string $errorMessage): bool
    {
        return $this->update([
            'status' => 'failed',
            'error_message' => $errorMessage,
            'failed_at' => now()
        ]);
    }

    public function markAsBounced(string $reason = null): bool
    {
        return $this->update([
            'status' => 'bounced',
            'error_message' => $reason,
            'failed_at' => now()
        ]);
    }

    public function trackOpen(): bool
    {
        $now = now();
        $wasFirstOpen = !$this->opened;
        
        $this->update([
            'opened' => true,
            'first_opened_at' => $wasFirstOpen ? $now : $this->first_opened_at,
            'last_opened_at' => $now,
            'open_count' => $this->open_count + 1
        ]);

        // Update campaign metrics if this is first open
        if ($wasFirstOpen) {
            $this->emailCampaign->increment('emails_opened');
            $this->emailCampaign->updateMetrics();
        }

        return true;
    }

    public function trackClick(string $url, array $metadata = []): EmailClick
    {
        $now = now();
        $wasFirstClick = !$this->clicked;

        // Update send record
        $this->update([
            'clicked' => true,
            'first_clicked_at' => $wasFirstClick ? $now : $this->first_clicked_at,
            'last_clicked_at' => $now,
            'click_count' => $this->click_count + 1
        ]);

        // Create click record
        $click = $this->emailClicks()->create([
            'link_url' => $url,
            'link_text' => $metadata['link_text'] ?? null,
            'link_position' => $metadata['link_position'] ?? null,
            'clicked_at' => $now,
            'user_agent' => $metadata['user_agent'] ?? null,
            'ip_address' => $metadata['ip_address'] ?? null,
            'referrer' => $metadata['referrer'] ?? null
        ]);

        // Update campaign metrics if this is first click
        if ($wasFirstClick) {
            $this->emailCampaign->increment('emails_clicked');
            $this->emailCampaign->updateMetrics();
        }

        return $click;
    }

    public function unsubscribe(): bool
    {
        if ($this->unsubscribed) {
            return false;
        }

        $this->update([
            'unsubscribed' => true,
            'unsubscribed_at' => now()
        ]);

        // Update campaign metrics
        $this->emailCampaign->increment('emails_unsubscribed');
        $this->emailCampaign->updateMetrics();

        // Unsubscribe the recipient from future emails
        if ($this->recipient) {
            $this->recipient->update(['unsubscribed' => true]);
        }

        return true;
    }

    public function createActivity(): ?Activity
    {
        if ($this->activity_id || !$this->recipient) {
            return null;
        }

        $activity = Activity::create([
            'type' => 'email',
            'subject' => "Email enviado: {$this->subject}",
            'description' => "Email enviado a {$this->recipient_email}",
            'status' => 'completed',
            'priority' => 'medium',
            'related_type' => $this->recipient_type,
            'related_id' => $this->recipient_id,
            'completed_at' => $this->sent_at,
            'user_id' => $this->emailCampaign->created_by,
            'metadata' => [
                'email_send_id' => $this->id,
                'email_campaign_id' => $this->email_campaign_id,
                'email_subject' => $this->subject,
                'tracking_token' => $this->tracking_token
            ]
        ]);

        $this->update(['activity_id' => $activity->id]);

        return $activity;
    }

    public function getTrackingPixelHtml(): string
    {
        return '<img src="' . $this->tracking_url . '" alt="" width="1" height="1" style="display:block;width:1px;height:1px;" />';
    }

    public function processLinksForTracking(): string
    {
        $html = $this->html_content;
        $trackingBaseUrl = url('/email/click/' . $this->tracking_token);
        
        // Replace all links with tracking links
        $html = preg_replace_callback(
            '/<a\s+[^>]*href=["\']([^"\']*)["\'][^>]*>(.*?)<\/a>/i',
            function ($matches) use ($trackingBaseUrl) {
                $originalUrl = $matches[1];
                $linkText = $matches[2];
                $trackingUrl = $trackingBaseUrl . '?url=' . urlencode($originalUrl);
                
                return str_replace('href="' . $originalUrl . '"', 'href="' . $trackingUrl . '"', $matches[0]);
            },
            $html
        );
        
        // Add tracking pixel at the end
        $html .= $this->getTrackingPixelHtml();
        
        return $html;
    }

    public function getPersonalizedContent(): array
    {
        if (!$this->recipient) {
            return [
                'subject' => $this->subject,
                'html_content' => $this->html_content,
                'text_content' => $this->text_content
            ];
        }

        // Get variables for personalization
        $variables = $this->getPersonalizationVariables();
        
        // Process content
        $processedHtml = $this->processLinksForTracking();
        
        return [
            'subject' => $this->replaceVariables($this->subject, $variables),
            'html_content' => $this->replaceVariables($processedHtml, $variables),
            'text_content' => $this->text_content ? $this->replaceVariables($this->text_content, $variables) : null
        ];
    }

    private function getPersonalizationVariables(): array
    {
        $variables = [
            'recipient_name' => $this->recipient_name ?: $this->recipient_email,
            'recipient_email' => $this->recipient_email,
            'tracking_token' => $this->tracking_token,
            'unsubscribe_url' => $this->unsubscribe_url,
            'current_date' => now()->format('d/m/Y'),
            'company_name' => config('app.name', 'InmoApp')
        ];

        // Add recipient-specific variables
        if ($this->recipient instanceof Lead) {
            $variables = array_merge($variables, [
                'lead_first_name' => $this->recipient->first_name,
                'lead_last_name' => $this->recipient->last_name,
                'lead_full_name' => $this->recipient->full_name,
                'lead_status' => $this->recipient->formatted_status,
                'lead_source' => $this->recipient->formatted_source,
                'lead_budget_min' => $this->recipient->budget_min ? '$' . number_format($this->recipient->budget_min) : '',
                'lead_budget_max' => $this->recipient->budget_max ? '$' . number_format($this->recipient->budget_max) : '',
                'lead_interests' => implode(', ', $this->recipient->interests ?? []),
                'assigned_agent_name' => $this->recipient->assignedAgent?->name ?? ''
            ]);
        }

        return $variables;
    }

    private function replaceVariables(string $content, array $variables): string
    {
        foreach ($variables as $key => $value) {
            $content = str_replace('{{' . $key . '}}', $value ?? '', $content);
        }
        
        return $content;
    }
}