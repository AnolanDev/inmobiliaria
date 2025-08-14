<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Campaign extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'type',
        'status',
        'description',
        'target_audience',
        'budget',
        'spent',
        'start_date',
        'end_date',
        'content',
        'settings',
        'impressions',
        'clicks',
        'conversions',
        'conversion_rate',
        'created_by',
    ];

    protected $casts = [
        'target_audience' => 'array',
        'content' => 'array',
        'settings' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
        'budget' => 'decimal:2',
        'spent' => 'decimal:2',
        'conversion_rate' => 'decimal:2',
    ];

    const TYPES = [
        'email' => 'Email Marketing',
        'sms' => 'SMS Marketing',
        'social' => 'Redes Sociales',
        'digital_ads' => 'Publicidad Digital',
        'print' => 'Material Impreso',
        'event' => 'Eventos'
    ];

    const STATUSES = [
        'draft' => 'Borrador',
        'active' => 'Activa',
        'paused' => 'Pausada',
        'completed' => 'Completada'
    ];

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }

    public function getFormattedTypeAttribute(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }

    public function getFormattedStatusAttribute(): string
    {
        return self::STATUSES[$this->status] ?? $this->status;
    }

    public function isActive(): bool
    {
        return $this->status === 'active' && 
               ($this->start_date === null || $this->start_date <= now()) &&
               ($this->end_date === null || $this->end_date >= now());
    }

    public function calculateConversionRate(): void
    {
        if ($this->clicks > 0) {
            $this->conversion_rate = ($this->conversions / $this->clicks) * 100;
            $this->save();
        }
    }

    public function getRemainingBudget(): float
    {
        return $this->budget ? $this->budget - $this->spent : 0;
    }
}
