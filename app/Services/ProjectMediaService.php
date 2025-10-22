<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\Project;
use App\Services\ImageOptimizationService;

class ProjectMediaService
{
    private const PROJECTS_STORAGE_PATH = 'projects';
    
    protected ImageOptimizationService $imageService;
    
    public function __construct(ImageOptimizationService $imageService)
    {
        $this->imageService = $imageService;
    }

    /**
     * Store cover image for a project with optimization
     */
    public function storeCoverImage(UploadedFile $file, int $projectId): array
    {
        // Simple validation
        if (!$file->isValid() || !in_array($file->getMimeType(), ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'])) {
            throw new \InvalidArgumentException('Invalid image file');
        }

        $path = self::PROJECTS_STORAGE_PATH . '/' . $projectId;
        
        // Simple file storage without optimization for now
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = $file->getClientOriginalExtension();
        $timestamp = now()->timestamp;
        $hash = substr(md5($originalName . $timestamp), 0, 10);
        
        $filename = "cover_{$timestamp}_{$hash}.{$extension}";
        $storedPath = $file->storeAs($path, $filename, 'public');

        Log::info('Cover image stored successfully', [
            'project_id' => $projectId,
            'path' => $storedPath
        ]);

        // Return simple path instead of array for now
        return [$storedPath];
    }

    /**
     * Store gallery images for a project with optimization
     */
    public function storeGalleryImages(array $files, int $projectId): array
    {
        $allPaths = [];
        $path = self::PROJECTS_STORAGE_PATH . '/' . $projectId;

        foreach ($files as $index => $file) {
            if ($file instanceof UploadedFile && $file->isValid() && 
                in_array($file->getMimeType(), ['image/jpeg', 'image/jpg', 'image/png', 'image/webp'])) {
                
                $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $extension = $file->getClientOriginalExtension();
                $timestamp = now()->timestamp;
                $hash = substr(md5($originalName . $timestamp . $index), 0, 10);
                
                $filename = "gallery_{$index}_{$timestamp}_{$hash}.{$extension}";
                $storedPath = $file->storeAs($path, $filename, 'public');
                
                $allPaths[] = $storedPath;

                Log::info('Gallery image stored successfully', [
                    'project_id' => $projectId,
                    'index' => $index,
                    'path' => $storedPath
                ]);
            }
        }

        return $allPaths;
    }

    /**
     * Store videos for a project
     */
    public function storeVideos(array $files, int $projectId): array
    {
        $paths = [];

        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                $filename = 'video_' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $path = self::PROJECTS_STORAGE_PATH . '/' . $projectId . '/' . $filename;

                Storage::disk('public')->putFileAs(
                    dirname($path),
                    $file,
                    basename($path)
                );

                $paths[] = $path;
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

        // For now, just store the file directly
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
     * Delete project media files
     */
    public function deleteProjectMedia(Project $project): void
    {
        $projectPath = self::PROJECTS_STORAGE_PATH . '/' . $project->id;

        if (Storage::disk('public')->exists($projectPath)) {
            Storage::disk('public')->deleteDirectory($projectPath);
        }
    }

    /**
     * Delete specific files from project
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
     * Update cover image
     */
    public function updateCoverImage(UploadedFile $file, Project $project): array
    {
        // Delete old cover image files if they exist
        if ($project->cover_image) {
            if (is_array($project->cover_image)) {
                // New format: delete all sizes
                $this->imageService->deleteImages($project->cover_image);
            } elseif (is_string($project->cover_image) && Storage::disk('public')->exists($project->cover_image)) {
                // Legacy format: delete single file
                Storage::disk('public')->delete($project->cover_image);
            }
        }

        return $this->storeCoverImage($file, $project->id);
    }

    /**
     * Update gallery images
     */
    public function updateGalleryImages(array $newFiles, array $removeFiles, Project $project): array
    {
        $currentGallery = $project->gallery ?? [];

        // Remove specified files by index
        if (!empty($removeFiles)) {
            // Sort indexes in descending order to remove from highest to lowest
            rsort($removeFiles);
            
            foreach ($removeFiles as $index) {
                if (isset($currentGallery[$index])) {
                    // Delete the physical files for this gallery item
                    if (is_array($currentGallery[$index])) {
                        $this->deleteFiles(array_values($currentGallery[$index]));
                    }
                    // Remove from array
                    unset($currentGallery[$index]);
                }
            }
            
            // Re-index array
            $currentGallery = array_values($currentGallery);
        }

        // Add new files
        if (!empty($newFiles)) {
            $newPaths = $this->storeGalleryImages($newFiles, $project->id);
            $currentGallery = array_merge($currentGallery, $newPaths);
        }

        return $currentGallery;
    }

    /**
     * Update videos
     */
    public function updateVideos(array $newFiles, array $removeFiles, Project $project): array
    {
        $currentVideos = $project->videos ?? [];

        // Remove specified files
        if (!empty($removeFiles)) {
            $this->deleteFiles($removeFiles);
            $currentVideos = array_diff($currentVideos, $removeFiles);
        }

        // Add new files
        if (!empty($newFiles)) {
            $newPaths = $this->storeVideos($newFiles, $project->id);
            $currentVideos = array_merge($currentVideos, $newPaths);
        }

        return array_values($currentVideos);
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