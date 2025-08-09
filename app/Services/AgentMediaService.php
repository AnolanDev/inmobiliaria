<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AgentMediaService
{
    private const PROFILE_PATH = 'agents/profiles';
    private const GALLERY_PATH = 'agents/gallery';
    private const VIDEOS_PATH = 'agents/videos';

    public function handleProfilePicture(?UploadedFile $file, ?string $oldPath = null): ?string
    {
        if (!$file) {
            return $oldPath;
        }

        // Delete old profile picture if exists
        if ($oldPath && Storage::disk('public')->exists($oldPath)) {
            Storage::disk('public')->delete($oldPath);
        }

        // Generate unique filename
        $filename = $this->generateUniqueFilename($file, 'profile');
        
        // Store the file
        $path = $file->storeAs(self::PROFILE_PATH, $filename, 'public');

        return $path;
    }

    public function handleGallery(array $files = [], array $existingPaths = [], array $removeFiles = []): array
    {
        $finalPaths = array_filter($existingPaths, function ($path) use ($removeFiles) {
            return !in_array($path, $removeFiles);
        });

        // Delete removed files
        foreach ($removeFiles as $path) {
            if ($path && Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }

        // Process new files
        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                $filename = $this->generateUniqueFilename($file, 'gallery');
                $path = $file->storeAs(self::GALLERY_PATH, $filename, 'public');
                $finalPaths[] = $path;
            }
        }

        return array_values($finalPaths);
    }

    public function handleVideos(array $files = [], array $existingPaths = [], array $removeFiles = []): array
    {
        $finalPaths = array_filter($existingPaths, function ($path) use ($removeFiles) {
            return !in_array($path, $removeFiles);
        });

        // Delete removed files
        foreach ($removeFiles as $path) {
            if ($path && Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }

        // Process new files
        foreach ($files as $file) {
            if ($file instanceof UploadedFile) {
                $filename = $this->generateUniqueFilename($file, 'video');
                $path = $file->storeAs(self::VIDEOS_PATH, $filename, 'public');
                $finalPaths[] = $path;
            }
        }

        return array_values($finalPaths);
    }

    public function deleteAgentMedia(?string $profilePicture, array $gallery = [], array $videos = []): void
    {
        // Delete profile picture
        if ($profilePicture && Storage::disk('public')->exists($profilePicture)) {
            Storage::disk('public')->delete($profilePicture);
        }

        // Delete gallery images
        foreach ($gallery as $path) {
            if ($path && Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }

        // Delete videos
        foreach ($videos as $path) {
            if ($path && Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }
    }

    private function generateUniqueFilename(UploadedFile $file, string $prefix): string
    {
        $timestamp = now()->format('Y-m-d_H-i-s');
        $randomString = Str::random(8);
        $extension = $file->getClientOriginalExtension();
        
        return "{$prefix}_{$timestamp}_{$randomString}.{$extension}";
    }

    public function validateFile(UploadedFile $file, string $type = 'image'): bool
    {
        if ($type === 'image') {
            $allowedMimes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif', 'image/webp'];
            $maxSize = 5 * 1024 * 1024; // 5MB
        } elseif ($type === 'video') {
            $allowedMimes = ['video/mp4', 'video/mov', 'video/avi', 'video/wmv', 'video/webm'];
            $maxSize = 100 * 1024 * 1024; // 100MB
        } else {
            return false;
        }

        return in_array($file->getMimeType(), $allowedMimes) && $file->getSize() <= $maxSize;
    }

    public function createDirectories(): void
    {
        $directories = [
            self::PROFILE_PATH,
            self::GALLERY_PATH,
            self::VIDEOS_PATH,
        ];

        foreach ($directories as $directory) {
            if (!Storage::disk('public')->exists($directory)) {
                Storage::disk('public')->makeDirectory($directory);
            }
        }
    }

    public function getFileInfo(string $path): ?array
    {
        if (!Storage::disk('public')->exists($path)) {
            return null;
        }

        return [
            'path' => $path,
            'url' => asset('storage/' . $path),
            'size' => Storage::disk('public')->size($path),
            'last_modified' => Storage::disk('public')->lastModified($path),
        ];
    }
}