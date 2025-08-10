<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreVisitRequest;
use App\Http\Requests\UpdateVisitRequest;
use App\Models\Visit;
use App\Models\Property;
use App\Models\Project;
use App\Models\Client;
use App\Models\Agent;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class VisitController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): Response
    {
        $query = Visit::query()
            ->with([
                'property:id,title,address', 
                'project:id,name,description',
                'client:id,name,email,phone', 
                'agent:id,name,email'
            ])
            ->orderBy('scheduled_at', 'desc');

        // Apply filters
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('client', function ($clientQuery) use ($search) {
                    $clientQuery->where('name', 'like', "%{$search}%")
                               ->orWhere('email', 'like', "%{$search}%")
                               ->orWhere('phone', 'like', "%{$search}%");
                })->orWhereHas('property', function ($propertyQuery) use ($search) {
                    $propertyQuery->where('title', 'like', "%{$search}%")
                                 ->orWhere('address', 'like', "%{$search}%");
                })->orWhereHas('project', function ($projectQuery) use ($search) {
                    $projectQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('description', 'like', "%{$search}%");
                })->orWhereHas('agent', function ($agentQuery) use ($search) {
                    $agentQuery->where('name', 'like', "%{$search}%");
                });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('agent_id')) {
            $query->where('agent_id', $request->agent_id);
        }

        if ($request->filled('property_id')) {
            $query->where('property_id', $request->property_id);
        }

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->filled('client_id')) {
            $query->where('client_id', $request->client_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('scheduled_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('scheduled_at', '<=', $request->date_to);
        }

        if ($request->filled('outcome')) {
            $query->where('outcome', $request->outcome);
        }

        if ($request->boolean('overdue')) {
            $query->overdue();
        }

        if ($request->boolean('today')) {
            $query->today();
        }

        if ($request->boolean('requires_follow_up')) {
            $query->requiresFollowUp();
        }

        $visits = $query->paginate(15)->withQueryString();

        // Get all visits for calendar view (last 3 months and next 3 months)
        $allVisits = Visit::with(['client', 'agent', 'property', 'project'])
            ->whereBetween('scheduled_at', [
                now()->subMonths(3)->startOfMonth(),
                now()->addMonths(3)->endOfMonth()
            ])
            ->orderBy('scheduled_at')
            ->get();

        // Calculate statistics from all visits (not just paginated results)
        $stats = [
            'today_scheduled' => Visit::today()->scheduled()->count(),
            'completed' => Visit::completed()->count(),
            'overdue' => Visit::overdue()->count(),
            'no_show' => Visit::where('status', 'no_show')->count(),
            'requires_follow_up' => Visit::requiresFollowUp()->count(),
        ];

        return Inertia::render('Visits/Index', [
            'visits' => $visits,
            'allVisits' => $allVisits,
            'stats' => $stats,
            'filters' => $request->only(['search', 'status', 'type', 'priority', 'agent_id', 'property_id', 'client_id', 'date_from', 'date_to', 'outcome', 'overdue', 'today', 'requires_follow_up']),
            'statuses' => [
                'scheduled' => 'Programada',
                'completed' => 'Completada',
                'cancelled' => 'Cancelada',
                'no_show' => 'No Asistió',
            ],
            'types' => [
                'showing' => 'Visita',
                'inspection' => 'Inspección',
                'evaluation' => 'Evaluación',
                'follow_up' => 'Seguimiento',
                'closing' => 'Cierre',
            ],
            'priorities' => [
                'low' => 'Baja',
                'medium' => 'Media',
                'high' => 'Alta',
                'urgent' => 'Urgente',
            ],
            'outcomes' => [
                'interested' => 'Interesado',
                'not_interested' => 'No Interesado',
                'needs_follow_up' => 'Requiere Seguimiento',
                'offer_made' => 'Oferta Realizada',
                'deal_closed' => 'Trato Cerrado',
            ],
            'agents' => Agent::select('id', 'name')->orderBy('name')->get(),
            'properties' => Property::select('id', 'title', 'address')->orderBy('title')->get(),
            'projects' => Project::select('id', 'name', 'description')->orderBy('name')->get(),
            'clients' => Client::select('id', 'name')->orderBy('name')->get(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request): Response
    {
        return Inertia::render('Visits/Create', [
            'properties' => Property::select('id', 'title', 'address', 'price')->orderBy('title')->get(),
            'projects' => Project::select('id', 'name', 'description')->orderBy('name')->get(),
            'clients' => Client::select('id', 'name', 'email', 'phone')->orderBy('name')->get(),
            'agents' => Agent::select('id', 'name', 'email')->orderBy('name')->get(),
            'preselected' => [
                'property_id' => $request->property_id,
                'project_id' => $request->project_id,
                'client_id' => $request->client_id,
                'agent_id' => $request->agent_id,
            ]
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreVisitRequest $request): RedirectResponse
    {
        $visitData = $request->validated();
        
        // Handle file uploads
        if ($request->hasFile('attachments')) {
            $attachments = [];
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('visits/attachments', 'public');
                $attachments[] = [
                    'original_name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                ];
            }
            $visitData['attachments'] = $attachments;
        }

        // Set defaults
        $visitData['estimated_duration'] = $visitData['estimated_duration'] ?? 60;
        $visitData['priority'] = $visitData['priority'] ?? 'medium';
        $visitData['type'] = $visitData['type'] ?? 'showing';
        $visitData['status'] = 'scheduled';
        $visitData['reminder_hours_before'] = $visitData['reminder_hours_before'] ?? 24;

        $visit = Visit::create($visitData);

        return redirect()->route('visits.show', $visit)->with('success', 'Visita creada exitosamente.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Visit $visit): Response
    {
        $visit->load(['property', 'project', 'client', 'agent', 'cancelledBy']);

        return Inertia::render('Visits/Show', [
            'visit' => $visit,
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Visit $visit): Response
    {
        $visit->load(['property', 'project', 'client', 'agent']);

        return Inertia::render('Visits/Edit', [
            'visit' => $visit,
            'properties' => Property::select('id', 'title', 'address', 'price')->orderBy('title')->get(),
            'projects' => Project::select('id', 'name', 'description')->orderBy('name')->get(),
            'clients' => Client::select('id', 'name', 'email', 'phone')->orderBy('name')->get(),
            'agents' => Agent::select('id', 'name', 'email')->orderBy('name')->get(),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateVisitRequest $request, Visit $visit): RedirectResponse
    {
        $visitData = $request->validated();

        // Handle file uploads
        if ($request->hasFile('attachments')) {
            $existingAttachments = $visit->attachments ?? [];
            $newAttachments = [];
            
            foreach ($request->file('attachments') as $file) {
                $path = $file->store('visits/attachments', 'public');
                $newAttachments[] = [
                    'original_name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                ];
            }
            
            $visitData['attachments'] = array_merge($existingAttachments, $newAttachments);
        }

        $visit->update($visitData);

        return redirect()->route('visits.show', $visit)->with('success', 'Visita actualizada exitosamente.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Visit $visit): RedirectResponse
    {
        // Delete associated files
        if ($visit->attachments) {
            foreach ($visit->attachments as $attachment) {
                Storage::disk('public')->delete($attachment['path']);
            }
        }

        $visit->delete();

        return redirect()->route('visits.index')->with('success', 'Visita eliminada exitosamente.');
    }

    /**
     * Mark visit as completed
     */
    public function markCompleted(Request $request, Visit $visit): RedirectResponse
    {
        $request->validate([
            'actual_duration' => 'nullable|integer|min:1',
            'outcome' => 'nullable|in:interested,not_interested,needs_follow_up,offer_made,deal_closed',
            'client_feedback' => 'nullable|string|max:1000',
            'agent_observations' => 'nullable|string|max:1000',
            'client_rating' => 'nullable|integer|min:1|max:5',
            'offered_price' => 'nullable|numeric|min:0',
        ]);

        $visit->markAsCompleted($request->actual_duration);
        
        if ($request->filled('outcome')) {
            $visit->update($request->only(['outcome', 'client_feedback', 'agent_observations', 'client_rating', 'offered_price']));
        }

        return redirect()->back()->with('success', 'Visita marcada como completada.');
    }

    /**
     * Mark visit as cancelled
     */
    public function markCancelled(Request $request, Visit $visit): RedirectResponse
    {
        $request->validate([
            'cancellation_reason' => 'required|string|max:255',
        ]);

        $visit->markAsCancelled($request->cancellation_reason, auth()->id());

        return redirect()->back()->with('success', 'Visita cancelada exitosamente.');
    }

    /**
     * Mark visit as no show
     */
    public function markNoShow(Visit $visit): RedirectResponse
    {
        $visit->markAsNoShow();

        return redirect()->back()->with('success', 'Visita marcada como no asistió.');
    }

    /**
     * Send reminder for visit
     */
    public function sendReminder(Visit $visit): RedirectResponse
    {
        $visit->sendReminder();

        return redirect()->back()->with('success', 'Recordatorio enviado exitosamente.');
    }

    /**
     * Schedule follow up
     */
    public function scheduleFollowUp(Request $request, Visit $visit): RedirectResponse
    {
        $request->validate([
            'follow_up_date' => 'required|date|after:today',
            'follow_up_notes' => 'nullable|string|max:500',
        ]);

        $visit->scheduleFollowUp(
            \Carbon\Carbon::parse($request->follow_up_date),
            $request->follow_up_notes
        );

        return redirect()->back()->with('success', 'Seguimiento programado exitosamente.');
    }
}
