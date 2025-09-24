<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreAgentRequest;
use App\Http\Requests\UpdateAgentRequest;
use App\Models\Agent;
use App\Services\AgentMediaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class AgentController extends Controller
{
    protected $agentMediaService;

    public function __construct(AgentMediaService $agentMediaService)
    {
        $this->agentMediaService = $agentMediaService;
        $this->agentMediaService->createDirectories();
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $query = Agent::query()
            ->withCount('properties')
            ->orderBy('sort_order', 'asc')
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('bio', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            if ($request->status === 'Activo') {
                $query->where('is_active', true);
            } elseif ($request->status === 'Inactivo') {
                $query->where('is_active', false);
            }
        }

        $agents = $query->paginate(12);

        return Inertia::render('Agents/Index', [
            'agents' => $agents,
            'filters' => $request->only(['search', 'type', 'status']),
            'types' => [
                'Interno' => 'Interno',
                'Externo' => 'Externo'
            ],
            'statuses' => [
                'Activo' => 'Activo',
                'Inactivo' => 'Inactivo'
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('Agents/Create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreAgentRequest $request)
    {
        try {
            DB::beginTransaction();

            $validatedData = $request->validated();

            // Handle profile picture
            $profilePicturePath = $this->agentMediaService->handleProfilePicture(
                $request->file('profile_picture')
            );

            // Handle gallery images
            $galleryPaths = [];
            if ($request->hasFile('gallery')) {
                $galleryPaths = $this->agentMediaService->handleGallery(
                    $request->file('gallery')
                );
            }

            // Handle videos
            $videoPaths = [];
            if ($request->hasFile('videos')) {
                $videoPaths = $this->agentMediaService->handleVideos(
                    $request->file('videos')
                );
            }

            // Create agent
            $agent = Agent::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'phone' => $validatedData['phone'],
                'type' => $validatedData['type'],
                'bio' => $validatedData['bio'] ?? null,
                'profile_picture' => $profilePicturePath,
                'facebook' => $validatedData['facebook'] ?? null,
                'instagram' => $validatedData['instagram'] ?? null,
                'linkedin' => $validatedData['linkedin'] ?? null,
                'gallery' => $galleryPaths,
                'videos' => $videoPaths,
                'is_active' => $validatedData['is_active'] ?? true,
            ]);

            DB::commit();

            return redirect()
                ->route('agents.show', $agent)
                ->with('success', 'Agente creado exitosamente.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()
                ->withErrors(['error' => 'Error al crear el agente: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Agent $agent): Response
    {
        $agent->load(['properties' => function ($query) {
            $query->with(['project'])
                  ->latest()
                  ->take(10);
        }]);

        return Inertia::render('Agents/Show', [
            'agent' => $agent,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Agent $agent): Response
    {
        return Inertia::render('Agents/Edit', [
            'agent' => $agent,
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAgentRequest $request, Agent $agent)
    {
        try {
            DB::beginTransaction();

            $validatedData = $request->validated();

            // Handle profile picture
            $profilePicturePath = $this->agentMediaService->handleProfilePicture(
                $request->file('profile_picture'),
                $agent->profile_picture
            );

            // Handle gallery images
            $galleryPaths = $this->agentMediaService->handleGallery(
                $request->file('gallery', []),
                $agent->gallery ?? [],
                $request->get('remove_gallery', [])
            );

            // Handle videos
            $videoPaths = $this->agentMediaService->handleVideos(
                $request->file('videos', []),
                $agent->videos ?? [],
                $request->get('remove_videos', [])
            );

            // Update agent
            $agent->update([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'phone' => $validatedData['phone'],
                'type' => $validatedData['type'],
                'bio' => $validatedData['bio'] ?? null,
                'profile_picture' => $profilePicturePath,
                'facebook' => $validatedData['facebook'] ?? null,
                'instagram' => $validatedData['instagram'] ?? null,
                'linkedin' => $validatedData['linkedin'] ?? null,
                'gallery' => $galleryPaths,
                'videos' => $videoPaths,
                'is_active' => $validatedData['is_active'] ?? true,
            ]);

            DB::commit();

            return redirect()
                ->route('agents.show', $agent)
                ->with('success', 'Agente actualizado exitosamente.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()
                ->withErrors(['error' => 'Error al actualizar el agente: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Agent $agent)
    {
        try {
            DB::beginTransaction();

            // Check if agent has active properties
            $activePropertiesCount = $agent->properties()->whereIn('status', ['available', 'pending'])->count();
            
            if ($activePropertiesCount > 0) {
                return back()->withErrors([
                    'error' => "No se puede eliminar el agente porque tiene {$activePropertiesCount} propiedades activas asignadas."
                ]);
            }

            // Delete media files
            $this->agentMediaService->deleteAgentMedia(
                $agent->profile_picture,
                $agent->gallery ?? [],
                $agent->videos ?? []
            );

            // Delete agent
            $agent->delete();

            DB::commit();

            return redirect()
                ->route('agents.index')
                ->with('success', 'Agente eliminado exitosamente.');

        } catch (\Exception $e) {
            DB::rollback();
            return back()->withErrors([
                'error' => 'Error al eliminar el agente: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Quick create agent for property forms
     */
    public function quickCreate(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:agents,email',
            'phone' => 'required|string|max:20',
            'type' => 'required|in:Interno,Externo',
        ], [
            'name.required' => 'El nombre es obligatorio.',
            'email.required' => 'El correo electrónico es obligatorio.',
            'email.email' => 'Debe proporcionar una dirección de correo válida.',
            'email.unique' => 'Este correo electrónico ya está registrado.',
            'phone.required' => 'El teléfono es obligatorio.',
            'type.required' => 'El tipo de agente es obligatorio.',
            'type.in' => 'El tipo debe ser Interno o Externo.',
        ]);

        try {
            $agent = Agent::create([
                'name' => $request->get('name'),
                'email' => $request->get('email'),
                'phone' => $request->get('phone'),
                'type' => $request->get('type'),
                'is_active' => true,
                'profile_picture' => null, // Will be updated later when they edit the agent
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Agente creado exitosamente.',
                'agent' => $agent
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al crear el agente: ' . $e->getMessage(),
                'errors' => ['general' => 'Error al crear el agente.']
            ], 422);
        }
    }

    /**
     * Toggle agent status
     */
    public function toggleStatus(Agent $agent)
    {
        try {
            $agent->update([
                'is_active' => !$agent->is_active
            ]);

            $status = $agent->is_active ? 'activado' : 'desactivado';

            return back()->with('success', "Agente {$status} exitosamente.");

        } catch (\Exception $e) {
            return back()->withErrors([
                'error' => 'Error al cambiar el estado del agente: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Update agents sort order in bulk
     */
    public function updateOrder(Request $request)
    {
        $request->validate([
            'agents' => 'required|array',
            'agents.*.id' => 'required|exists:agents,id',
            'agents.*.sort_order' => 'required|integer|min:0'
        ]);

        foreach ($request->agents as $agentData) {
            Agent::where('id', $agentData['id'])
                ->update(['sort_order' => $agentData['sort_order']]);
        }

        return back()->with('success', 'Orden de agentes actualizado exitosamente.');
    }

    /**
     * Toggle agent visibility
     */
    public function toggleVisibility(Agent $agent)
    {
        $agent->update([
            'is_public' => !$agent->is_public
        ]);

        return back()->with('success', 'Visibilidad del agente actualizada exitosamente.');
    }
}
