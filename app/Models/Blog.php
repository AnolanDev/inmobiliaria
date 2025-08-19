<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Blog extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'excerpt',
        'content',
        'cover_image',
        'gallery',
        'author',
        'category',
        'tags',
        'status',
        'is_public',
        'sort_order',
        'published_at',
        'views_count',
        'meta_title',
        'meta_description',
        'meta_keywords',
    ];

    protected $casts = [
        'gallery' => 'array',
        'tags' => 'array',
        'meta_keywords' => 'array',
        'is_public' => 'boolean',
        'sort_order' => 'integer',
        'views_count' => 'integer',
        'published_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    protected $appends = [
        'cover_image_url',
        'gallery_urls',
        'status_color',
        'excerpt_preview',
        'reading_time',
    ];

    // Constants
    const STATUSES = [
        'draft' => 'Borrador',
        'published' => 'Publicado',
        'archived' => 'Archivado',
    ];

    const CATEGORIES = [
        'inmobiliario' => 'Sector Inmobiliario',
        'mercado' => 'Tendencias de Mercado',
        'consejos' => 'Consejos de Compra',
        'inversion' => 'Inversión Inmobiliaria',
        'legal' => 'Aspectos Legales',
        'financiacion' => 'Financiación',
        'noticias' => 'Noticias del Sector',
    ];

    // Mutators & Accessors
    public function setTitleAttribute($value)
    {
        $this->attributes['title'] = $value;
        if (empty($this->attributes['slug'])) {
            $this->attributes['slug'] = Str::slug($value);
        }
    }

    public function getCoverImageUrlAttribute()
    {
        if ($this->cover_image) {
            return asset('storage/' . $this->cover_image);
        }
        // Default blog image
        return 'https://images.unsplash.com/photo-1486312338219-ce68d2c6f44d?w=800&h=600&fit=crop';
    }

    public function getGalleryUrlsAttribute()
    {
        if (!$this->gallery || !is_array($this->gallery)) {
            return [];
        }
        
        return array_map(function ($path) {
            return asset('storage/' . $path);
        }, $this->gallery);
    }

    public function getStatusColorAttribute()
    {
        $colors = [
            'draft' => 'bg-yellow-100 text-yellow-800',
            'published' => 'bg-green-100 text-green-800',
            'archived' => 'bg-gray-100 text-gray-800',
        ];
        
        return $colors[$this->status] ?? 'bg-gray-100 text-gray-800';
    }

    public function getExcerptPreviewAttribute()
    {
        if ($this->excerpt) {
            return Str::limit($this->excerpt, 150);
        }
        
        // Generate excerpt from content if not provided
        $content = strip_tags($this->content);
        return Str::limit($content, 150);
    }

    public function getReadingTimeAttribute()
    {
        $wordCount = str_word_count(strip_tags($this->content));
        $readingTime = ceil($wordCount / 200); // Average reading speed: 200 words per minute
        
        return $readingTime . ' min de lectura';
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
                    ->where('is_public', true)
                    ->whereNotNull('published_at')
                    ->where('published_at', '<=', now());
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeByTag($query, $tag)
    {
        return $query->whereJsonContains('tags', $tag);
    }

    public function scopeOrderedForPublic($query)
    {
        return $query->orderBy('sort_order', 'asc')
                    ->orderBy('published_at', 'desc');
    }

    // Methods
    public function incrementViews()
    {
        $this->increment('views_count');
    }

    public function isPublished()
    {
        return $this->status === 'published' && 
               $this->is_public && 
               $this->published_at && 
               $this->published_at <= now();
    }

    public function publish()
    {
        $this->update([
            'status' => 'published',
            'published_at' => now(),
        ]);
    }

    public function unpublish()
    {
        $this->update([
            'status' => 'draft',
            'published_at' => null,
        ]);
    }
}
