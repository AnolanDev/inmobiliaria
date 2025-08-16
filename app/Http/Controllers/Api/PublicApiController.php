<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Property;
use App\Models\Agent;
use App\Models\Visit;
use App\Models\Lead;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class PublicApiController extends Controller
{
    /**
     * Get all public projects with their properties
     */
    public function getProjects(Request $request): JsonResponse
    {
        $query = Project::with(['properties' => function ($query) {
            $query->where('status', 'available')
                  ->orWhere('status', 'reserved');
        }])
        ->where('status', 'Disponible')
        ->where('is_public', true);

        // Apply filters
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('location')) {
            $query->whereHas('properties', function ($q) use ($request) {
                $q->where('city', 'like', '%' . $request->location . '%');
            });
        }

        if ($request->filled('min_price')) {
            $query->whereHas('properties', function ($q) use ($request) {
                $q->where('price', '>=', $request->min_price);
            });
        }

        if ($request->filled('max_price')) {
            $query->whereHas('properties', function ($q) use ($request) {
                $q->where('price', '<=', $request->max_price);
            });
        }

        $projects = $query->paginate(12);

        return response()->json([
            'data' => $projects->items(),
            'meta' => [
                'current_page' => $projects->currentPage(),
                'last_page' => $projects->lastPage(),
                'per_page' => $projects->perPage(),
                'total' => $projects->total(),
            ]
        ]);
    }

    /**
     * Get project details by ID
     */
    public function getProject($id): JsonResponse
    {
        $project = Project::with([
            'properties' => function ($query) {
                $query->where('status', 'available')
                      ->orWhere('status', 'reserved');
            },
            'properties.images',
            'agent'
        ])
        ->where('status', 'Disponible')
        ->where('is_public', true)
        ->find($id);

        if (!$project) {
            return response()->json(['message' => 'Proyecto no encontrado'], 404);
        }

        return response()->json($project);
    }

    /**
     * Get all public agents
     */
    public function getAgents(): JsonResponse
    {
        $agents = Agent::where('is_active', true)
            ->where('is_public', true)
            ->select([
                'id',
                'name', 
                'email',
                'phone',
                'type',
                'bio',
                'profile_picture',
                'facebook',
                'instagram',
                'linkedin'
            ])
            ->withCount(['properties' => function ($query) {
                $query->where('status', 'available');
            }])
            ->get();

        return response()->json($agents);
    }

    /**
     * Get agent details by ID
     */
    public function getAgent($id): JsonResponse
    {
        $agent = Agent::with([
            'properties' => function ($query) {
                $query->where('status', 'available')
                      ->orWhere('status', 'reserved')
                      ->with('project');
            }
        ])
        ->where('is_active', true)
        ->where('is_public', true)
        ->find($id);

        if (!$agent) {
            return response()->json(['message' => 'Agente no encontrado'], 404);
        }

        return response()->json($agent);
    }

    /**
     * Get available properties with filters
     */
    public function getProperties(Request $request): JsonResponse
    {
        $query = Property::with(['project', 'agent', 'images'])
            ->whereIn('status', ['available', 'reserved'])
            ->whereHas('project', function ($q) {
                $q->where('status', 'Disponible')->where('is_public', true);
            });

        // Apply filters
        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        if ($request->filled('bedrooms')) {
            $query->where('bedrooms', $request->bedrooms);
        }

        if ($request->filled('bathrooms')) {
            $query->where('bathrooms', $request->bathrooms);
        }

        if ($request->filled('min_area')) {
            $query->where('area', '>=', $request->min_area);
        }

        if ($request->filled('max_area')) {
            $query->where('area', '<=', $request->max_area);
        }

        $properties = $query->paginate(12);

        return response()->json([
            'data' => $properties->items(),
            'meta' => [
                'current_page' => $properties->currentPage(),
                'last_page' => $properties->lastPage(),
                'per_page' => $properties->perPage(),
                'total' => $properties->total(),
            ]
        ]);
    }

    /**
     * Get property details by ID
     */
    public function getProperty($id): JsonResponse
    {
        $property = Property::with([
            'project',
            'agent',
            'images',
            'visits' => function ($query) {
                $query->where('scheduled_at', '>', Carbon::now())
                      ->where('status', 'confirmed');
            }
        ])
        ->whereIn('status', ['available', 'reserved'])
        ->whereHas('project', function ($q) {
            $q->where('status', 'Disponible')->where('is_public', true);
        })
        ->find($id);

        if (!$property) {
            return response()->json(['message' => 'Propiedad no encontrada'], 404);
        }

        return response()->json($property);
    }

    /**
     * Schedule a visit appointment
     */
    public function scheduleAppointment(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'property_id' => 'required|exists:properties,id',
            'agent_id' => 'required|exists:agents,id',
            'client_name' => 'required|string|max:255',
            'client_email' => 'required|email|max:255',
            'client_phone' => 'required|string|max:20',
            'preferred_date' => 'required|date|after:now',
            'preferred_time' => 'required|date_format:H:i',
            'message' => 'nullable|string|max:500',
            'visit_type' => 'in:presencial,virtual',
        ]);

        // Check if property is available
        $property = Property::whereIn('status', ['available', 'reserved'])
            ->whereHas('project', function ($q) {
                $q->where('status', 'Disponible')->where('is_public', true);
            })
            ->find($validated['property_id']);

        if (!$property) {
            return response()->json(['message' => 'Propiedad no disponible'], 400);
        }

        // Check if agent is available
        $agent = Agent::where('is_active', true)
            ->where('is_public', true)
            ->find($validated['agent_id']);

        if (!$agent) {
            return response()->json(['message' => 'Agente no disponible'], 400);
        }

        // Create or find lead
        $lead = Lead::firstOrCreate(
            ['email' => $validated['client_email']],
            [
                'first_name' => explode(' ', $validated['client_name'])[0],
                'last_name' => substr($validated['client_name'], strlen(explode(' ', $validated['client_name'])[0]) + 1),
                'phone' => $validated['client_phone'],
                'source' => 'website',
                'status' => 'new',
                'notes' => $validated['message'] ?? 'Lead generado desde página web pública',
                'assigned_agent_id' => $validated['agent_id'],
                'created_by' => $validated['agent_id'], // Temporarily assign to agent
            ]
        );

        // Create visit appointment
        $scheduledAt = Carbon::createFromFormat(
            'Y-m-d H:i',
            $validated['preferred_date'] . ' ' . $validated['preferred_time']
        );

        $visit = Visit::create([
            'property_id' => $validated['property_id'],
            'agent_id' => $validated['agent_id'],
            'client_id' => $lead->id, // Using lead as client
            'scheduled_at' => $scheduledAt,
            'type' => $validated['visit_type'] ?? 'presencial',
            'status' => 'scheduled',
            'notes' => $validated['message'],
            'created_by' => $validated['agent_id'],
        ]);

        return response()->json([
            'message' => 'Cita agendada exitosamente',
            'appointment' => [
                'id' => $visit->id,
                'scheduled_at' => $visit->scheduled_at->format('Y-m-d H:i'),
                'property' => $property->title,
                'agent' => $agent->name,
                'type' => $visit->type,
                'status' => $visit->status,
            ]
        ], 201);
    }

    /**
     * Submit contact form / lead
     */
    public function submitContact(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'message' => 'required|string|max:1000',
            'property_id' => 'nullable|exists:properties,id',
            'project_id' => 'nullable|exists:projects,id',
            'preferred_contact' => 'in:email,phone,whatsapp',
            'budget_range' => 'nullable|string',
        ]);

        // Create lead
        $lead = Lead::create([
            'first_name' => explode(' ', $validated['name'])[0],
            'last_name' => substr($validated['name'], strlen(explode(' ', $validated['name'])[0]) + 1),
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'source' => 'website',
            'status' => 'new',
            'notes' => $validated['message'],
            'interests' => $validated['property_id'] ? [$validated['property_id']] : [],
            'budget_min' => $this->extractBudgetMin($validated['budget_range'] ?? ''),
            'budget_max' => $this->extractBudgetMax($validated['budget_range'] ?? ''),
            'contact_preferences' => [$validated['preferred_contact'] ?? 'email'],
        ]);

        return response()->json([
            'message' => 'Contacto recibido exitosamente. Nos pondremos en contacto contigo pronto.',
            'lead_id' => $lead->id
        ], 201);
    }

    /**
     * Get filter options for properties
     */
    public function getFilterOptions(): JsonResponse
    {
        return response()->json([
            'property_types' => Property::distinct()->pluck('type')->filter()->values(),
            'property_categories' => Property::distinct()->pluck('category')->filter()->values(),
            'project_types' => Project::distinct()->pluck('type')->filter()->values(),
            'cities' => Property::distinct()->pluck('city')->filter()->values(),
            'bedrooms' => Property::distinct()->pluck('bedrooms')->filter()->sort()->values(),
            'bathrooms' => Property::distinct()->pluck('bathrooms')->filter()->sort()->values(),
            'price_ranges' => [
                ['label' => 'Hasta $200M', 'min' => 0, 'max' => 200000000],
                ['label' => '$200M - $500M', 'min' => 200000000, 'max' => 500000000],
                ['label' => '$500M - $1B', 'min' => 500000000, 'max' => 1000000000],
                ['label' => 'Más de $1B', 'min' => 1000000000, 'max' => null],
            ]
        ]);
    }

    /**
     * Extract budget minimum from range string
     */
    private function extractBudgetMin(string $range): ?int
    {
        $patterns = [
            '/(\d+)M?\s*-/' => 1000000,
            '/hasta\s*(\d+)M?/i' => 1000000,
            '/menos\s*de\s*(\d+)M?/i' => 1000000,
        ];

        foreach ($patterns as $pattern => $multiplier) {
            if (preg_match($pattern, $range, $matches)) {
                return (int)$matches[1] * $multiplier;
            }
        }

        return null;
    }

    /**
     * Extract budget maximum from range string
     */
    private function extractBudgetMax(string $range): ?int
    {
        $patterns = [
            '/-\s*(\d+)M?/' => 1000000,
            '/hasta\s*(\d+)M?/i' => 1000000,
            '/menos\s*de\s*(\d+)M?/i' => 1000000,
            '/más\s*de\s*(\d+)M?/i' => null, // No max for "more than"
        ];

        foreach ($patterns as $pattern => $multiplier) {
            if (preg_match($pattern, $range, $matches)) {
                return $multiplier ? (int)$matches[1] * $multiplier : null;
            }
        }

        return null;
    }
}