<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Agent extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'type',
        'bio',
        'profile_picture',
        'facebook',
        'instagram',
        'linkedin',
        'gallery',
        'videos',
        'is_active',
        'is_public',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_public' => 'boolean',
        'gallery' => 'array',
        'videos' => 'array',
    ];

    protected $appends = [
        'profile_picture_url',
        'gallery_urls',
        'video_urls',
    ];

    public function properties(): HasMany
    {
        return $this->hasMany(Property::class);
    }

    public function visits(): HasMany
    {
        return $this->hasMany(Visit::class);
    }

    // Accessor for profile picture URL
    protected function profilePictureUrl(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if ($this->profile_picture) {
                    $imagePath = storage_path('app/public/' . $this->profile_picture);
                    if (file_exists($imagePath)) {
                        return asset('storage/' . $this->profile_picture);
                    }
                }
                // Generate avatar placeholder based on name and type
                $name = urlencode($this->name);
                $backgroundColor = $this->type === 'Externo' ? 'e3f2fd' : 'f3e5f5';
                $textColor = $this->type === 'Externo' ? '1976d2' : '7b1fa2';
                
                return "https://ui-avatars.com/api/?name={$name}&background={$backgroundColor}&color={$textColor}&size=400&font-size=0.5";
            }
        );
    }

    // Accessor for gallery URLs
    protected function galleryUrls(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (!$this->gallery || !is_array($this->gallery)) {
                    return [];
                }

                return array_map(function ($path) {
                    if ($path && file_exists(storage_path('app/public/' . $path))) {
                        return asset('storage/' . $path);
                    }
                    return null;
                }, $this->gallery);
            }
        );
    }

    // Accessor for video URLs
    protected function videoUrls(): Attribute
    {
        return Attribute::make(
            get: function ($value) {
                if (!$this->videos || !is_array($this->videos)) {
                    return [];
                }

                return array_map(function ($path) {
                    if ($path && file_exists(storage_path('app/public/' . $path))) {
                        return asset('storage/' . $path);
                    }
                    return null;
                }, $this->videos);
            }
        );
    }

    // Helper method to get social media links
    public function getSocialMediaLinks(): array
    {
        $links = [];

        if ($this->facebook) {
            $links['facebook'] = $this->facebook;
        }

        if ($this->instagram) {
            $links['instagram'] = $this->instagram;
        }

        if ($this->linkedin) {
            $links['linkedin'] = $this->linkedin;
        }

        return $links;
    }

    // Scope for filtering by type
    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Scope for active agents
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope for public agents
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    // Scope for ordering agents for public display
    public function scopeOrderedForPublic($query)
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('created_at', 'desc');
    }
}
