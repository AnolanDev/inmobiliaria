<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Blog;

class BlogMediaService
{
    /**
     * Store cover image for blog
     */
    public function storeCoverImage(?UploadedFile $file, int $blogId): ?string
    {
        if (!$file) {
            return null;
        }

        $filename = $this->generateFilename($file, 'cover');
        $path = "blogs/{$blogId}/cover/{$filename}";
        
        Storage::disk('public')->putFileAs(
            "blogs/{$blogId}/cover",
            $file,
            $filename
        );

        return $path;
    }

    /**
     * Update cover image for blog
     */
    public function updateCoverImage(UploadedFile $file, Blog $blog): string
    {
        // Delete old cover image if exists
        if ($blog->cover_image) {
            Storage::disk('public')->delete($blog->cover_image);
        }

        return $this->storeCoverImage($file, $blog->id);
    }

    /**
     * Store gallery images for blog
     */
    public function storeGalleryImages(array $files, int $blogId): array
    {
        $paths = [];
        
        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                $filename = $this->generateFilename($file, 'gallery');
                $path = "blogs/{$blogId}/gallery/{$filename}";
                
                Storage::disk('public')->putFileAs(
                    "blogs/{$blogId}/gallery",
                    $file,
                    $filename
                );
                
                $paths[] = $path;
            }
        }

        return $paths;
    }

    /**
     * Update gallery images for blog
     */
    public function updateGalleryImages(array $newFiles, array $removeFiles, Blog $blog): array
    {
        $currentGallery = $blog->gallery ?? [];
        
        // Remove specified files
        foreach ($removeFiles as $filePath) {
            if (($key = array_search($filePath, $currentGallery)) !== false) {
                unset($currentGallery[$key]);
                Storage::disk('public')->delete($filePath);
            }
        }

        // Add new files
        if (!empty($newFiles)) {
            $newPaths = $this->storeGalleryImages($newFiles, $blog->id);
            $currentGallery = array_merge($currentGallery, $newPaths);
        }

        return array_values($currentGallery);
    }

    /**
     * Delete all media files for blog
     */
    public function deleteBlogMedia(Blog $blog): void
    {
        // Delete cover image
        if ($blog->cover_image) {
            Storage::disk('public')->delete($blog->cover_image);
        }

        // Delete gallery images
        if ($blog->gallery) {
            foreach ($blog->gallery as $imagePath) {
                Storage::disk('public')->delete($imagePath);
            }
        }

        // Delete entire blog directory
        Storage::disk('public')->deleteDirectory("blogs/{$blog->id}");
    }

    /**
     * Generate unique filename for uploaded file
     */
    private function generateFilename(UploadedFile $file, string $type): string
    {
        $extension = $file->getClientOriginalExtension();
        $timestamp = now()->format('Y-m-d_H-i-s');
        $random = Str::random(8);
        
        return "{$type}_{$timestamp}_{$random}.{$extension}";
    }

    /**
     * Optimize image after upload (future enhancement)
     */
    private function optimizeImage(string $path): void
    {
        // Future: Add image optimization logic here
        // - Resize large images
        // - Compress images
        // - Generate different sizes for responsive images
    }

    /**
     * Generate responsive image sizes (future enhancement)
     */
    public function generateResponsiveSizes(string $imagePath): array
    {
        // Future: Generate different image sizes
        // - Thumbnail (300x200)
        // - Medium (800x600)
        // - Large (1200x800)
        
        return [
            'thumbnail' => $imagePath,
            'medium' => $imagePath,
            'large' => $imagePath,
        ];
    }
}
