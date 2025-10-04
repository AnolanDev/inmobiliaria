<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'description',
        'type',
        'status',
        'property_count',
        'is_public',
        'sort_order',
        'cover_image',
        'gallery',
        'videos',
        'city',
        'state',
    ];

    protected $casts = [
        'cover_image' => 'array',
        'gallery' => 'array',
        'videos' => 'array',
        'property_count' => 'integer',
        'is_public' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected $appends = [
        'cover_image_url',
        'cover_image_responsive',
        'gallery_urls',
        'type_color',
        'status_color',
    ];

    const TYPES = [
        'Campestres' => 'Campestres',
        'Urbanos' => 'Urbanos',
        'Turísticos' => 'Turísticos',
    ];

    const STATUSES = [
        'Vendido' => 'Vendido',
        'Disponible' => 'Disponible',
        'Reservado' => 'Reservado',
    ];

    public function properties(): HasMany
    {
        return $this->hasMany(Property::class);
    }

    public function getTypeColorAttribute(): string
    {
        return match ($this->type) {
            'Campestres' => 'bg-green-100 text-green-800',
            'Urbanos' => 'bg-blue-100 text-blue-800',
            'Turísticos' => 'bg-purple-100 text-purple-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'Vendido' => 'bg-red-100 text-red-800',
            'Disponible' => 'bg-green-100 text-green-800',
            'Reservado' => 'bg-yellow-100 text-yellow-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    public function getCoverImageUrlAttribute(): string
    {
        // Handle new optimized image structure
        if ($this->cover_image && is_array($this->cover_image)) {
            // Return medium size for backward compatibility
            if (isset($this->cover_image['medium'])) {
                return asset('storage/' . $this->cover_image['medium']);
            }
            // Fallback to original if medium doesn't exist
            if (isset($this->cover_image['original'])) {
                return asset('storage/' . $this->cover_image['original']);
            }
        }
        
        // Handle legacy string format
        if ($this->cover_image && is_string($this->cover_image)) {
            $imagePath = storage_path('app/public/' . $this->cover_image);
            if (file_exists($imagePath)) {
                return asset('storage/' . $this->cover_image);
            }
        }
        
        // Fallback to placeholders
        return app(\App\Services\ImageOptimizationService::class)->getFallbackUrl('project', 800);
    }

    public function getCoverImageResponsiveAttribute(): array
    {
        if ($this->cover_image && is_array($this->cover_image)) {
            return app(\App\Services\ImageOptimizationService::class)->generateResponsiveUrls($this->cover_image);
        }
        
        // Return fallback responsive images
        return app(\App\Services\ImageOptimizationService::class)->getFallbackUrls('project');
    }

    public function getGalleryUrlsAttribute(): array
    {
        if (!$this->gallery || !is_array($this->gallery)) {
            return [];
        }

        $urls = [];
        foreach ($this->gallery as $imageSet) {
            if (is_array($imageSet)) {
                // New optimized format
                $urls[] = app(\App\Services\ImageOptimizationService::class)->generateResponsiveUrls($imageSet);
            } else {
                // Legacy format
                $imagePath = storage_path('app/public/' . $imageSet);
                if (file_exists($imagePath)) {
                    $urls[] = [
                        'medium' => ['url' => asset('storage/' . $imageSet), 'width' => 800]
                    ];
                }
            }
        }
        
        return $urls;
    }

    // Scopes for ordering and filtering
    public function scopeOrderedForPublic($query)
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('created_at', 'desc');
    }

    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    // Location filtering scopes
    public function scopeByLocation($query, $location)
    {
        if (!$location) {
            return $query;
        }

        return $query->where(function ($q) use ($location) {
            $q->where('city', 'like', '%' . $location . '%')
              ->orWhere('state', 'like', '%' . $location . '%');
        });
    }

    public function scopeByState($query, $state)
    {
        if (!$state) {
            return $query;
        }

        return $query->where('state', $state);
    }

    public function scopeByCity($query, $city)
    {
        if (!$city) {
            return $query;
        }

        return $query->where('city', $city);
    }
}
