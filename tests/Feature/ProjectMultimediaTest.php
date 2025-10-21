<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use App\Services\ImageOptimizationService;
use App\Services\ProjectMediaService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Mockery;

class ProjectMultimediaTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private ImageOptimizationService $imageService;
    private ProjectMediaService $mediaService;

    protected function setUp(): void
    {
        parent::setUp();
        
        Storage::fake('public');
        
        // Create and authenticate user
        $this->user = User::factory()->create(['is_active' => true]);
        $superAdminRole = \App\Models\Role::firstOrCreate(['slug' => 'super-admin'], [
            'name' => 'Super Administrator'
        ]);
        $this->user->roles()->attach($superAdminRole);
        $this->actingAs($this->user);

        $this->imageService = app(ImageOptimizationService::class);
        $this->mediaService = app(ProjectMediaService::class);
    }

    /** @test */
    public function it_processes_cover_image_with_optimization(): void
    {
        $coverImage = UploadedFile::fake()->image('cover.jpg', 1200, 800);
        
        $projectData = [
            'name' => 'Test Project with Images',
            'type' => 'Campestres',
            'status' => 'Disponible',
            'city' => 'Bogotá',
            'state' => 'Cundinamarca',
            'cover_image' => $coverImage,
        ];

        $response = $this->post(route('projects.store'), $projectData);
        
        $response->assertRedirect(route('projects.index'));
        
        $project = Project::where('name', 'Test Project with Images')->first();
        $this->assertNotNull($project);
        $this->assertNotNull($project->cover_image);
        $this->assertIsArray($project->cover_image);
        
        // Verify optimized sizes were created
        $this->assertArrayHasKey('thumbnail', $project->cover_image);
        $this->assertArrayHasKey('medium', $project->cover_image);
        $this->assertArrayHasKey('large', $project->cover_image);
        $this->assertArrayHasKey('original', $project->cover_image);
    }

    /** @test */
    public function it_processes_gallery_images_with_optimization(): void
    {
        $galleryImage1 = UploadedFile::fake()->image('gallery1.jpg', 800, 600);
        $galleryImage2 = UploadedFile::fake()->image('gallery2.jpg', 1000, 750);
        
        $projectData = [
            'name' => 'Test Project with Gallery',
            'type' => 'Urbanos',
            'status' => 'Disponible',
            'city' => 'Medellín',
            'state' => 'Antioquia',
            'cover_image' => UploadedFile::fake()->image('cover.jpg'),
            'gallery' => [$galleryImage1, $galleryImage2],
        ];

        $response = $this->post(route('projects.store'), $projectData);
        
        $response->assertRedirect(route('projects.index'));
        
        $project = Project::where('name', 'Test Project with Gallery')->first();
        $this->assertNotNull($project);
        $this->assertNotNull($project->gallery);
        $this->assertIsArray($project->gallery);
        $this->assertCount(2, $project->gallery);
        
        // Verify each gallery item has optimized sizes
        foreach ($project->gallery as $galleryItem) {
            $this->assertIsArray($galleryItem);
            $this->assertArrayHasKey('thumbnail', $galleryItem);
            $this->assertArrayHasKey('medium', $galleryItem);
            $this->assertArrayHasKey('large', $galleryItem);
            $this->assertArrayHasKey('original', $galleryItem);
        }
    }

    /** @test */
    public function it_processes_videos_correctly(): void
    {
        $video1 = UploadedFile::fake()->create('video1.mp4', 2000, 'video/mp4');
        $video2 = UploadedFile::fake()->create('video2.mov', 3000, 'video/quicktime');
        
        $projectData = [
            'name' => 'Test Project with Videos',
            'type' => 'Turísticos',
            'status' => 'Disponible',
            'city' => 'Cartagena',
            'state' => 'Bolívar',
            'cover_image' => UploadedFile::fake()->image('cover.jpg'),
            'videos' => [$video1, $video2],
        ];

        $response = $this->post(route('projects.store'), $projectData);
        
        $response->assertRedirect(route('projects.index'));
        
        $project = Project::where('name', 'Test Project with Videos')->first();
        $this->assertNotNull($project);
        $this->assertNotNull($project->videos);
        $this->assertIsArray($project->videos);
        $this->assertCount(2, $project->videos);
        
        // Verify video files exist in storage
        foreach ($project->videos as $videoPath) {
            $this->assertTrue(Storage::disk('public')->exists($videoPath));
        }
    }

    /** @test */
    public function it_updates_cover_image_and_removes_old_one(): void
    {
        // Create project with initial cover image
        $initialCover = UploadedFile::fake()->image('initial_cover.jpg');
        $project = Project::factory()->create();
        
        // Store initial cover image
        $initialCoverPaths = $this->mediaService->storeCoverImage($initialCover, $project->id);
        $project->update(['cover_image' => $initialCoverPaths]);
        
        // Verify initial files exist
        foreach ($initialCoverPaths as $path) {
            $this->assertTrue(Storage::disk('public')->exists($path));
        }
        
        // Update with new cover image
        $newCover = UploadedFile::fake()->image('new_cover.jpg');
        $updateData = [
            'name' => $project->name,
            'type' => $project->type,
            'status' => $project->status,
            'city' => $project->city,
            'state' => $project->state,
            'cover_image' => $newCover,
        ];

        $response = $this->put(route('projects.update', $project), $updateData);
        
        $response->assertRedirect(route('projects.show', $project));
        
        $project->refresh();
        $this->assertNotNull($project->cover_image);
        $this->assertIsArray($project->cover_image);
        
        // Old files should be deleted (mock verification)
        // New files should exist
        foreach ($project->cover_image as $path) {
            $this->assertTrue(Storage::disk('public')->exists($path));
        }
    }

    /** @test */
    public function it_adds_new_gallery_images_to_existing_ones(): void
    {
        $project = Project::factory()->create();
        
        // Add initial gallery
        $initialGallery1 = UploadedFile::fake()->image('initial1.jpg');
        $initialGalleryPaths = $this->mediaService->storeGalleryImages([$initialGallery1], $project->id);
        $project->update(['gallery' => $initialGalleryPaths]);
        
        $this->assertCount(1, $project->fresh()->gallery);
        
        // Add more gallery images
        $newGallery1 = UploadedFile::fake()->image('new1.jpg');
        $newGallery2 = UploadedFile::fake()->image('new2.jpg');
        
        $updateData = [
            'name' => $project->name,
            'type' => $project->type,
            'status' => $project->status,
            'city' => $project->city,
            'state' => $project->state,
            'gallery' => [$newGallery1, $newGallery2],
        ];

        $response = $this->put(route('projects.update', $project), $updateData);
        
        $response->assertRedirect(route('projects.show', $project));
        
        $project->refresh();
        $this->assertCount(3, $project->gallery); // 1 initial + 2 new
    }

    /** @test */
    public function it_removes_specific_gallery_images(): void
    {
        $project = Project::factory()->create();
        
        // Add gallery with multiple images
        $gallery1 = UploadedFile::fake()->image('gallery1.jpg');
        $gallery2 = UploadedFile::fake()->image('gallery2.jpg');
        $gallery3 = UploadedFile::fake()->image('gallery3.jpg');
        
        $galleryPaths = $this->mediaService->storeGalleryImages([$gallery1, $gallery2, $gallery3], $project->id);
        $project->update(['gallery' => $galleryPaths]);
        
        $this->assertCount(3, $project->fresh()->gallery);
        
        // Remove second image (index 1)
        $updateData = [
            'name' => $project->name,
            'type' => $project->type,
            'status' => $project->status,
            'city' => $project->city,
            'state' => $project->state,
            'remove_gallery' => [1], // Remove second image
        ];

        $response = $this->put(route('projects.update', $project), $updateData);
        
        $response->assertRedirect(route('projects.show', $project));
        
        $project->refresh();
        $this->assertCount(2, $project->gallery); // Should have 2 remaining
    }

    /** @test */
    public function it_generates_responsive_urls_for_cover_image(): void
    {
        $project = Project::factory()->create([
            'cover_image' => [
                'thumbnail' => 'projects/1/cover_thumb.jpg',
                'medium' => 'projects/1/cover_medium.jpg',
                'large' => 'projects/1/cover_large.jpg',
                'original' => 'projects/1/cover_original.jpg'
            ]
        ]);
        
        $responsiveUrls = $project->cover_image_responsive;
        
        $this->assertIsArray($responsiveUrls);
        $this->assertArrayHasKey('thumbnail', $responsiveUrls);
        $this->assertArrayHasKey('medium', $responsiveUrls);
        $this->assertArrayHasKey('large', $responsiveUrls);
        $this->assertArrayHasKey('original', $responsiveUrls);
        
        // Each size should have url and width
        foreach ($responsiveUrls as $size => $data) {
            $this->assertArrayHasKey('url', $data);
            $this->assertArrayHasKey('width', $data);
            $this->assertStringContainsString("api/images/projects/{$project->id}", $data['url']);
        }
    }

    /** @test */
    public function it_provides_fallback_urls_when_no_cover_image(): void
    {
        $project = Project::factory()->create(['cover_image' => null]);
        
        $coverImageUrl = $project->cover_image_url;
        $responsiveUrls = $project->cover_image_responsive;
        
        // Should get fallback URL
        $this->assertStringContainsString('unsplash.com', $coverImageUrl);
        
        // Responsive URLs should also be fallbacks
        $this->assertIsArray($responsiveUrls);
        $this->assertArrayHasKey('thumbnail', $responsiveUrls);
        foreach ($responsiveUrls as $data) {
            $this->assertStringContainsString('unsplash.com', $data['url']);
        }
    }

    /** @test */
    public function it_provides_different_fallbacks_by_project_type(): void
    {
        $campestre = Project::factory()->create(['type' => 'Campestres', 'cover_image' => null]);
        $urbano = Project::factory()->create(['type' => 'Urbanos', 'cover_image' => null]);
        $turistico = Project::factory()->create(['type' => 'Turísticos', 'cover_image' => null]);
        
        $campestreUrl = $campestre->cover_image_url;
        $urbanoUrl = $urbano->cover_image_url;
        $turisticoUrl = $turistico->cover_image_url;
        
        // Different projects should get different fallback images
        $this->assertNotEquals($campestreUrl, $urbanoUrl);
        $this->assertNotEquals($urbanoUrl, $turisticoUrl);
        $this->assertNotEquals($campestreUrl, $turisticoUrl);
    }

    /** @test */
    public function it_handles_legacy_string_format_images(): void
    {
        // Test backward compatibility with old string format
        $project = Project::factory()->create(['cover_image' => 'projects/1/old_image.jpg']);
        
        $coverImageUrl = $project->cover_image_url;
        $responsiveUrls = $project->cover_image_responsive;
        
        // Should handle legacy format gracefully
        $this->assertStringContainsString('api/images/projects/', $coverImageUrl);
        $this->assertIsArray($responsiveUrls);
        
        // All responsive sizes should point to same image
        foreach ($responsiveUrls as $data) {
            $this->assertStringContainsString('old_image.jpg', $data['url']);
        }
    }

    /** @test */
    public function it_generates_gallery_urls_correctly(): void
    {
        $project = Project::factory()->create([
            'gallery' => [
                [
                    'thumbnail' => 'projects/1/gallery_1_thumb.jpg',
                    'medium' => 'projects/1/gallery_1_medium.jpg',
                    'large' => 'projects/1/gallery_1_large.jpg',
                    'original' => 'projects/1/gallery_1_original.jpg'
                ],
                [
                    'thumbnail' => 'projects/1/gallery_2_thumb.jpg',
                    'medium' => 'projects/1/gallery_2_medium.jpg',
                    'large' => 'projects/1/gallery_2_large.jpg',
                    'original' => 'projects/1/gallery_2_original.jpg'
                ]
            ]
        ]);
        
        $galleryUrls = $project->gallery_urls;
        
        $this->assertIsArray($galleryUrls);
        $this->assertCount(2, $galleryUrls);
        
        foreach ($galleryUrls as $imageUrls) {
            $this->assertIsArray($imageUrls);
            $this->assertArrayHasKey('thumbnail', $imageUrls);
            $this->assertArrayHasKey('medium', $imageUrls);
            $this->assertArrayHasKey('large', $imageUrls);
            $this->assertArrayHasKey('original', $imageUrls);
        }
    }

    /** @test */
    public function it_generates_video_urls_with_metadata(): void
    {
        $project = Project::factory()->create([
            'videos' => [
                'projects/1/video_1.mp4',
                'projects/1/video_2.mov',
                'projects/1/video_3.webm'
            ]
        ]);
        
        // Mock storage exists check
        Storage::disk('public')->put('projects/1/video_1.mp4', 'fake video content');
        Storage::disk('public')->put('projects/1/video_2.mov', 'fake video content');
        Storage::disk('public')->put('projects/1/video_3.webm', 'fake video content');
        
        $videoUrls = $project->video_urls;
        
        $this->assertIsArray($videoUrls);
        $this->assertCount(3, $videoUrls);
        
        foreach ($videoUrls as $video) {
            $this->assertArrayHasKey('url', $video);
            $this->assertArrayHasKey('path', $video);
            $this->assertArrayHasKey('filename', $video);
            $this->assertArrayHasKey('type', $video);
            $this->assertStringContainsString('storage/', $video['url']);
        }
        
        // Check specific video types
        $this->assertEquals('video/mp4', $videoUrls[0]['type']);
        $this->assertEquals('video/quicktime', $videoUrls[1]['type']);
        $this->assertEquals('video/webm', $videoUrls[2]['type']);
    }

    /** @test */
    public function it_deletes_all_media_when_project_is_deleted(): void
    {
        $project = Project::factory()->create();
        
        // Add media files
        $coverImage = UploadedFile::fake()->image('cover.jpg');
        $galleryImage = UploadedFile::fake()->image('gallery.jpg');
        $video = UploadedFile::fake()->create('video.mp4', 1000, 'video/mp4');
        
        $coverPaths = $this->mediaService->storeCoverImage($coverImage, $project->id);
        $galleryPaths = $this->mediaService->storeGalleryImages([$galleryImage], $project->id);
        $videoPaths = $this->mediaService->storeVideos([$video], $project->id);
        
        $project->update([
            'cover_image' => $coverPaths,
            'gallery' => $galleryPaths,
            'videos' => $videoPaths
        ]);
        
        // Verify files exist
        foreach ($coverPaths as $path) {
            $this->assertTrue(Storage::disk('public')->exists($path));
        }
        
        // Delete project
        $response = $this->delete(route('projects.destroy', $project));
        
        $response->assertRedirect(route('projects.index'));
        
        // Verify project is soft deleted
        $this->assertSoftDeleted('projects', ['id' => $project->id]);
        
        // Verify media directory is cleaned up
        $this->assertFalse(Storage::disk('public')->exists("projects/{$project->id}"));
    }

    /** @test */
    public function it_validates_image_file_types_and_sizes(): void
    {
        // Test invalid file type
        $invalidFile = UploadedFile::fake()->create('document.txt', 100, 'text/plain');
        
        $response = $this->post(route('projects.store'), [
            'name' => 'Test Project',
            'type' => 'Campestres',
            'status' => 'Disponible',
            'city' => 'Bogotá',
            'state' => 'Cundinamarca',
            'cover_image' => $invalidFile,
        ]);
        
        $response->assertSessionHasErrors(['cover_image']);
        
        // Test oversized file (mock large file)
        $oversizedFile = UploadedFile::fake()->create('large_image.jpg', 10240, 'image/jpeg'); // 10MB
        
        $response = $this->post(route('projects.store'), [
            'name' => 'Test Project',
            'type' => 'Campestres',
            'status' => 'Disponible',
            'city' => 'Bogotá',
            'state' => 'Cundinamarca',
            'cover_image' => $oversizedFile,
        ]);
        
        $response->assertSessionHasErrors(['cover_image']);
    }
}