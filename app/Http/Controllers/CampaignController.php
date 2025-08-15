<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Lead;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class CampaignController extends Controller
{
    /**
     * Display a listing of campaigns.
     */
    public function index(Request $request): Response
    {
        $query = Campaign::query()
            ->with(['creator', 'leads'])
            ->withCount('leads')
            ->latest();

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

        $campaigns = $query->paginate(12);

        return Inertia::render('Marketing/Campaigns/Index', [
            'campaigns' => $campaigns,
            'filters' => $request->only(['search', 'type', 'status']),
            'types' => Campaign::TYPES,
            'statuses' => Campaign::STATUSES,
        ]);
    }

    /**
     * Show the form for creating a new campaign.
     */
    public function create(): Response
    {
        return Inertia::render('Marketing/Campaigns/Create', [
            'types' => Campaign::TYPES,
            'statuses' => Campaign::STATUSES,
        ]);
    }

    /**
     * Store a newly created campaign.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:' . implode(',', array_keys(Campaign::TYPES)),
            'status' => 'required|in:' . implode(',', array_keys(Campaign::STATUSES)),
            'description' => 'nullable|string',
            'budget' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'target_audience' => 'nullable|array',
            'content' => 'nullable|array',
            'settings' => 'nullable|array',
        ]);

        $validated['created_by'] = auth()->id();

        Campaign::create($validated);

        return redirect()->route('campaigns.index')
            ->with('success', 'Campaña creada exitosamente.');
    }

    /**
     * Display the specified campaign.
     */
    public function show(Campaign $campaign): Response
    {
        $campaign->load([
            'creator',
            'leads' => function ($query) {
                $query->with('assignedAgent')->latest();
            }
        ]);

        // Calculate metrics
        $metrics = [
            'total_leads' => $campaign->leads->count(),
            'new_leads' => $campaign->leads->where('status', 'new')->count(),
            'qualified_leads' => $campaign->leads->where('status', 'qualified')->count(),
            'converted_leads' => $campaign->leads->where('status', 'converted')->count(),
            'conversion_rate' => $campaign->leads->count() > 0 
                ? round(($campaign->leads->where('status', 'converted')->count() / $campaign->leads->count()) * 100, 2)
                : 0,
            'remaining_budget' => $campaign->getRemainingBudget(),
            'budget_spent_percentage' => $campaign->budget > 0 
                ? round(($campaign->spent / $campaign->budget) * 100, 2) 
                : 0,
        ];

        return Inertia::render('Marketing/Campaigns/Show', [
            'campaign' => $campaign,
            'metrics' => $metrics,
        ]);
    }

    /**
     * Show the form for editing the campaign.
     */
    public function edit(Campaign $campaign): Response
    {
        return Inertia::render('Marketing/Campaigns/Edit', [
            'campaign' => $campaign,
            'types' => Campaign::TYPES,
            'statuses' => Campaign::STATUSES,
        ]);
    }

    /**
     * Update the specified campaign.
     */
    public function update(Request $request, Campaign $campaign): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|in:' . implode(',', array_keys(Campaign::TYPES)),
            'status' => 'required|in:' . implode(',', array_keys(Campaign::STATUSES)),
            'description' => 'nullable|string',
            'budget' => 'nullable|numeric|min:0',
            'spent' => 'nullable|numeric|min:0',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'target_audience' => 'nullable|array',
            'content' => 'nullable|array',
            'settings' => 'nullable|array',
            'impressions' => 'nullable|integer|min:0',
            'clicks' => 'nullable|integer|min:0',
            'conversions' => 'nullable|integer|min:0',
        ]);

        $campaign->update($validated);

        // Recalculate conversion rate if clicks were updated
        if (isset($validated['clicks']) || isset($validated['conversions'])) {
            $campaign->calculateConversionRate();
        }

        return redirect()->route('campaigns.index')
            ->with('success', 'Campaña actualizada exitosamente.');
    }

    /**
     * Remove the specified campaign.
     */
    public function destroy(Campaign $campaign): RedirectResponse
    {
        $campaign->delete();

        return redirect()->route('campaigns.index')
            ->with('success', 'Campaña eliminada exitosamente.');
    }

    /**
     * Toggle campaign status (active/paused).
     */
    public function toggleStatus(Campaign $campaign): RedirectResponse
    {
        $newStatus = $campaign->status === 'active' ? 'paused' : 'active';
        
        $campaign->update(['status' => $newStatus]);

        $statusText = $newStatus === 'active' ? 'activada' : 'pausada';

        return redirect()->back()
            ->with('success', "Campaña {$statusText} exitosamente.");
    }

    /**
     * Show campaign analytics page.
     */
    public function analytics(Request $request, Campaign $campaign)
    {
        $leadsOverTime = $campaign->leads()
            ->selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $leadsByStatus = $campaign->leads()
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->get();

        $leadsBySource = $campaign->leads()
            ->selectRaw('source, COUNT(*) as count')
            ->groupBy('source')
            ->get();

        $data = [
            'campaign' => $campaign,
            'leads_over_time' => $leadsOverTime,
            'leads_by_status' => $leadsByStatus,
            'leads_by_source' => $leadsBySource,
            'metrics' => [
                'total_leads' => $campaign->leads->count(),
                'conversion_rate' => $campaign->conversion_rate,
                'cost_per_lead' => $campaign->leads->count() > 0 
                    ? round($campaign->spent / $campaign->leads->count(), 2) 
                    : 0,
                'roi' => $campaign->spent > 0 
                    ? round((($campaign->conversions * 1000) - $campaign->spent) / $campaign->spent * 100, 2) 
                    : 0,
            ]
        ];

        // If it's an AJAX request, return JSON
        if ($request->wantsJson() || $request->expectsJson()) {
            return response()->json($data);
        }

        // Otherwise, return Inertia page
        return Inertia::render('Marketing/Campaigns/Analytics', $data);
    }

    /**
     * Duplicate a campaign.
     */
    public function duplicate(Campaign $campaign): RedirectResponse
    {
        $newCampaign = $campaign->replicate();
        $newCampaign->name = $campaign->name . ' (Copia)';
        $newCampaign->status = 'draft';
        $newCampaign->impressions = 0;
        $newCampaign->clicks = 0;
        $newCampaign->conversions = 0;
        $newCampaign->spent = 0;
        $newCampaign->conversion_rate = 0;
        $newCampaign->created_by = auth()->id();
        $newCampaign->save();

        return redirect()->route('campaigns.edit', $newCampaign)
            ->with('success', 'Campaña duplicada exitosamente.');
    }
}