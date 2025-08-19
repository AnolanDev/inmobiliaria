<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreBlogRequest;
use App\Http\Requests\UpdateBlogRequest;
use App\Models\Blog;
use App\Services\BlogMediaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class BlogController extends Controller
{
    public function __construct(
        private BlogMediaService $mediaService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $query = Blog::query()
            ->orderBy('sort_order', 'asc')
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('content', 'like', '%' . $request->search . '%')
                  ->orWhere('excerpt', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('author')) {
            $query->where('author', 'like', '%' . $request->author . '%');
        }

        $blogs = $query->paginate(12);

        return Inertia::render('Blogs/Index', [
            'blogs' => $blogs,
            'filters' => $request->only(['search', 'category', 'status', 'author']),
            'categories' => Blog::CATEGORIES,
            'statuses' => Blog::STATUSES,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('Blogs/Create', [
            'categories' => Blog::CATEGORIES,
            'statuses' => Blog::STATUSES,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreBlogRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        // Create the blog first
        $blog = Blog::create([
            'title' => $validated['title'],
            'slug' => $validated['slug'] ?: null, // Let the model handle auto-generation
            'excerpt' => $validated['excerpt'],
            'content' => $validated['content'],
            'author' => $validated['author'] ?? auth()->user()->name,
            'category' => $validated['category'],
            'tags' => $validated['tags'] ?? [],
            'status' => $validated['status'],
            'is_public' => $validated['is_public'] ?? false,
            'sort_order' => $validated['sort_order'] ?? 0,
            'published_at' => $validated['status'] === 'published' ? now() : null,
            'meta_title' => $validated['meta_title'],
            'meta_description' => $validated['meta_description'],
            'meta_keywords' => $validated['meta_keywords'] ?? [],
            'cover_image' => '', // Will be updated after file upload
        ]);

        // Handle cover image upload
        $coverImagePath = $this->mediaService->storeCoverImage(
            $request->file('cover_image'),
            $blog->id
        );

        // Handle gallery images
        $galleryPaths = [];
        if ($request->hasFile('gallery')) {
            $galleryPaths = $this->mediaService->storeGalleryImages(
                $request->file('gallery'),
                $blog->id
            );
        }

        // Update blog with file paths
        $blog->update([
            'cover_image' => $coverImagePath,
            'gallery' => $galleryPaths,
        ]);

        return redirect()->route('blogs.index')
            ->with('success', 'Blog creado exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Blog $blog): Response
    {
        return Inertia::render('Blogs/Show', [
            'blog' => $blog,
            'categories' => Blog::CATEGORIES,
            'statuses' => Blog::STATUSES,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Blog $blog): Response
    {
        return Inertia::render('Blogs/Edit', [
            'blog' => $blog,
            'categories' => Blog::CATEGORIES,
            'statuses' => Blog::STATUSES,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateBlogRequest $request, Blog $blog): RedirectResponse
    {
        $validated = $request->validated();

        // Update basic blog information
        $blog->update([
            'title' => $validated['title'],
            'slug' => $validated['slug'] ?: null,
            'excerpt' => $validated['excerpt'],
            'content' => $validated['content'],
            'author' => $validated['author'] ?? $blog->author,
            'category' => $validated['category'],
            'tags' => $validated['tags'] ?? [],
            'status' => $validated['status'],
            'is_public' => $validated['is_public'] ?? false,
            'sort_order' => $validated['sort_order'] ?? $blog->sort_order,
            'published_at' => $validated['status'] === 'published' && !$blog->published_at ? now() : $blog->published_at,
            'meta_title' => $validated['meta_title'],
            'meta_description' => $validated['meta_description'],
            'meta_keywords' => $validated['meta_keywords'] ?? [],
        ]);

        // Handle cover image update
        if ($request->hasFile('cover_image')) {
            $coverImagePath = $this->mediaService->updateCoverImage(
                $request->file('cover_image'),
                $blog
            );
            $blog->update(['cover_image' => $coverImagePath]);
        }

        // Handle gallery updates
        $newGalleryFiles = $request->hasFile('gallery') ? $request->file('gallery') : [];
        $removeGalleryFiles = $validated['remove_gallery'] ?? [];

        if (!empty($newGalleryFiles) || !empty($removeGalleryFiles)) {
            $updatedGallery = $this->mediaService->updateGalleryImages(
                $newGalleryFiles,
                $removeGalleryFiles,
                $blog
            );
            $blog->update(['gallery' => $updatedGallery]);
        }

        return redirect()->route('blogs.index')
            ->with('success', 'Blog actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Blog $blog): RedirectResponse
    {
        // Delete all associated media files
        $this->mediaService->deleteBlogMedia($blog);

        // Delete the blog (soft delete due to SoftDeletes trait)
        $blog->delete();

        return redirect()->route('blogs.index')
            ->with('success', 'Blog eliminado exitosamente.');
    }

    /**
     * Update blogs sort order in bulk
     */
    public function updateOrder(Request $request)
    {
        $request->validate([
            'blogs' => 'required|array',
            'blogs.*.id' => 'required|exists:blogs,id',
            'blogs.*.sort_order' => 'required|integer|min:0'
        ]);

        foreach ($request->blogs as $blogData) {
            Blog::where('id', $blogData['id'])
                ->update(['sort_order' => $blogData['sort_order']]);
        }

        return back()->with('success', 'Orden de blogs actualizado exitosamente.');
    }

    /**
     * Toggle blog visibility
     */
    public function toggleVisibility(Blog $blog)
    {
        $blog->update([
            'is_public' => !$blog->is_public
        ]);

        return back()->with('success', 'Visibilidad del blog actualizada exitosamente.');
    }

    /**
     * Toggle blog status (publish/unpublish)
     */
    public function toggleStatus(Blog $blog)
    {
        if ($blog->status === 'published') {
            $blog->unpublish();
            $message = 'Blog despublicado exitosamente.';
        } else {
            $blog->publish();
            $message = 'Blog publicado exitosamente.';
        }

        return back()->with('success', $message);
    }

    /**
     * Duplicate blog
     */
    public function duplicate(Blog $blog): RedirectResponse
    {
        $duplicatedBlog = $blog->replicate();
        $duplicatedBlog->title = $blog->title . ' (Copia)';
        $duplicatedBlog->slug = null; // Let the model generate new slug
        $duplicatedBlog->status = 'draft';
        $duplicatedBlog->is_public = false;
        $duplicatedBlog->published_at = null;
        $duplicatedBlog->views_count = 0;
        $duplicatedBlog->sort_order = 0;
        $duplicatedBlog->save();

        return redirect()->route('blogs.edit', $duplicatedBlog)
            ->with('success', 'Blog duplicado exitosamente. Puedes editarlo ahora.');
    }

    /**
     * Get blog analytics data
     */
    public function analytics(Blog $blog): Response
    {
        return Inertia::render('Blogs/Analytics', [
            'blog' => $blog,
            'analytics' => [
                'views_count' => $blog->views_count,
                'reading_time' => $blog->reading_time,
                'word_count' => str_word_count(strip_tags($blog->content)),
                'character_count' => strlen(strip_tags($blog->content)),
                'published_days' => $blog->published_at ? $blog->published_at->diffInDays(now()) : 0,
            ]
        ]);
    }
}
