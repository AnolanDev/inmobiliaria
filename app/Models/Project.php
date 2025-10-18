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
        'video_urls',
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
                $filename = basename($this->cover_image['medium']);
                return url("api/images/projects/{$this->id}/{$filename}");
            }
            // Fallback to original if medium doesn't exist
            if (isset($this->cover_image['original'])) {
                $filename = basename($this->cover_image['original']);
                return url("api/images/projects/{$this->id}/{$filename}");
            }
        }
        
        // Handle legacy string format
        if ($this->cover_image && is_string($this->cover_image)) {
            $imagePath = storage_path('app/public/' . $this->cover_image);
            if (file_exists($imagePath)) {
                $filename = basename($this->cover_image);
                return url("api/images/projects/{$this->id}/{$filename}");
            }
        }
        
        // Fallback to unique placeholders based on project type and ID
        $placeholdersByType = [
            'Campestres' => [
                'https://images.unsplash.com/photo-1500382017468-9049fed747ef?w=800&h=600&fit=crop&crop=entropy&auto=format',
                'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=800&h=600&fit=crop&crop=entropy&auto=format',
                'https://images.unsplash.com/photo-1464822759844-d150baec843a?w=800&h=600&fit=crop&crop=entropy&auto=format',
                'https://images.unsplash.com/photo-1449824913935-59a10b8d2000?w=800&h=600&fit=crop&crop=entropy&auto=format'
            ],
            'Urbanos' => [
                'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?w=800&h=600&fit=crop&crop=entropy&auto=format',
                'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?w=800&h=600&fit=crop&crop=entropy&auto=format',
                'https://images.unsplash.com/photo-1449824913935-59a10b8d2000?w=800&h=600&fit=crop&crop=entropy&auto=format',
                'https://images.unsplash.com/photo-1516156008625-3a99593fa974?w=800&h=600&fit=crop&crop=entropy&auto=format'
            ],
            'Turísticos' => [
                'https://images.unsplash.com/photo-1571896349842-33c89424de2d?w=800&h=600&fit=crop&crop=entropy&auto=format',
                'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?w=800&h=600&fit=crop&crop=entropy&auto=format',
                'https://images.unsplash.com/photo-1564501049412-61c2a3083791?w=800&h=600&fit=crop&crop=entropy&auto=format',
                'https://images.unsplash.com/photo-1587061949409-02df41d5e562?w=800&h=600&fit=crop&crop=entropy&auto=format'
            ]
        ];
        
        $type = $this->type ?? 'Urbanos';
        $placeholders = $placeholdersByType[$type] ?? $placeholdersByType['Urbanos'];
        
        // Use project ID to select a consistent but different placeholder
        $index = ($this->id - 1) % count($placeholders);
        return $placeholders[$index];
    }

    public function getCoverImageResponsiveAttribute(): array
    {
        if ($this->cover_image && is_array($this->cover_image)) {
            return app(\App\Services\ImageOptimizationService::class)->generateResponsiveUrls($this->cover_image, 'projects', $this->id);
        }
        
        // Handle legacy string format - create responsive URLs from existing image
        if ($this->cover_image && is_string($this->cover_image)) {
            $filename = basename($this->cover_image);
            $baseUrl = url("api/images/projects/{$this->id}/{$filename}");
            return [
                'thumbnail' => ['url' => $baseUrl, 'width' => 400],
                'medium' => ['url' => $baseUrl, 'width' => 800],
                'large' => ['url' => $baseUrl, 'width' => 1200],
                'original' => ['url' => $baseUrl, 'width' => null]
            ];
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
                $urls[] = app(\App\Services\ImageOptimizationService::class)->generateResponsiveUrls($imageSet, 'projects', $this->id);
            } else {
                // Legacy format - create responsive URLs from existing image
                $imagePath = storage_path('app/public/' . $imageSet);
                if (file_exists($imagePath)) {
                    $baseUrl = asset('storage/' . $imageSet);
                    $urls[] = [
                        'thumbnail' => ['url' => $baseUrl, 'width' => 400],
                        'medium' => ['url' => $baseUrl, 'width' => 800],
                        'large' => ['url' => $baseUrl, 'width' => 1200],
                        'original' => ['url' => $baseUrl, 'width' => null]
                    ];
                }
            }
        }
        
        return $urls;
    }

    public function getVideoUrlsAttribute(): array
    {
        if (!$this->videos || !is_array($this->videos)) {
            return [];
        }

        $urls = [];
        foreach ($this->videos as $videoPath) {
            if (is_string($videoPath) && !empty($videoPath)) {
                // Check if video file exists
                $fullPath = storage_path('app/public/' . $videoPath);
                if (file_exists($fullPath)) {
                    $urls[] = [
                        'url' => asset('storage/' . $videoPath),
                        'path' => $videoPath,
                        'filename' => basename($videoPath),
                        'type' => $this->getVideoType($videoPath)
                    ];
                }
            }
        }
        
        return $urls;
    }

    private function getVideoType(string $path): string
    {
        $extension = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        return match ($extension) {
            'mp4' => 'video/mp4',
            'webm' => 'video/webm',
            'ogg' => 'video/ogg',
            'avi' => 'video/avi',
            'mov' => 'video/quicktime',
            'wmv' => 'video/x-ms-wmv',
            default => 'video/mp4'
        };
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
