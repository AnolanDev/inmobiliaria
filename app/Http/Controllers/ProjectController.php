<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Models\Project;
use App\Services\ProjectMediaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProjectController extends Controller
{
    public function __construct(
        private ProjectMediaService $mediaService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $query = Project::query()
            ->withCount('properties')
            ->orderBy('sort_order', 'asc')
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Location filtering
        if ($request->filled('location')) {
            $query->byLocation($request->location);
        }

        if ($request->filled('state')) {
            $query->byState($request->state);
        }

        if ($request->filled('city')) {
            $query->byCity($request->city);
        }

        $projects = $query->paginate(12);

        return Inertia::render('Projects/Index', [
            'projects' => $projects,
            'filters' => $request->only(['search', 'type', 'status', 'location', 'state', 'city']),
            'types' => Project::TYPES,
            'statuses' => Project::STATUSES,
            'states' => Project::distinct()->whereNotNull('state')->pluck('state')->sort()->values(),
            'cities' => Project::distinct()->whereNotNull('city')->pluck('city')->sort()->values(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('Projects/Create', [
            'types' => Project::TYPES,
            'statuses' => Project::STATUSES,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProjectRequest $request): RedirectResponse
    {
        try {
            $validated = $request->validated();
            
            // Create basic project without files first
            $project = Project::create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? '',
                'type' => $validated['type'],
                'status' => $validated['status'],
                'property_count' => $validated['property_count'] ?? 0,
                'is_public' => $validated['is_public'] ?? false,
                'sort_order' => $validated['sort_order'] ?? null,
                'city' => $validated['city'],
                'state' => $validated['state'],
                'cover_image' => null,
                'gallery' => [],
                'videos' => [],
            ]);

            return redirect()->route('projects.index')
                ->with('success', 'Proyecto creado exitosamente.');
                
        } catch (\Exception $e) {
            \Log::error('Error in ProjectController::store', [
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Return a proper error response for production
            if (request()->expectsJson()) {
                return response()->json(['error' => 'Error interno del servidor'], 500);
            }
            
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Error al crear el proyecto. Por favor, inténtalo de nuevo.']);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Project $project): Response
    {
        $project->load(['properties' => function ($query) {
            $query->with('agent')->latest();
        }]);

        return Inertia::render('Projects/Show', [
            'project' => $project,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Project $project): Response
    {
        return Inertia::render('Projects/Edit', [
            'project' => $project,
            'types' => Project::TYPES,
            'statuses' => Project::STATUSES,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProjectRequest $request, Project $project): RedirectResponse
    {
        $validated = $request->validated();

        // Update basic project information
        $project->update([
            'name' => $validated['name'],
            'description' => $validated['description'],
            'type' => $validated['type'],
            'status' => $validated['status'],
            'property_count' => $validated['property_count'] ?? $project->property_count,
            'is_public' => $validated['is_public'] ?? false,
            'sort_order' => $validated['sort_order'] ?? $project->sort_order,
            'city' => $validated['city'],
            'state' => $validated['state'],
        ]);

        // Handle cover image update
        if ($request->hasFile('cover_image')) {
            $coverImagePath = $this->mediaService->updateCoverImage(
                $request->file('cover_image'),
                $project
            );
            $project->update(['cover_image' => $coverImagePath]);
        }

        // Handle gallery updates
        $newGalleryFiles = $request->hasFile('gallery') ? $request->file('gallery') : [];
        $removeGalleryFiles = $validated['remove_gallery'] ?? [];

        if (!empty($newGalleryFiles) || !empty($removeGalleryFiles)) {
            $updatedGallery = $this->mediaService->updateGalleryImages(
                $newGalleryFiles,
                $removeGalleryFiles,
                $project
            );
            $project->update(['gallery' => $updatedGallery]);
        }

        // Handle video updates
        $newVideoFiles = $request->hasFile('videos') ? $request->file('videos') : [];
        $removeVideoFiles = $validated['remove_videos'] ?? [];

        if (!empty($newVideoFiles) || !empty($removeVideoFiles)) {
            $updatedVideos = $this->mediaService->updateVideos(
                $newVideoFiles,
                $removeVideoFiles,
                $project
            );
            $project->update(['videos' => $updatedVideos]);
        }

        return redirect()->route('projects.index')
            ->with('success', 'Proyecto actualizado exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Project $project): RedirectResponse
    {
        // Delete all associated media files
        $this->mediaService->deleteProjectMedia($project);

        // Delete the project (soft delete due to SoftDeletes trait)
        $project->delete();

        return redirect()->route('projects.index')
            ->with('success', 'Proyecto eliminado exitosamente.');
    }

    /**
     * Get projects for select dropdown (used in property creation)
     */
    public function getForSelect(Request $request)
    {
        $projects = Project::query()
            ->select('id', 'name', 'type')
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where('name', 'like', '%' . $request->search . '%');
            })
            ->orderBy('name')
            ->limit(20)
            ->get();

        return response()->json($projects);
    }

    /**
     * Quick create project (used in property creation modal)
     */
    public function quickStore(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:projects,name',
            'type' => 'required|in:Campestres,Urbanos,Turísticos',
            'cover_image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ]);

        $project = Project::create([
            'name' => $request->name,
            'type' => $request->type,
            'status' => 'Disponible',
            'property_count' => 0,
            'city' => $request->city ?? '',
            'state' => $request->state ?? '',
            'cover_image' => '', // Will be updated after file upload
        ]);

        // Handle cover image upload
        $coverImagePath = null;
        if ($request->hasFile('cover_image') && $request->file('cover_image')->isValid()) {
            $coverImagePath = $this->mediaService->storeCoverImage(
                $request->file('cover_image'),
                $project->id
            );
        }

        $project->update(['cover_image' => $coverImagePath]);

        return response()->json([
            'project' => $project,
            'message' => 'Proyecto creado exitosamente.'
        ]);
    }

    /**
     * Update projects sort order in bulk
     */
    public function updateOrder(Request $request)
    {
        $request->validate([
            'projects' => 'required|array',
            'projects.*.id' => 'required|exists:projects,id',
            'projects.*.sort_order' => 'required|integer|min:0'
        ]);

        foreach ($request->projects as $projectData) {
            Project::where('id', $projectData['id'])
                ->update(['sort_order' => $projectData['sort_order']]);
        }

        return back()->with('success', 'Orden de proyectos actualizado exitosamente.');
    }

    /**
     * Toggle project visibility
     */
    public function toggleVisibility(Project $project)
    {
        $project->update([
            'is_public' => !$project->is_public
        ]);

        return back()->with('success', 'Visibilidad del proyecto actualizada exitosamente.');
    }
}
