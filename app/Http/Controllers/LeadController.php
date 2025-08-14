<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\Campaign;
use App\Models\Agent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class LeadController extends Controller
{
    /**
     * Display a listing of leads.
     */
    public function index(Request $request): Response
    {
        $query = Lead::query()
            ->with(['campaign', 'assignedAgent', 'creator'])
            ->latest();

        // Apply filters
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('first_name', 'like', '%' . $request->search . '%')
                  ->orWhere('last_name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%')
                  ->orWhere('phone', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('source')) {
            $query->where('source', $request->source);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('campaign_id')) {
            $query->where('campaign_id', $request->campaign_id);
        }

        if ($request->filled('assigned_agent_id')) {
            $query->where('assigned_agent_id', $request->assigned_agent_id);
        }

        if ($request->filled('overdue')) {
            $query->overdue();
        }

        $leads = $query->paginate(15);

        // Get filter options
        $campaigns = Campaign::select('id', 'name')->orderBy('name')->get();
        $agents = Agent::select('id', 'name')->where('is_active', true)->orderBy('name')->get();

        return Inertia::render('Marketing/Leads/Index', [
            'leads' => $leads,
            'filters' => $request->only(['search', 'status', 'source', 'priority', 'campaign_id', 'assigned_agent_id', 'overdue']),
            'statuses' => Lead::STATUSES,
            'sources' => Lead::SOURCES,
            'priorities' => Lead::PRIORITIES,
            'campaigns' => $campaigns,
            'agents' => $agents,
        ]);
    }

    /**
     * Show the form for creating a new lead.
     */
    public function create(): Response
    {
        $campaigns = Campaign::select('id', 'name')->where('status', 'active')->orderBy('name')->get();
        $agents = Agent::select('id', 'name')->where('is_active', true)->orderBy('name')->get();

        return Inertia::render('Marketing/Leads/Create', [
            'statuses' => Lead::STATUSES,
            'sources' => Lead::SOURCES,
            'priorities' => Lead::PRIORITIES,
            'campaigns' => $campaigns,
            'agents' => $agents,
        ]);
    }

    /**
     * Store a newly created lead.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:leads,email',
            'phone' => 'nullable|string|max:20',
            'status' => 'required|in:' . implode(',', array_keys(Lead::STATUSES)),
            'source' => 'required|in:' . implode(',', array_keys(Lead::SOURCES)),
            'priority' => 'required|in:' . implode(',', array_keys(Lead::PRIORITIES)),
            'notes' => 'nullable|string',
            'interests' => 'nullable|array',
            'budget_min' => 'nullable|numeric|min:0',
            'budget_max' => 'nullable|numeric|min:0|gte:budget_min',
            'preferred_location' => 'nullable|string|max:255',
            'contact_preferences' => 'nullable|array',
            'next_follow_up' => 'nullable|date|after:now',
            'campaign_id' => 'nullable|exists:campaigns,id',
            'assigned_agent_id' => 'nullable|exists:agents,id',
        ]);

        $validated['created_by'] = auth()->id();

        Lead::create($validated);

        return redirect()->route('leads.index')
            ->with('success', 'Lead creado exitosamente.');
    }

    /**
     * Display the specified lead.
     */
    public function show(Lead $lead): Response
    {
        $lead->load([
            'campaign', 
            'assignedAgent', 
            'convertedClient', 
            'creator',
            'activities' => function ($query) {
                $query->with(['user', 'assignedUser'])
                      ->orderBy('scheduled_at', 'desc')
                      ->orderBy('created_at', 'desc');
            }
        ]);

        return Inertia::render('Marketing/Leads/Show', [
            'lead' => $lead,
        ]);
    }

    /**
     * Show the form for editing the lead.
     */
    public function edit(Lead $lead): Response
    {
        $campaigns = Campaign::select('id', 'name')->where('status', 'active')->orderBy('name')->get();
        $agents = Agent::select('id', 'name')->where('is_active', true)->orderBy('name')->get();

        return Inertia::render('Marketing/Leads/Edit', [
            'lead' => $lead,
            'statuses' => Lead::STATUSES,
            'sources' => Lead::SOURCES,
            'priorities' => Lead::PRIORITIES,
            'campaigns' => $campaigns,
            'agents' => $agents,
        ]);
    }

    /**
     * Update the specified lead.
     */
    public function update(Request $request, Lead $lead): RedirectResponse
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:leads,email,' . $lead->id,
            'phone' => 'nullable|string|max:20',
            'status' => 'required|in:' . implode(',', array_keys(Lead::STATUSES)),
            'source' => 'required|in:' . implode(',', array_keys(Lead::SOURCES)),
            'priority' => 'required|in:' . implode(',', array_keys(Lead::PRIORITIES)),
            'notes' => 'nullable|string',
            'interests' => 'nullable|array',
            'budget_min' => 'nullable|numeric|min:0',
            'budget_max' => 'nullable|numeric|min:0|gte:budget_min',
            'preferred_location' => 'nullable|string|max:255',
            'contact_preferences' => 'nullable|array',
            'last_contact_date' => 'nullable|date',
            'next_follow_up' => 'nullable|date',
            'campaign_id' => 'nullable|exists:campaigns,id',
            'assigned_agent_id' => 'nullable|exists:agents,id',
        ]);

        $lead->update($validated);

        return redirect()->route('leads.index')
            ->with('success', 'Lead actualizado exitosamente.');
    }

    /**
     * Remove the specified lead.
     */
    public function destroy(Lead $lead): RedirectResponse
    {
        $lead->delete();

        return redirect()->route('leads.index')
            ->with('success', 'Lead eliminado exitosamente.');
    }

    /**
     * Assign lead to an agent.
     */
    public function assignAgent(Request $request, Lead $lead): RedirectResponse
    {
        $request->validate([
            'assigned_agent_id' => 'required|exists:agents,id',
        ]);

        $lead->update([
            'assigned_agent_id' => $request->assigned_agent_id,
            'status' => 'contacted',
        ]);

        return redirect()->back()
            ->with('success', 'Lead asignado al agente exitosamente.');
    }

    /**
     * Update lead status.
     */
    public function updateStatus(Request $request, Lead $lead): RedirectResponse
    {
        $request->validate([
            'status' => 'required|in:' . implode(',', array_keys(Lead::STATUSES)),
            'notes' => 'nullable|string',
        ]);

        $updateData = ['status' => $request->status];

        // Update last contact date when status changes to contacted or qualified
        if (in_array($request->status, ['contacted', 'qualified'])) {
            $updateData['last_contact_date'] = now();
        }

        // Add notes if provided
        if ($request->filled('notes')) {
            $currentNotes = $lead->notes ? $lead->notes . "\n\n" : '';
            $updateData['notes'] = $currentNotes . now()->format('Y-m-d H:i') . ': ' . $request->notes;
        }

        $lead->update($updateData);

        return redirect()->back()
            ->with('success', 'Estado del lead actualizado exitosamente.');
    }

    /**
     * Convert lead to client.
     */
    public function convertToClient(Lead $lead): RedirectResponse
    {
        if ($lead->status === 'converted') {
            return redirect()->back()
                ->with('error', 'Este lead ya ha sido convertido.');
        }

        try {
            $client = $lead->convertToClient();

            return redirect()->route('clients.show', $client)
                ->with('success', 'Lead convertido a cliente exitosamente.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error al convertir el lead: ' . $e->getMessage());
        }
    }

    /**
     * Schedule follow-up for lead.
     */
    public function scheduleFollowUp(Request $request, Lead $lead): RedirectResponse
    {
        $request->validate([
            'next_follow_up' => 'required|date|after:now',
            'notes' => 'nullable|string',
        ]);

        $updateData = ['next_follow_up' => $request->next_follow_up];

        if ($request->filled('notes')) {
            $currentNotes = $lead->notes ? $lead->notes . "\n\n" : '';
            $updateData['notes'] = $currentNotes . now()->format('Y-m-d H:i') . ' - Seguimiento programado: ' . $request->notes;
        }

        $lead->update($updateData);

        return redirect()->back()
            ->with('success', 'Seguimiento programado exitosamente.');
    }

    /**
     * Get leads for select dropdown.
     */
    public function getForSelect(Request $request)
    {
        $leads = Lead::query()
            ->select('id', 'first_name', 'last_name', 'email', 'status')
            ->when($request->filled('search'), function ($query) use ($request) {
                $query->where(function ($q) use ($request) {
                    $q->where('first_name', 'like', '%' . $request->search . '%')
                      ->orWhere('last_name', 'like', '%' . $request->search . '%')
                      ->orWhere('email', 'like', '%' . $request->search . '%');
                });
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->orderBy('first_name')
            ->limit(20)
            ->get()
            ->map(function ($lead) {
                return [
                    'id' => $lead->id,
                    'name' => $lead->full_name,
                    'email' => $lead->email,
                    'status' => $lead->formatted_status,
                ];
            });

        return response()->json($leads);
    }

    /**
     * Export leads to Excel.
     */
    public function export(Request $request)
    {
        // This would require implementing an Excel export class
        // For now, return a JSON response
        $leads = Lead::with(['campaign', 'assignedAgent'])
            ->when($request->filled('status'), function ($query) use ($request) {
                $query->where('status', $request->status);
            })
            ->get();

        return response()->json([
            'message' => 'Export functionality would be implemented here',
            'count' => $leads->count()
        ]);
    }

    /**
     * Get lead analytics data.
     */
    public function analytics(Request $request)
    {
        $leadsByStatus = Lead::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();

        $leadsBySource = Lead::selectRaw('source, COUNT(*) as count')
            ->groupBy('source')
            ->get();

        $leadsOverTime = Lead::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->limit(30)
            ->get();

        $conversionRate = Lead::count() > 0 
            ? round((Lead::where('status', 'converted')->count() / Lead::count()) * 100, 2)
            : 0;

        return response()->json([
            'leads_by_status' => $leadsByStatus,
            'leads_by_source' => $leadsBySource,
            'leads_over_time' => $leadsOverTime,
            'metrics' => [
                'total_leads' => Lead::count(),
                'new_leads' => Lead::where('status', 'new')->count(),
                'qualified_leads' => Lead::where('status', 'qualified')->count(),
                'converted_leads' => Lead::where('status', 'converted')->count(),
                'conversion_rate' => $conversionRate,
                'overdue_followups' => Lead::overdue()->count(),
            ]
        ]);
    }
}