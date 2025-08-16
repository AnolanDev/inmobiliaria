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
    ];

    protected $casts = [
        'gallery' => 'array',
        'videos' => 'array',
        'property_count' => 'integer',
        'is_public' => 'boolean',
        'sort_order' => 'integer',
    ];

    protected $appends = [
        'cover_image_url',
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
        // Si hay imagen de portada, verificar si existe
        if ($this->cover_image) {
            $imagePath = storage_path('app/public/' . $this->cover_image);
            if (file_exists($imagePath)) {
                return asset('storage/' . $this->cover_image);
            }
        }
        
        // Imágenes placeholder según el tipo de proyecto
        $placeholders = [
            'Campestres' => 'https://images.unsplash.com/photo-1500382017468-9049fed747ef?w=800&h=600&fit=crop',
            'Urbanos' => 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?w=800&h=600&fit=crop',
            'Turísticos' => 'https://images.unsplash.com/photo-1571896349842-33c89424de2d?w=800&h=600&fit=crop'
        ];
        
        return $placeholders[$this->type ?? 'Urbanos'] ?? $placeholders['Urbanos'];
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
}
