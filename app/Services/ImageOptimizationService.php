<?php

namespace App\Services;

use Intervention\Image\Laravel\Facades\Image;
use Intervention\Image\Interfaces\ImageInterface;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class ImageOptimizationService
{
    protected array $sizes;
    protected int $quality;
    protected string $format;

    public function __construct()
    {
        $this->sizes = config('images.sizes', [
            'thumbnail' => 400,
            'medium' => 800,
            'large' => 1200,
            'original' => null
        ]);
        $this->quality = config('images.quality', 85);
        $this->format = config('images.format', 'jpg');
    }

    /**
     * Process and store image with multiple sizes
     */
    public function processImage(UploadedFile $file, string $path, string $prefix = ''): array
    {
        try {
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $timestamp = now()->timestamp;
            $hash = Str::random(10);
            
            $urls = [];
            
            // Process each size
            foreach ($this->sizes as $sizeName => $width) {
                $filename = $this->generateFilename($prefix, $timestamp, $hash, $sizeName);
                $fullPath = $path . '/' . $filename;
                
                // Load and process image
                $image = Image::read($file->path());
                
                if ($width && $sizeName !== 'original') {
                    $image = $this->resizeImage($image, $width);
                }
                
                $image = $this->optimizeImage($image);
                
                // Convert to desired format and save
                $processedImage = $image->toJpeg($this->quality);
                Storage::disk('public')->put($fullPath, $processedImage);
                
                $urls[$sizeName] = $fullPath;
                
                Log::info("Image processed: {$fullPath}", [
                    'size' => $sizeName,
                    'width' => $width,
                    'file_size' => Storage::disk('public')->size($fullPath)
                ]);
            }
            
            return $urls;
            
        } catch (\Exception $e) {
            Log::error('Image processing failed', [
                'error' => $e->getMessage(),
                'file' => $file->getClientOriginalName(),
                'path' => $path
            ]);
            throw $e;
        }
    }

    /**
     * Generate responsive image URLs for API responses
     */
    public function generateResponsiveUrls(array $imagePaths, string $type = 'projects', int $id = null): array
    {
        $urls = [];
        
        foreach ($imagePaths as $sizeName => $path) {
            if ($path) {
                // Use API image routes instead of direct storage access
                $filename = basename($path);
                $urls[$sizeName] = [
                    'url' => $id ? url("api/images/{$type}/{$id}/{$filename}") : asset('storage/' . $path),
                    'width' => $this->sizes[$sizeName] ?? null
                ];
            }
        }
        
        return $urls;
    }

    /**
     * Get image URLs with fallbacks
     */
    public function getImageWithFallback(array $imagePaths, string $fallbackCategory = 'default'): array
    {
        $urls = $this->generateResponsiveUrls($imagePaths);
        
        // If no images exist, return fallback placeholders
        if (empty($urls)) {
            return $this->getFallbackUrls($fallbackCategory);
        }
        
        // Ensure all sizes have fallbacks
        foreach ($this->sizes as $sizeName => $width) {
            if (!isset($urls[$sizeName])) {
                $urls[$sizeName] = [
                    'url' => $this->getFallbackUrl($fallbackCategory, $width),
                    'width' => $width
                ];
            }
        }
        
        return $urls;
    }

    /**
     * Clean up old image files
     */
    public function deleteImages(array $imagePaths): bool
    {
        try {
            foreach ($imagePaths as $path) {
                if ($path && Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                    Log::info("Deleted image: {$path}");
                }
            }
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to delete images', [
                'error' => $e->getMessage(),
                'paths' => $imagePaths
            ]);
            return false;
        }
    }

    /**
     * Resize image maintaining aspect ratio
     */
    protected function resizeImage(ImageInterface $image, int $maxWidth): ImageInterface
    {
        $width = $image->width();
        $height = $image->height();
        
        if ($width > $maxWidth) {
            $newHeight = intval(($height * $maxWidth) / $width);
            return $image->resize($maxWidth, $newHeight);
        }
        
        return $image;
    }

    /**
     * Apply optimization settings
     */
    protected function optimizeImage(ImageInterface $image): ImageInterface
    {
        // Apply any additional optimizations here
        // For example: sharpening, color correction, etc.
        return $image;
    }

    /**
     * Generate consistent filename
     */
    protected function generateFilename(string $prefix, int $timestamp, string $hash, string $size): string
    {
        $sizeSuffix = $size === 'original' ? '' : "_{$size}";
        return "{$prefix}_{$timestamp}_{$hash}{$sizeSuffix}.{$this->format}";
    }

    /**
     * Get fallback URLs for different categories
     */
    public function getFallbackUrls(string $category): array
    {
        $baseUrl = $this->getFallbackBaseUrl($category);
        $urls = [];
        
        foreach ($this->sizes as $sizeName => $width) {
            $urls[$sizeName] = [
                'url' => $this->getFallbackUrl($category, $width),
                'width' => $width
            ];
        }
        
        return $urls;
    }

    /**
     * Get fallback URL for specific size (public method)
     */
    public function getFallbackUrl(string $category, ?int $width): string
    {
        $size = $width ? "w={$width}&h=" . intval($width * 0.75) : "w=800&h=600";
        
        $categories = [
            'project' => "https://images.unsplash.com/photo-1560518883-ce09059eeffa?{$size}&fit=crop&crop=entropy&auto=format",
            'property' => "https://images.unsplash.com/photo-1570129477492-45c003edd2be?{$size}&fit=crop&crop=entropy&auto=format",
            'agent' => "https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?{$size}&fit=crop&crop=face&auto=format",
            'blog' => "https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?{$size}&fit=crop&crop=entropy&auto=format",
            'default' => "https://images.unsplash.com/photo-1560518883-ce09059eeffa?{$size}&fit=crop&crop=entropy&auto=format"
        ];
        
        return $categories[$category] ?? $categories['default'];
    }

    /**
     * Get base fallback URL
     */
    public function getFallbackBaseUrl(string $category): string
    {
        return $this->getFallbackUrl($category, 800);
    }

    /**
     * Validate image file
     */
    public function validateImage(UploadedFile $file): bool
    {
        $allowedMimes = config('images.validation.allowed_mimes', ['image/jpeg', 'image/jpg', 'image/png', 'image/webp']);
        $maxSize = config('images.validation.max_size', 10 * 1024 * 1024);
        
        if (!in_array($file->getMimeType(), $allowedMimes)) {
            return false;
        }
        
        if ($file->getSize() > $maxSize) {
            return false;
        }
        
        return true;
    }

    /**
     * Get image info
     */
    public function getImageInfo(string $path): ?array
    {
        try {
            if (!Storage::disk('public')->exists($path)) {
                return null;
            }
            
            $fullPath = Storage::disk('public')->path($path);
            $image = Image::read($fullPath);
            
            return [
                'width' => $image->width(),
                'height' => $image->height(),
                'size' => Storage::disk('public')->size($path),
                'url' => asset('storage/' . $path)
            ];
        } catch (\Exception $e) {
            Log::error('Failed to get image info', [
                'path' => $path,
                'error' => $e->getMessage()
            ]);
            return null;
        }
    }
}