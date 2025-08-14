<?php

namespace App\Http\Controllers;

use App\Models\EmailCampaign;
use App\Models\EmailTemplate;
use App\Models\Lead;
use App\Jobs\SendEmailCampaign;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Carbon\Carbon;

class EmailCampaignController extends Controller
{
    /**
     * Display a listing of email campaigns.
     */
    public function index(Request $request): Response
    {
        $query = EmailCampaign::with(['emailTemplate', 'creator', 'marketingCampaign'])
            ->latest();

        // Apply filters
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->boolean('ab_test_only')) {
            $query->where('is_ab_test', true);
        }

        if ($request->boolean('drip_only')) {
            $query->where('is_drip_campaign', true);
        }

        $campaigns = $query->paginate(15);

        return Inertia::render('EmailMarketing/Campaigns/Index', [
            'campaigns' => $campaigns,
            'filters' => $request->only(['search', 'type', 'status', 'ab_test_only', 'drip_only']),
            'types' => EmailCampaign::TYPES,
            'statuses' => EmailCampaign::STATUSES,
            'stats' => $this->getCampaignStats()
        ]);
    }

    /**
     * Show the form for creating a new email campaign.
     */
    public function create(): Response
    {
        return Inertia::render('EmailMarketing/Campaigns/Create', [
            'types' => EmailCampaign::TYPES,
            'statuses' => EmailCampaign::STATUSES,
            'templates' => EmailTemplate::active()->get(['id', 'name', 'subject', 'category']),
            'defaultFilters' => $this->getDefaultRecipientFilters(),
            'segmentFields' => $this->getSegmentFields(),
            'totalLeads' => Lead::where('unsubscribed', false)->count()
        ]);
    }

    /**
     * Store a newly created email campaign.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|string|in:' . implode(',', array_keys(EmailCampaign::TYPES)),
            'email_template_id' => 'required|exists:email_templates,id',
            'subject_override' => 'nullable|string|max:255',
            'segment_conditions' => 'nullable|array',
            'recipient_filters' => 'nullable|array',
            'scheduled_at' => 'nullable|date|after:now',
            'is_ab_test' => 'boolean',
            'ab_test_config' => 'nullable|array',
            'is_drip_campaign' => 'boolean',
            'drip_schedule' => 'nullable|array',
            'parent_drip_campaign_id' => 'nullable|exists:email_campaigns,id',
            'marketing_campaign_id' => 'nullable|exists:campaigns,id'
        ]);

        $validated['created_by'] = auth()->id();
        $validated['status'] = $request->has('scheduled_at') ? 'scheduled' : 'draft';

        $campaign = EmailCampaign::create($validated);

        // Calculate recipients
        $campaign->calculateRecipients();

        return redirect()->route('email-campaigns.show', $campaign)
            ->with('success', 'Campaña de email creada exitosamente.');
    }

    /**
     * Display the specified email campaign.
     */
    public function show(EmailCampaign $emailCampaign): Response
    {
        $emailCampaign->load([
            'emailTemplate', 
            'creator', 
            'marketingCampaign',
            'emailSends' => function ($query) {
                $query->latest()->take(10);
            }
        ]);

        return Inertia::render('EmailMarketing/Campaigns/Show', [
            'campaign' => $emailCampaign,
            'metrics' => $this->getCampaignMetrics($emailCampaign),
            'canSend' => $emailCampaign->can_be_sent,
            'canPause' => $emailCampaign->can_be_paused,
            'canResume' => $emailCampaign->can_be_resumed,
            'canCancel' => $emailCampaign->can_be_cancelled,
            'previewUrl' => route('email-campaigns.preview', $emailCampaign)
        ]);
    }

    /**
     * Show the form for editing the specified email campaign.
     */
    public function edit(EmailCampaign $emailCampaign): Response
    {
        if (!in_array($emailCampaign->status, ['draft', 'scheduled'])) {
            abort(403, 'No se puede editar una campaña que ya está enviándose o ha sido enviada.');
        }

        return Inertia::render('EmailMarketing/Campaigns/Edit', [
            'campaign' => $emailCampaign,
            'types' => EmailCampaign::TYPES,
            'statuses' => EmailCampaign::STATUSES,
            'templates' => EmailTemplate::active()->get(['id', 'name', 'subject', 'category']),
            'defaultFilters' => $this->getDefaultRecipientFilters(),
            'segmentFields' => $this->getSegmentFields()
        ]);
    }

    /**
     * Update the specified email campaign.
     */
    public function update(Request $request, EmailCampaign $emailCampaign): RedirectResponse
    {
        if (!in_array($emailCampaign->status, ['draft', 'scheduled'])) {
            return redirect()->back()
                ->with('error', 'No se puede editar una campaña que ya está enviándose o ha sido enviada.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|string|in:' . implode(',', array_keys(EmailCampaign::TYPES)),
            'email_template_id' => 'required|exists:email_templates,id',
            'subject_override' => 'nullable|string|max:255',
            'segment_conditions' => 'nullable|array',
            'recipient_filters' => 'nullable|array',
            'scheduled_at' => 'nullable|date|after:now',
            'is_ab_test' => 'boolean',
            'ab_test_config' => 'nullable|array',
            'is_drip_campaign' => 'boolean',
            'drip_schedule' => 'nullable|array',
            'marketing_campaign_id' => 'nullable|exists:campaigns,id'
        ]);

        $validated['updated_by'] = auth()->id();

        // Update status if scheduled date changed
        if ($request->has('scheduled_at')) {
            $validated['status'] = 'scheduled';
        } elseif ($emailCampaign->status === 'scheduled' && !$request->has('scheduled_at')) {
            $validated['status'] = 'draft';
            $validated['scheduled_at'] = null;
        }

        $emailCampaign->update($validated);

        // Recalculate recipients if filters changed
        if ($request->has(['segment_conditions', 'recipient_filters'])) {
            $emailCampaign->calculateRecipients();
        }

        return redirect()->route('email-campaigns.show', $emailCampaign)
            ->with('success', 'Campaña de email actualizada exitosamente.');
    }

    /**
     * Remove the specified email campaign.
     */
    public function destroy(EmailCampaign $emailCampaign): RedirectResponse
    {
        if (!in_array($emailCampaign->status, ['draft', 'cancelled'])) {
            return redirect()->back()
                ->with('error', 'No se puede eliminar una campaña que está activa o ha sido enviada.');
        }

        $emailCampaign->delete();

        return redirect()->route('email-campaigns.index')
            ->with('success', 'Campaña de email eliminada exitosamente.');
    }

    /**
     * Send the email campaign immediately.
     */
    public function send(EmailCampaign $emailCampaign): RedirectResponse
    {
        if (!$emailCampaign->can_be_sent) {
            return redirect()->back()
                ->with('error', 'Esta campaña no se puede enviar en su estado actual.');
        }

        // Start the campaign
        $emailCampaign->start();

        // Dispatch the job to send emails
        SendEmailCampaign::dispatch($emailCampaign);

        return redirect()->route('email-campaigns.show', $emailCampaign)
            ->with('success', 'Campaña de email iniciada. Los emails se están enviando en segundo plano.');
    }

    /**
     * Schedule the email campaign.
     */
    public function schedule(Request $request, EmailCampaign $emailCampaign): RedirectResponse
    {
        $request->validate([
            'scheduled_at' => 'required|date|after:now'
        ]);

        if (!$emailCampaign->schedule(Carbon::parse($request->scheduled_at))) {
            return redirect()->back()
                ->with('error', 'No se pudo programar la campaña.');
        }

        return redirect()->route('email-campaigns.show', $emailCampaign)
            ->with('success', 'Campaña programada exitosamente.');
    }

    /**
     * Pause the email campaign.
     */
    public function pause(EmailCampaign $emailCampaign): RedirectResponse
    {
        if (!$emailCampaign->pause()) {
            return redirect()->back()
                ->with('error', 'No se pudo pausar la campaña.');
        }

        return redirect()->route('email-campaigns.show', $emailCampaign)
            ->with('success', 'Campaña pausada exitosamente.');
    }

    /**
     * Resume the email campaign.
     */
    public function resume(EmailCampaign $emailCampaign): RedirectResponse
    {
        if (!$emailCampaign->resume()) {
            return redirect()->back()
                ->with('error', 'No se pudo reanudar la campaña.');
        }

        // Dispatch the job to continue sending emails
        SendEmailCampaign::dispatch($emailCampaign);

        return redirect()->route('email-campaigns.show', $emailCampaign)
            ->with('success', 'Campaña reanudada exitosamente.');
    }

    /**
     * Cancel the email campaign.
     */
    public function cancel(EmailCampaign $emailCampaign): RedirectResponse
    {
        if (!$emailCampaign->cancel()) {
            return redirect()->back()
                ->with('error', 'No se pudo cancelar la campaña.');
        }

        return redirect()->route('email-campaigns.show', $emailCampaign)
            ->with('success', 'Campaña cancelada exitosamente.');
    }

    /**
     * Preview the email campaign.
     */
    public function preview(Request $request, EmailCampaign $emailCampaign)
    {
        $template = $emailCampaign->emailTemplate;
        
        if (!$template) {
            abort(404, 'Template no encontrado.');
        }

        // Sample variables for preview
        $sampleVariables = [
            'recipient_name' => 'María García',
            'recipient_email' => 'maria@example.com',
            'company_name' => config('app.name', 'InmoApp'),
            'current_date' => now()->format('d/m/Y'),
            'lead_first_name' => 'María',
            'lead_last_name' => 'García',
            'lead_full_name' => 'María García',
            'lead_status' => 'Nuevo',
            'lead_source' => 'Sitio Web',
            'lead_budget_min' => '$200,000',
            'lead_budget_max' => '$300,000',
            'lead_interests' => 'Apartamento, Centro',
            'assigned_agent_name' => 'Juan Pérez',
            'unsubscribe_url' => url('/unsubscribe/sample-token')
        ];

        $renderedContent = $template->renderContent($sampleVariables);

        // Override subject if campaign has custom subject
        if ($emailCampaign->subject_override) {
            $renderedContent['subject'] = $emailCampaign->subject_override;
        }

        if ($request->wantsJson() || $request->has('json')) {
            return response()->json($renderedContent);
        }

        // Return HTML preview
        return response($renderedContent['html_content'])
            ->header('Content-Type', 'text/html');
    }

    /**
     * Duplicate an existing campaign.
     */
    public function duplicate(Request $request, EmailCampaign $emailCampaign): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255'
        ]);

        $duplicate = $emailCampaign->duplicate($request->name);

        return redirect()->route('email-campaigns.edit', $duplicate)
            ->with('success', 'Campaña duplicada exitosamente.');
    }

    /**
     * Get recipients preview for campaign.
     */
    public function recipients(Request $request, EmailCampaign $emailCampaign): Response
    {
        $query = $emailCampaign->buildRecipientsQuery();
        
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('first_name', 'like', '%' . $request->search . '%')
                  ->orWhere('last_name', 'like', '%' . $request->search . '%')
                  ->orWhere('email', 'like', '%' . $request->search . '%');
            });
        }

        $recipients = $query->paginate(20);

        return Inertia::render('EmailMarketing/Campaigns/Recipients', [
            'campaign' => $emailCampaign,
            'recipients' => $recipients,
            'total' => $emailCampaign->estimated_recipients
        ]);
    }

    /**
     * Calculate recipients for given conditions.
     */
    public function calculateRecipients(Request $request)
    {
        $segmentConditions = $request->get('segment_conditions', []);
        $recipientFilters = $request->get('recipient_filters', []);

        // Create temporary campaign to use existing logic
        $tempCampaign = new EmailCampaign([
            'segment_conditions' => $segmentConditions,
            'recipient_filters' => $recipientFilters
        ]);

        $count = $tempCampaign->buildRecipientsQuery()->count();

        return response()->json(['count' => $count]);
    }

    /**
     * Get campaign statistics.
     */
    private function getCampaignStats(): array
    {
        return [
            'total' => EmailCampaign::count(),
            'draft' => EmailCampaign::where('status', 'draft')->count(),
            'scheduled' => EmailCampaign::where('status', 'scheduled')->count(),
            'sending' => EmailCampaign::where('status', 'sending')->count(),
            'sent' => EmailCampaign::where('status', 'sent')->count(),
            'by_type' => EmailCampaign::selectRaw('type, COUNT(*) as count')
                ->groupBy('type')
                ->pluck('count', 'type')
                ->toArray()
        ];
    }

    /**
     * Get metrics for a specific campaign.
     */
    private function getCampaignMetrics(EmailCampaign $campaign): array
    {
        $sends = $campaign->emailSends();

        return [
            'total_recipients' => $campaign->actual_recipients,
            'emails_sent' => $campaign->emails_sent,
            'emails_delivered' => $campaign->emails_delivered,
            'emails_opened' => $campaign->emails_opened,
            'emails_clicked' => $campaign->emails_clicked,
            'emails_bounced' => $campaign->emails_bounced,
            'emails_unsubscribed' => $campaign->emails_unsubscribed,
            'open_rate' => $campaign->open_rate,
            'click_rate' => $campaign->click_rate,
            'bounce_rate' => $campaign->bounce_rate,
            'delivery_rate' => $campaign->emails_sent > 0 ? 
                round(($campaign->emails_delivered / $campaign->emails_sent) * 100, 2) : 0,
            'engagement_score' => $this->calculateEngagementScore($campaign),
            'hourly_stats' => $this->getHourlyStats($campaign),
            'top_clicked_links' => $this->getTopClickedLinks($campaign)
        ];
    }

    /**
     * Calculate engagement score for campaign.
     */
    private function calculateEngagementScore(EmailCampaign $campaign): int
    {
        if ($campaign->emails_delivered == 0) {
            return 0;
        }

        $openWeight = 30;
        $clickWeight = 50;
        $unsubscribeWeight = -20;

        $openScore = ($campaign->open_rate / 100) * $openWeight;
        $clickScore = ($campaign->click_rate / 100) * $clickWeight;
        $unsubscribeScore = ($campaign->emails_unsubscribed / $campaign->emails_delivered) * $unsubscribeWeight;

        return max(0, min(100, round($openScore + $clickScore + $unsubscribeScore)));
    }

    /**
     * Get hourly statistics for campaign.
     */
    private function getHourlyStats(EmailCampaign $campaign): array
    {
        if (!$campaign->started_at) {
            return [];
        }

        return $campaign->emailSends()
            ->selectRaw('HOUR(sent_at) as hour, COUNT(*) as sent, SUM(opened) as opened, SUM(clicked) as clicked')
            ->whereNotNull('sent_at')
            ->groupBy('hour')
            ->orderBy('hour')
            ->get()
            ->toArray();
    }

    /**
     * Get top clicked links for campaign.
     */
    private function getTopClickedLinks(EmailCampaign $campaign): array
    {
        return $campaign->emailSends()
            ->join('email_clicks', 'email_sends.id', '=', 'email_clicks.email_send_id')
            ->selectRaw('link_url, COUNT(*) as clicks, COUNT(DISTINCT email_sends.id) as unique_clicks')
            ->groupBy('link_url')
            ->orderByDesc('clicks')
            ->take(10)
            ->get()
            ->toArray();
    }

    /**
     * Get default recipient filters.
     */
    private function getDefaultRecipientFilters(): array
    {
        return [
            'exclude_unsubscribed' => true,
            'only_active' => true,
            'created_after' => null,
            'created_before' => null
        ];
    }

    /**
     * Get available segment fields.
     */
    private function getSegmentFields(): array
    {
        return [
            'status' => [
                'label' => 'Estado',
                'type' => 'select',
                'options' => Lead::STATUSES
            ],
            'source' => [
                'label' => 'Fuente',
                'type' => 'select',
                'options' => Lead::SOURCES
            ],
            'budget_min' => [
                'label' => 'Presupuesto mínimo',
                'type' => 'number'
            ],
            'budget_max' => [
                'label' => 'Presupuesto máximo',
                'type' => 'number'
            ],
            'created_at' => [
                'label' => 'Fecha de creación',
                'type' => 'date'
            ]
        ];
    }
}