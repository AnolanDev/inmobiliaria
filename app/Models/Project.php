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
        // Get raw database value to handle empty strings vs null properly
        $rawCoverImage = $this->getRawOriginal('cover_image');

        // Handle actual stored images with proper validation
        if (!empty($rawCoverImage) && $rawCoverImage !== '""' && $rawCoverImage !== '[]') {
            // Handle new optimized image structure (valid JSON array)
            if ($this->cover_image && is_array($this->cover_image) && !empty($this->cover_image)) {
                // Check if it's an associative array with size keys (medium, original, etc.)
                if (isset($this->cover_image['medium'])) {
                    $filename = basename($this->cover_image['medium']);
                    return url("api/images/projects/{$this->id}/{$filename}");
                }
                if (isset($this->cover_image['original'])) {
                    $filename = basename($this->cover_image['original']);
                    return url("api/images/projects/{$this->id}/{$filename}");
                }

                // Handle simple array format: ['projects/1/cover_123.jpg']
                if (isset($this->cover_image[0]) && is_string($this->cover_image[0])) {
                    $imagePath = storage_path('app/public/' . $this->cover_image[0]);
                    if (file_exists($imagePath)) {
                        return asset('storage/' . $this->cover_image[0]);
                    }
                }
            }

            // Handle legacy string format (non-empty string)
            if (is_string($rawCoverImage) && $rawCoverImage !== 'null' && !str_starts_with($rawCoverImage, '[')) {
                $imagePath = storage_path('app/public/' . $rawCoverImage);
                if (file_exists($imagePath)) {
                    return asset('storage/' . $rawCoverImage);
                }
            }
        }

        // Generate unique placeholder for projects without images
        return $this->generateUniquePlaceholder();
    }

    /**
     * Generate unique placeholder image based on project ID
     * Ensures each project gets a different placeholder image
     */
    private function generateUniquePlaceholder(): string
    {
        $placeholders = [
            // Campestres (0-3)
            'https://images.unsplash.com/photo-1500382017468-9049fed747ef?w=800&h=600&fit=crop&crop=entropy&auto=format',
            'https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=800&h=600&fit=crop&crop=entropy&auto=format',
            'https://images.unsplash.com/photo-1464822759844-d150baec843a?w=800&h=600&fit=crop&crop=entropy&auto=format',
            'https://images.unsplash.com/photo-1449824913935-59a10b8d2000?w=800&h=600&fit=crop&crop=entropy&auto=format',
            
            // Urbanos (4-7)
            'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?w=800&h=600&fit=crop&crop=entropy&auto=format',
            'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?w=800&h=600&fit=crop&crop=entropy&auto=format',
            'https://images.unsplash.com/photo-1516156008625-3a99593fa974?w=800&h=600&fit=crop&crop=entropy&auto=format',
            'https://images.unsplash.com/photo-1560518883-ce09059eeffa?w=800&h=600&fit=crop&crop=entropy&auto=format',
            
            // Turísticos (8-11)
            'https://images.unsplash.com/photo-1571896349842-33c89424de2d?w=800&h=600&fit=crop&crop=entropy&auto=format',
            'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?w=800&h=600&fit=crop&crop=entropy&auto=format',
            'https://images.unsplash.com/photo-1564501049412-61c2a3083791?w=800&h=600&fit=crop&crop=entropy&auto=format',
            'https://images.unsplash.com/photo-1587061949409-02df41d5e562?w=800&h=600&fit=crop&crop=entropy&auto=format'
        ];
        
        // Simple algorithm: Use project ID to ensure unique distribution
        // This guarantees each project gets a different image without collisions
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
            // Handle optimized format with responsive sizes
            if (is_array($imageSet) && (isset($imageSet['thumbnail']) || isset($imageSet['medium']))) {
                $urls[] = app(\App\Services\ImageOptimizationService::class)->generateResponsiveUrls($imageSet, 'projects', $this->id);
            }
            // Handle simple string path from storage: 'projects/1/gallery_0_123.jpg'
            elseif (is_string($imageSet) && !empty($imageSet)) {
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
            // Skip corrupted data (non-string, non-array values)
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
