<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Lead;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ActivityController extends Controller
{

    /**
     * Display a listing of activities.
     */
    public function index(Request $request)
    {

        $query = Activity::with(['user', 'assignedUser', 'related'])
            ->orderBy('scheduled_at', 'desc')
            ->orderBy('created_at', 'desc');

        // Filtros
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('assigned_to')) {
            $query->where('assigned_to', $request->assigned_to);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('scheduled_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('scheduled_at', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('subject', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        // Vista especial para actividades del usuario
        if ($request->filled('my_activities')) {
            $query->forUser(Auth::id());
        }

        // Actividades vencidas
        if ($request->filled('overdue')) {
            $query->overdue();
        }

        // Actividades de hoy
        if ($request->filled('today')) {
            $query->dueToday();
        }

        $activities = $query->paginate(15);

        return Inertia::render('Activities/Index', [
            'activities' => $activities,
            'filters' => $request->only(['type', 'status', 'priority', 'assigned_to', 'date_from', 'date_to', 'search']),
            'types' => Activity::TYPES,
            'statuses' => Activity::STATUSES,
            'priorities' => Activity::PRIORITIES,
            'users' => User::select('id', 'name')->get(),
            'stats' => $this->getActivityStats()
        ]);
    }

    /**
     * Show the form for creating a new activity.
     */
    public function create(Request $request)
    {

        // Si viene con datos relacionados pre-seleccionados
        $relatedData = null;
        if ($request->filled('related_type') && $request->filled('related_id')) {
            $modelClass = $request->related_type;
            
            // Mapear el tipo de modelo a una instancia
            switch ($modelClass) {
                case 'App\\Models\\Lead':
                    $entity = Lead::findOrFail($request->related_id);
                    $relatedData = [
                        'type' => $modelClass,
                        'id' => $entity->id,
                        'name' => $entity->full_name
                    ];
                    break;
                // Aquí se pueden agregar otros tipos como Client, Property, etc.
            }
        }

        // Compatibilidad con el parámetro legacy lead_id
        if ($request->filled('lead_id') && !$relatedData) {
            $lead = Lead::findOrFail($request->lead_id);
            $relatedData = [
                'type' => 'App\\Models\\Lead',
                'id' => $lead->id,
                'name' => $lead->full_name
            ];
        }

        return Inertia::render('Activities/Create', [
            'types' => Activity::TYPES,
            'statuses' => Activity::STATUSES,
            'priorities' => Activity::PRIORITIES,
            'users' => User::select('id', 'name')->get(),
            'leads' => Lead::select('id', 'first_name', 'last_name')->get()->map(function ($lead) {
                return [
                    'id' => $lead->id,
                    'name' => $lead->full_name,
                    'value' => $lead->id
                ];
            }),
            'relatedData' => $relatedData
        ]);
    }

    /**
     * Store a newly created activity in storage.
     */
    public function store(Request $request)
    {

        $validated = $request->validate([
            'type' => 'required|string|in:' . implode(',', array_keys(Activity::TYPES)),
            'subject' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|string|in:' . implode(',', array_keys(Activity::STATUSES)),
            'priority' => 'required|string|in:' . implode(',', array_keys(Activity::PRIORITIES)),
            'scheduled_at' => 'nullable|date',
            'duration' => 'nullable|integer|min:1',
            'related_type' => 'nullable|string',
            'related_id' => 'nullable|integer',
            'assigned_to' => 'nullable|exists:users,id',
            'has_reminder' => 'boolean',
            'reminder_at' => 'nullable|date|after:now',
            'metadata' => 'nullable|array'
        ]);

        $validated['user_id'] = Auth::id();
        
        // Si no se asigna a nadie, se asigna al usuario actual
        if (!$validated['assigned_to']) {
            $validated['assigned_to'] = Auth::id();
        }

        $activity = Activity::create($validated);

        // Actualizar el último contacto del lead si es aplicable
        if ($activity->related_type === Lead::class) {
            $lead = Lead::find($activity->related_id);
            if ($lead) {
                $lead->update(['last_contact_at' => now()]);
            }
        }

        return redirect()->route('activities.index')
            ->with('success', 'Actividad creada exitosamente.');
    }

    /**
     * Display the specified activity.
     */
    public function show(Activity $activity)
    {

        $activity->load(['user', 'assignedUser', 'related', 'parentActivity', 'followUpActivities.user']);

        return Inertia::render('Activities/Show', [
            'activity' => $activity,
            'types' => Activity::TYPES,
            'statuses' => Activity::STATUSES,
            'priorities' => Activity::PRIORITIES
        ]);
    }

    /**
     * Show the form for editing the specified activity.
     */
    public function edit(Activity $activity)
    {

        $activity->load(['related']);

        return Inertia::render('Activities/Edit', [
            'activity' => $activity,
            'types' => Activity::TYPES,
            'statuses' => Activity::STATUSES,
            'priorities' => Activity::PRIORITIES,
            'users' => User::select('id', 'name')->get(),
            'leads' => Lead::select('id', 'first_name', 'last_name')->get()->map(function ($lead) {
                return [
                    'id' => $lead->id,
                    'name' => $lead->full_name,
                    'value' => $lead->id
                ];
            })
        ]);
    }

    /**
     * Update the specified activity in storage.
     */
    public function update(Request $request, Activity $activity)
    {

        $validated = $request->validate([
            'type' => 'required|string|in:' . implode(',', array_keys(Activity::TYPES)),
            'subject' => 'required|string|max:255',
            'description' => 'nullable|string',
            'status' => 'required|string|in:' . implode(',', array_keys(Activity::STATUSES)),
            'priority' => 'required|string|in:' . implode(',', array_keys(Activity::PRIORITIES)),
            'scheduled_at' => 'nullable|date',
            'duration' => 'nullable|integer|min:1',
            'assigned_to' => 'nullable|exists:users,id',
            'has_reminder' => 'boolean',
            'reminder_at' => 'nullable|date|after:now',
            'metadata' => 'nullable|array'
        ]);

        // Si se marca como completada, establecer fecha de finalización
        if ($validated['status'] === 'completed' && $activity->status !== 'completed') {
            $validated['completed_at'] = now();
        }

        $activity->update($validated);

        return redirect()->route('activities.show', $activity)
            ->with('success', 'Actividad actualizada exitosamente.');
    }

    /**
     * Remove the specified activity from storage.
     */
    public function destroy(Activity $activity)
    {

        $activity->delete();

        return redirect()->route('activities.index')
            ->with('success', 'Actividad eliminada exitosamente.');
    }

    /**
     * Mark activity as completed
     */
    public function markCompleted(Activity $activity)
    {

        $activity->markAsCompleted();

        return back()->with('success', 'Actividad marcada como completada.');
    }

    /**
     * Mark activity as cancelled
     */
    public function markCancelled(Activity $activity)
    {

        $activity->markAsCancelled();

        return back()->with('success', 'Actividad cancelada.');
    }

    /**
     * Create a follow-up activity
     */
    public function createFollowUp(Request $request, Activity $activity)
    {

        $validated = $request->validate([
            'type' => 'required|string|in:' . implode(',', array_keys(Activity::TYPES)),
            'subject' => 'required|string|max:255',
            'description' => 'nullable|string',
            'scheduled_at' => 'required|date|after:now',
            'priority' => 'required|string|in:' . implode(',', array_keys(Activity::PRIORITIES)),
            'assigned_to' => 'nullable|exists:users,id'
        ]);

        $validated['user_id'] = Auth::id();
        $validated['assigned_to'] = $validated['assigned_to'] ?? Auth::id();

        $followUp = $activity->createFollowUp($validated);

        return back()->with('success', 'Seguimiento programado exitosamente.');
    }

    /**
     * Get activities for a specific lead
     */
    public function getForLead(Lead $lead)
    {

        $activities = Activity::forLead($lead->id)
            ->with(['user', 'assignedUser'])
            ->orderBy('scheduled_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($activities);
    }

    /**
     * Dashboard stats
     */
    public function dashboard()
    {

        $userId = Auth::id();

        $stats = [
            'pending_today' => Activity::forUser($userId)->dueToday()->count(),
            'overdue' => Activity::forUser($userId)->overdue()->count(),
            'completed_today' => Activity::forUser($userId)->completed()->whereDate('completed_at', today())->count(),
            'total_this_week' => Activity::forUser($userId)->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()])->count()
        ];

        $upcomingActivities = Activity::forUser($userId)
            ->pending()
            ->with(['related'])
            ->orderBy('scheduled_at')
            ->limit(5)
            ->get();

        $overdueActivities = Activity::forUser($userId)
            ->overdue()
            ->with(['related'])
            ->orderBy('scheduled_at')
            ->limit(5)
            ->get();

        return Inertia::render('Activities/Dashboard', [
            'stats' => $stats,
            'upcomingActivities' => $upcomingActivities,
            'overdueActivities' => $overdueActivities
        ]);
    }

    /**
     * Get activity statistics
     */
    private function getActivityStats()
    {
        return [
            'total_pending' => Activity::pending()->count(),
            'overdue' => Activity::overdue()->count(),
            'due_today' => Activity::dueToday()->count(),
            'completed_today' => Activity::completed()->whereDate('completed_at', today())->count()
        ];
    }
}