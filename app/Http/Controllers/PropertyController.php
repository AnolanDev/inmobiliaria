<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePropertyRequest;
use App\Http\Requests\UpdatePropertyRequest;
use App\Models\Agent;
use App\Models\Property;
use App\Models\Project;
use App\Services\PropertyMediaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class PropertyController extends Controller
{
    protected PropertyMediaService $mediaService;

    public function __construct(PropertyMediaService $mediaService)
    {
        $this->mediaService = $mediaService;
    }

    public function index(Request $request): Response
    {
        $query = Property::with(['agent', 'project'])
            ->orderBy('sort_order', 'asc')
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('address', 'like', '%' . $request->search . '%')
                  ->orWhere('city', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
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

        $properties = $query->paginate(12);

        return Inertia::render('Properties/Index', [
            'properties' => $properties,
            'projects' => Project::select('id', 'name')->get(),
            'filters' => $request->only(['search', 'project_id', 'type', 'category', 'status', 'location', 'state', 'city']),
            'types' => [
                'sale' => 'Venta',
                'rent' => 'Renta',
            ],
            'categories' => [
                'house' => 'Casa',
                'apartment' => 'Apartamento',
                'land' => 'Lote',
                'office' => 'Oficina',
                'commercial' => 'Local Comercial',
            ],
            'statuses' => [
                'available' => 'Disponible',
                'pending' => 'Pendiente',
                'sold' => 'Vendido',
                'rented' => 'Rentado',
            ],
            'states' => Property::distinct()->whereNotNull('state')->pluck('state')->sort()->values(),
            'cities' => Property::distinct()->whereNotNull('city')->pluck('city')->sort()->values(),
        ]);
    }

    public function create(Request $request): Response
    {
        $preselectedProjectId = $request->get('project_id');
        
        return Inertia::render('Properties/Create', [
            'agents' => Agent::where('is_active', true)->get(['id', 'name']),
            'projects' => Project::select('id', 'name', 'type')->orderBy('name')->get(),
            'preselectedProject' => $preselectedProjectId 
                ? Project::find($preselectedProjectId) 
                : null,
            'types' => Project::TYPES,
            'statuses' => Project::STATUSES,
        ]);
    }

    public function store(StorePropertyRequest $request): RedirectResponse
    {
        $data = $request->validated();

        // Create the property first
        $property = Property::create([
            'title' => $data['title'],
            'description' => $data['description'],
            'price' => $data['price'],
            'type' => $data['type'],
            'category' => $data['category'],
            'address' => $data['address'],
            'city' => $data['city'],
            'state' => $data['state'],
            'zip_code' => $data['zip_code'] ?? null,
            'bedrooms' => $data['bedrooms'] ?? 0,
            'bathrooms' => $data['bathrooms'] ?? 0,
            'area' => $data['area'],
            'images' => $data['images'] ?? [],
            'features' => $data['features'] ?? [],
            'agent_id' => $data['agent_id'],
            'project_id' => $data['project_id'] ?? null,
            'status' => $data['status'] ?? 'available',
        ]);

        // Handle media files
        if ($request->hasFile('cover_image')) {
            $coverImagePath = $this->mediaService->storeCoverImage(
                $request->file('cover_image'),
                $property->id
            );
            $property->update(['cover_image' => $coverImagePath]);
        }

        if ($request->hasFile('gallery')) {
            $galleryPaths = $this->mediaService->storeGalleryImages(
                $request->file('gallery'),
                $property->id
            );
            $property->update(['gallery' => $galleryPaths]);
        }

        if ($request->hasFile('videos')) {
            $videoPaths = $this->mediaService->storeVideos(
                $request->file('videos'),
                $property->id
            );
            $property->update(['videos' => $videoPaths]);
        }

        return redirect()->route('properties.index')
            ->with('success', 'Propiedad creada exitosamente.');
    }

    public function show(Property $property): Response
    {
        return Inertia::render('Properties/Show', [
            'property' => $property->load(['agent', 'project', 'visits.client']),
        ]);
    }

    public function edit(Property $property): Response
    {
        return Inertia::render('Properties/Edit', [
            'property' => $property->load('project'),
            'agents' => Agent::where('is_active', true)->get(['id', 'name']),
            'projects' => Project::select('id', 'name', 'type')->orderBy('name')->get(),
            'types' => Project::TYPES,
            'statuses' => Project::STATUSES,
        ]);
    }

    public function update(UpdatePropertyRequest $request, Property $property): RedirectResponse
    {
        $data = $request->validated();

        // Update basic property data
        $property->update([
            'title' => $data['title'],
            'description' => $data['description'],
            'price' => $data['price'],
            'type' => $data['type'],
            'category' => $data['category'],
            'address' => $data['address'],
            'city' => $data['city'],
            'state' => $data['state'],
            'zip_code' => $data['zip_code'] ?? null,
            'bedrooms' => $data['bedrooms'] ?? 0,
            'bathrooms' => $data['bathrooms'] ?? 0,
            'area' => $data['area'],
            'images' => $data['images'] ?? [],
            'features' => $data['features'] ?? [],
            'agent_id' => $data['agent_id'],
            'project_id' => $data['project_id'] ?? null,
            'status' => $data['status'] ?? 'available',
        ]);

        // Handle cover image update
        if ($request->hasFile('cover_image')) {
            $coverImagePath = $this->mediaService->updateCoverImage(
                $request->file('cover_image'),
                $property
            );
            $property->update(['cover_image' => $coverImagePath]);
        }

        // Handle gallery updates
        $newGalleryFiles = $request->file('gallery', []);
        $removeGalleryFiles = $data['remove_gallery'] ?? [];

        if (!empty($newGalleryFiles) || !empty($removeGalleryFiles)) {
            $updatedGallery = $this->mediaService->updateGalleryImages(
                $newGalleryFiles,
                $removeGalleryFiles,
                $property
            );
            $property->update(['gallery' => $updatedGallery]);
        }

        // Handle video updates
        $newVideoFiles = $request->file('videos', []);
        $removeVideoFiles = $data['remove_videos'] ?? [];

        if (!empty($newVideoFiles) || !empty($removeVideoFiles)) {
            $updatedVideos = $this->mediaService->updateVideos(
                $newVideoFiles,
                $removeVideoFiles,
                $property
            );
            $property->update(['videos' => $updatedVideos]);
        }

        return redirect()->route('properties.index')
            ->with('success', 'Propiedad actualizada exitosamente.');
    }

    public function destroy(Property $property): RedirectResponse
    {
        // Delete associated media files
        $this->mediaService->deletePropertyMedia($property);
        
        // Delete the property
        $property->delete();

        return redirect()->route('properties.index')
            ->with('success', 'Propiedad eliminada exitosamente.');
    }

    /**
     * Toggle property status between available and sold
     */
    public function toggleStatus(Property $property): RedirectResponse
    {
        $newStatus = $property->status === 'available' ? 'sold' : 'available';
        
        $property->update(['status' => $newStatus]);

        return redirect()->back()
            ->with('success', 'Estado de la propiedad actualizado exitosamente.');
    }

    /**
     * API endpoint for quick project creation in property forms
     */
    public function quickCreateProject(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:' . implode(',', array_keys(Project::TYPES)),
            'status' => 'required|in:' . implode(',', array_keys(Project::STATUSES)),
            'description' => 'nullable|string',
        ]);

        $project = Project::create($request->only(['name', 'type', 'status', 'description']));

        return response()->json([
            'project' => $project->only(['id', 'name', 'type', 'status']),
            'message' => 'Proyecto creado exitosamente'
        ]);
    }

    /**
     * Update properties sort order in bulk
     */
    public function updateOrder(Request $request)
    {
        $request->validate([
            'properties' => 'required|array',
            'properties.*.id' => 'required|exists:properties,id',
            'properties.*.sort_order' => 'required|integer|min:0'
        ]);

        foreach ($request->properties as $propertyData) {
            Property::where('id', $propertyData['id'])
                ->update(['sort_order' => $propertyData['sort_order']]);
        }

        return back()->with('success', 'Orden de propiedades actualizado exitosamente.');
    }

    /**
     * Toggle property visibility
     */
    public function toggleVisibility(Property $property)
    {
        $property->update([
            'is_public' => !$property->is_public
        ]);

        return back()->with('success', 'Visibilidad de la propiedad actualizada exitosamente.');
    }
}
