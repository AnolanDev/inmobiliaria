<?php

namespace App\Http\Controllers;

use App\Models\EmailTemplate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class EmailTemplateController extends Controller
{
    /**
     * Display a listing of email templates.
     */
    public function index(Request $request): Response
    {
        $query = EmailTemplate::with(['creator', 'updater'])
            ->latest();

        // Apply filters
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('subject', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->boolean('system_only')) {
            $query->systemTemplates();
        }

        if ($request->boolean('user_only')) {
            $query->userTemplates();
        }

        $templates = $query->paginate(15);

        return Inertia::render('EmailMarketing/Templates/Index', [
            'templates' => $templates,
            'filters' => $request->only(['search', 'category', 'status', 'system_only', 'user_only']),
            'categories' => EmailTemplate::CATEGORIES,
            'statuses' => EmailTemplate::STATUSES,
            'stats' => $this->getTemplateStats()
        ]);
    }

    /**
     * Show the form for creating a new email template.
     */
    public function create(): Response
    {
        return Inertia::render('EmailMarketing/Templates/Create', [
            'categories' => EmailTemplate::CATEGORIES,
            'statuses' => EmailTemplate::STATUSES,
            'defaultVariables' => EmailTemplate::DEFAULT_VARIABLES,
            'leadVariables' => EmailTemplate::LEAD_VARIABLES
        ]);
    }

    /**
     * Store a newly created email template.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:email_templates,name',
            'subject' => 'required|string|max:255',
            'description' => 'nullable|string',
            'html_content' => 'required|string',
            'text_content' => 'nullable|string',
            'category' => 'required|string|in:' . implode(',', array_keys(EmailTemplate::CATEGORIES)),
            'status' => 'required|string|in:' . implode(',', array_keys(EmailTemplate::STATUSES)),
            'variables' => 'nullable|array',
            'metadata' => 'nullable|array'
        ]);

        $validated['created_by'] = auth()->id();
        $validated['is_system_template'] = false;

        $template = EmailTemplate::create($validated);

        return redirect()->route('email-templates.show', $template)
            ->with('success', 'Template de email creado exitosamente.');
    }

    /**
     * Display the specified email template.
     */
    public function show(EmailTemplate $emailTemplate): Response
    {
        $emailTemplate->load(['creator', 'updater', 'emailCampaigns' => function ($query) {
            $query->latest()->take(5);
        }]);

        return Inertia::render('EmailMarketing/Templates/Show', [
            'template' => $emailTemplate,
            'canDelete' => $emailTemplate->canBeDeleted(),
            'previewUrl' => route('email-templates.preview', $emailTemplate)
        ]);
    }

    /**
     * Show the form for editing the specified email template.
     */
    public function edit(EmailTemplate $emailTemplate): Response
    {
        if ($emailTemplate->is_system_template && !auth()->user()->isSuperAdmin()) {
            abort(403, 'No puedes editar templates del sistema.');
        }

        return Inertia::render('EmailMarketing/Templates/Edit', [
            'template' => $emailTemplate,
            'categories' => EmailTemplate::CATEGORIES,
            'statuses' => EmailTemplate::STATUSES,
            'defaultVariables' => EmailTemplate::DEFAULT_VARIABLES,
            'leadVariables' => EmailTemplate::LEAD_VARIABLES
        ]);
    }

    /**
     * Update the specified email template.
     */
    public function update(Request $request, EmailTemplate $emailTemplate): RedirectResponse
    {
        if ($emailTemplate->is_system_template && !auth()->user()->isSuperAdmin()) {
            abort(403, 'No puedes editar templates del sistema.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:email_templates,name,' . $emailTemplate->id,
            'subject' => 'required|string|max:255',
            'description' => 'nullable|string',
            'html_content' => 'required|string',
            'text_content' => 'nullable|string',
            'category' => 'required|string|in:' . implode(',', array_keys(EmailTemplate::CATEGORIES)),
            'status' => 'required|string|in:' . implode(',', array_keys(EmailTemplate::STATUSES)),
            'variables' => 'nullable|array',
            'metadata' => 'nullable|array'
        ]);

        $validated['updated_by'] = auth()->id();

        $emailTemplate->update($validated);

        return redirect()->route('email-templates.show', $emailTemplate)
            ->with('success', 'Template de email actualizado exitosamente.');
    }

    /**
     * Remove the specified email template.
     */
    public function destroy(EmailTemplate $emailTemplate): RedirectResponse
    {
        if (!$emailTemplate->canBeDeleted()) {
            return redirect()->back()
                ->with('error', 'No se puede eliminar este template porque está en uso o es del sistema.');
        }

        $emailTemplate->delete();

        return redirect()->route('email-templates.index')
            ->with('success', 'Template de email eliminado exitosamente.');
    }

    /**
     * Preview the email template with sample data.
     */
    public function preview(Request $request, EmailTemplate $emailTemplate)
    {
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

        // Override with request variables if provided
        if ($request->has('variables')) {
            $sampleVariables = array_merge($sampleVariables, $request->variables);
        }

        $renderedContent = $emailTemplate->renderContent($sampleVariables);

        if ($request->wantsJson() || $request->has('json')) {
            return response()->json($renderedContent);
        }

        // Return HTML preview
        return response($renderedContent['html_content'])
            ->header('Content-Type', 'text/html');
    }

    /**
     * Duplicate an existing template.
     */
    public function duplicate(Request $request, EmailTemplate $emailTemplate): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:email_templates,name'
        ]);

        $duplicate = $emailTemplate->duplicate($request->name);

        return redirect()->route('email-templates.edit', $duplicate)
            ->with('success', 'Template duplicado exitosamente.');
    }

    /**
     * Get template statistics.
     */
    private function getTemplateStats(): array
    {
        return [
            'total' => EmailTemplate::count(),
            'active' => EmailTemplate::where('status', 'active')->count(),
            'draft' => EmailTemplate::where('status', 'draft')->count(),
            'system' => EmailTemplate::where('is_system_template', true)->count(),
            'by_category' => EmailTemplate::selectRaw('category, COUNT(*) as count')
                ->groupBy('category')
                ->pluck('count', 'category')
                ->toArray()
        ];
    }

    /**
     * Test template with real recipient data.
     */
    public function test(Request $request, EmailTemplate $emailTemplate): Response
    {
        $request->validate([
            'recipient_type' => 'required|string|in:lead,client',
            'recipient_id' => 'required|integer'
        ]);

        $recipientClass = $request->recipient_type === 'lead' ? 
            \App\Models\Lead::class : 
            \App\Models\Client::class;

        $recipient = $recipientClass::findOrFail($request->recipient_id);

        // Get variables based on recipient type
        $variables = [
            'recipient_name' => $recipient->full_name ?? $recipient->name,
            'recipient_email' => $recipient->email,
            'company_name' => config('app.name', 'InmoApp'),
            'current_date' => now()->format('d/m/Y'),
            'unsubscribe_url' => url('/unsubscribe/test-token')
        ];

        if ($recipient instanceof \App\Models\Lead) {
            $variables = array_merge($variables, [
                'lead_first_name' => $recipient->first_name,
                'lead_last_name' => $recipient->last_name,
                'lead_full_name' => $recipient->full_name,
                'lead_status' => $recipient->formatted_status,
                'lead_source' => $recipient->formatted_source,
                'lead_budget_min' => $recipient->budget_min ? '$' . number_format($recipient->budget_min) : '',
                'lead_budget_max' => $recipient->budget_max ? '$' . number_format($recipient->budget_max) : '',
                'lead_interests' => implode(', ', $recipient->interests ?? []),
                'assigned_agent_name' => $recipient->assignedAgent?->name ?? ''
            ]);
        }

        $renderedContent = $emailTemplate->renderContent($variables);

        return Inertia::render('EmailMarketing/Templates/Test', [
            'template' => $emailTemplate,
            'recipient' => $recipient,
            'renderedContent' => $renderedContent
        ]);
    }

    /**
     * Get available variables for a template category.
     */
    public function getVariables(Request $request): array
    {
        $category = $request->get('category', 'general');
        
        $variables = EmailTemplate::DEFAULT_VARIABLES;
        
        if (in_array($category, ['welcome', 'follow_up'])) {
            $variables = array_merge($variables, EmailTemplate::LEAD_VARIABLES);
        }
        
        return $variables;
    }
}