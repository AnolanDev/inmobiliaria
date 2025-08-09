<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\Property;

class PropertyMediaService
{
    private const PROPERTIES_STORAGE_PATH = 'properties';

    /**
     * Store cover image for a property
     */
    public function storeCoverImage(UploadedFile $file, int $propertyId): string
    {
        $filename = 'cover_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $path = self::PROPERTIES_STORAGE_PATH . '/covers/' . $propertyId . '/' . $filename;

        // Create directory if it doesn't exist
        $directory = self::PROPERTIES_STORAGE_PATH . '/covers/' . $propertyId;
        if (!Storage::disk('public')->exists($directory)) {
            Storage::disk('public')->makeDirectory($directory);
        }

        // Store the image
        $this->optimizeAndStoreImage($file, $path);

        // Log for debugging
        Log::info('Property cover image stored', [
            'property_id' => $propertyId,
            'path' => $path,
            'full_path' => storage_path('app/public/' . $path),
            'exists' => file_exists(storage_path('app/public/' . $path))
        ]);

        return $path;
    }

    /**
     * Store gallery images for a property
     */
    public function storeGalleryImages(array $files, int $propertyId): array
    {
        $paths = [];

        // Create directory if it doesn't exist
        $directory = self::PROPERTIES_STORAGE_PATH . '/gallery/' . $propertyId;
        if (!Storage::disk('public')->exists($directory)) {
            Storage::disk('public')->makeDirectory($directory);
        }

        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                $filename = 'gallery_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $path = self::PROPERTIES_STORAGE_PATH . '/gallery/' . $propertyId . '/' . $filename;

                // Store the image
                $this->optimizeAndStoreImage($file, $path);
                $paths[] = $path;

                // Log for debugging
                Log::info('Property gallery image stored', [
                    'property_id' => $propertyId,
                    'path' => $path,
                    'exists' => file_exists(storage_path('app/public/' . $path))
                ]);
            }
        }

        return $paths;
    }

    /**
     * Store videos for a property
     */
    public function storeVideos(array $files, int $propertyId): array
    {
        $paths = [];

        // Create directory if it doesn't exist
        $directory = self::PROPERTIES_STORAGE_PATH . '/videos/' . $propertyId;
        if (!Storage::disk('public')->exists($directory)) {
            Storage::disk('public')->makeDirectory($directory);
        }

        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                $filename = 'video_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $path = self::PROPERTIES_STORAGE_PATH . '/videos/' . $propertyId . '/' . $filename;

                Storage::disk('public')->putFileAs(
                    dirname($path),
                    $file,
                    basename($path)
                );

                $paths[] = $path;

                // Log for debugging
                Log::info('Property video stored', [
                    'property_id' => $propertyId,
                    'path' => $path,
                    'exists' => file_exists(storage_path('app/public/' . $path))
                ]);
            }
        }

        return $paths;
    }

    /**
     * Optimize and store image with compression
     */
    private function optimizeAndStoreImage(UploadedFile $file, string $path): void
    {
        // Create directories if they don't exist
        $directory = dirname($path);
        if (!Storage::disk('public')->exists($directory)) {
            Storage::disk('public')->makeDirectory($directory);
        }

        // Store the file directly for now
        // In a real app, you might want to use Intervention Image for optimization
        Storage::disk('public')->putFileAs(
            dirname($path),
            $file,
            basename($path)
        );

        // If you have Intervention Image installed, you could optimize like this:
        /*
        $image = Image::make($file->getRealPath());
        
        // Resize if too large (max 1920px width)
        if ($image->width() > 1920) {
            $image->resize(1920, null, function ($constraint) {
                $constraint->aspectRatio();
                $constraint->upsize();
            });
        }
        
        // Save with 85% quality
        $image->save(storage_path('app/public/' . $path), 85);
        */
    }

    /**
     * Update cover image
     */
    public function updateCoverImage(UploadedFile $file, Property $property): string
    {
        // Delete old cover image if exists
        if ($property->cover_image && Storage::disk('public')->exists($property->cover_image)) {
            Storage::disk('public')->delete($property->cover_image);
        }

        return $this->storeCoverImage($file, $property->id);
    }

    /**
     * Update gallery images
     */
    public function updateGalleryImages(array $newFiles, array $removeFiles, Property $property): array
    {
        $currentGallery = $property->gallery ?? [];

        // Remove specified files
        if (!empty($removeFiles)) {
            $this->deleteFiles($removeFiles);
            $currentGallery = array_diff($currentGallery, $removeFiles);
        }

        // Add new files
        if (!empty($newFiles)) {
            $newPaths = $this->storeGalleryImages($newFiles, $property->id);
            $currentGallery = array_merge($currentGallery, $newPaths);
        }

        return array_values($currentGallery);
    }

    /**
     * Update videos
     */
    public function updateVideos(array $newFiles, array $removeFiles, Property $property): array
    {
        $currentVideos = $property->videos ?? [];

        // Remove specified files
        if (!empty($removeFiles)) {
            $this->deleteFiles($removeFiles);
            $currentVideos = array_diff($currentVideos, $removeFiles);
        }

        // Add new files
        if (!empty($newFiles)) {
            $newPaths = $this->storeVideos($newFiles, $property->id);
            $currentVideos = array_merge($currentVideos, $newPaths);
        }

        return array_values($currentVideos);
    }

    /**
     * Delete property media files
     */
    public function deletePropertyMedia(Property $property): void
    {
        $propertyPaths = [
            self::PROPERTIES_STORAGE_PATH . '/covers/' . $property->id,
            self::PROPERTIES_STORAGE_PATH . '/gallery/' . $property->id,
            self::PROPERTIES_STORAGE_PATH . '/videos/' . $property->id,
        ];

        foreach ($propertyPaths as $path) {
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->deleteDirectory($path);
            }
        }
    }

    /**
     * Delete specific files from property
     */
    public function deleteFiles(array $filePaths): void
    {
        foreach ($filePaths as $path) {
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }
    }

    /**
     * Get file URL
     */
    public function getFileUrl(string $path): string
    {
        return Storage::disk('public')->url($path);
    }

    /**
     * Check if file exists
     */
    public function fileExists(string $path): bool
    {
        return Storage::disk('public')->exists($path);
    }
}