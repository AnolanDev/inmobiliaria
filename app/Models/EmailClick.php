<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailClick extends Model
{
    use HasFactory;

    protected $fillable = [
        'email_send_id',
        'link_url',
        'link_text',
        'link_position',
        'clicked_at',
        'user_agent',
        'ip_address',
        'referrer'
    ];

    protected $casts = [
        'clicked_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relationships
    public function emailSend(): BelongsTo
    {
        return $this->belongsTo(EmailSend::class);
    }

    // Accessors
    public function getDomainAttribute(): string
    {
        return parse_url($this->link_url, PHP_URL_HOST) ?? '';
    }

    public function getIsExternalLinkAttribute(): bool
    {
        $domain = $this->domain;
        $appDomain = parse_url(config('app.url'), PHP_URL_HOST);
        
        return $domain !== $appDomain;
    }

    public function getBrowserAttribute(): ?string
    {
        if (!$this->user_agent) {
            return null;
        }
        
        // Simple browser detection
        $browsers = [
            '/chrome/i' => 'Chrome',
            '/firefox/i' => 'Firefox',
            '/safari/i' => 'Safari',
            '/edge/i' => 'Edge',
            '/opera/i' => 'Opera',
            '/msie/i' => 'Internet Explorer'
        ];
        
        foreach ($browsers as $regex => $browser) {
            if (preg_match($regex, $this->user_agent)) {
                return $browser;
            }
        }
        
        return 'Unknown';
    }

    // Scopes
    public function scopeExternalLinks($query)
    {
        return $query->whereNotNull('link_url')
                    ->where('link_url', 'not like', '%' . parse_url(config('app.url'), PHP_URL_HOST) . '%');
    }

    public function scopeInternalLinks($query)
    {
        return $query->whereNotNull('link_url')
                    ->where('link_url', 'like', '%' . parse_url(config('app.url'), PHP_URL_HOST) . '%');
    }

    public function scopeByDomain($query, string $domain)
    {
        return $query->where('link_url', 'like', '%' . $domain . '%');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('clicked_at', today());
    }

    public function scopeThisWeek($query)
    {
        return $query->whereBetween('clicked_at', [
            now()->startOfWeek(),
            now()->endOfWeek()
        ]);
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('clicked_at', now()->month)
                    ->whereYear('clicked_at', now()->year);
    }
}