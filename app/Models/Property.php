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
        'project_id',
        'status',
        'cover_image',
        'gallery',
        'videos',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'area' => 'decimal:2',
        'images' => 'array',
        'features' => 'array',
        'bedrooms' => 'integer',
        'bathrooms' => 'integer',
        'gallery' => 'array',
        'videos' => 'array',
    ];

    protected $appends = [
        'cover_image_url',
        'gallery_urls',
        'video_urls',
    ];

    public function agent(): BelongsTo
    {
        return $this->belongsTo(Agent::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function visits(): HasMany
    {
        return $this->hasMany(Visit::class);
    }

    // Accessors for media URLs
    public function getCoverImageUrlAttribute(): string
    {
        if ($this->cover_image) {
            $imagePath = storage_path('app/public/' . $this->cover_image);
            if (file_exists($imagePath)) {
                return asset('storage/' . $this->cover_image);
            }
        }
        
        // Placeholder images based on property category
        $placeholders = [
            'house' => 'https://images.unsplash.com/photo-1568605114967-8130f3a36994?w=800&h=600&fit=crop',
            'apartment' => 'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?w=800&h=600&fit=crop',
            'office' => 'https://images.unsplash.com/photo-1497366216548-37526070297c?w=800&h=600&fit=crop',
            'land' => 'https://images.unsplash.com/photo-1500382017468-9049fed747ef?w=800&h=600&fit=crop',
            'commercial' => 'https://images.unsplash.com/photo-1497366411874-c6010cf14c74?w=800&h=600&fit=crop'
        ];
        
        return $placeholders[$this->category ?? 'house'] ?? $placeholders['house'];
    }

    public function getGalleryUrlsAttribute(): array
    {
        if (!$this->gallery || !is_array($this->gallery)) {
            return [];
        }

        return array_filter(array_map(function ($image) {
            $imagePath = storage_path('app/public/' . $image);
            if (file_exists($imagePath)) {
                return asset('storage/' . $image);
            }
            return null;
        }, $this->gallery));
    }

    public function getVideoUrlsAttribute(): array
    {
        if (!$this->videos || !is_array($this->videos)) {
            return [];
        }

        return array_filter(array_map(function ($video) {
            $videoPath = storage_path('app/public/' . $video);
            if (file_exists($videoPath)) {
                return asset('storage/' . $video);
            }
            return null;
        }, $this->videos));
    }
}
