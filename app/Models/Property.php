<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Property extends Model
{
    protected $fillable = [
        'title',
        'description',
        'price',
        'type',
        'category',
        'address',
        'city',
        'state',
        'zip_code',
        'bedrooms',
        'bathrooms',
        'area',
        'images',
        'features',
        'agent_id',
        'status',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'area' => 'decimal:2',
        'images' => 'array',
        'features' => 'array',
        'bedrooms' => 'integer',
        'bathrooms' => 'integer',
    ];

    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    public function visits(): HasMany
    {
        return $this->hasMany(Visit::class);
    }
}
