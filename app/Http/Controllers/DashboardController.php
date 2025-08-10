<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\Project;
use App\Models\Client;
use App\Models\Agent;
use App\Models\Visit;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Display the dashboard with all metrics
     */
    public function index(Request $request): Response
    {
        $period = $request->get('period', '30'); // Default to 30 days
        $startDate = $this->getStartDate($period);
        
        return Inertia::render('Dashboard', [
            'metrics' => $this->getMetrics($startDate),
            'charts' => $this->getChartData($startDate),
            'recentActivity' => $this->getRecentActivity(),
            'alerts' => $this->getAlerts(),
            'currentPeriod' => $period,
        ]);
    }

    /**
     * Get comprehensive metrics for the dashboard
     */
    private function getMetrics(Carbon $startDate): array
    {
        $previousPeriodStart = $startDate->copy()->subDays($startDate->diffInDays(now()));
        
        return [
            'properties' => $this->getPropertyMetrics($startDate, $previousPeriodStart),
            'projects' => $this->getProjectMetrics($startDate, $previousPeriodStart),
            'clients' => $this->getClientMetrics($startDate, $previousPeriodStart),
            'agents' => $this->getAgentMetrics($startDate, $previousPeriodStart),
            'visits' => $this->getVisitMetrics($startDate, $previousPeriodStart),
            'sales' => $this->getSalesMetrics($startDate, $previousPeriodStart),
        ];
    }

    /**
     * Get property-related metrics
     */
    private function getPropertyMetrics(Carbon $startDate, Carbon $previousStart): array
    {
        $current = Property::where('created_at', '>=', $startDate)->count();
        $previous = Property::whereBetween('created_at', [$previousStart, $startDate])->count();
        
        return [
            'total' => Property::count(),
            'new_this_period' => $current,
            'change_percentage' => $this->calculateChangePercentage($current, $previous),
            'by_status' => Property::select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray(),
            'by_type' => Property::select('type', DB::raw('count(*) as count'))
                ->groupBy('type')
                ->pluck('count', 'type')
                ->toArray(),
            'average_price' => Property::whereNotNull('price')->avg('price'),
            'total_value' => Property::whereNotNull('price')->sum('price'),
            'available' => Property::where('status', 'available')->count(),
            'sold' => Property::where('status', 'sold')->count(),
            'rented' => Property::where('status', 'rented')->count(),
        ];
    }

    /**
     * Get project-related metrics
     */
    private function getProjectMetrics(Carbon $startDate, Carbon $previousStart): array
    {
        $current = Project::where('created_at', '>=', $startDate)->count();
        $previous = Project::whereBetween('created_at', [$previousStart, $startDate])->count();
        
        return [
            'total' => Project::count(),
            'new_this_period' => $current,
            'change_percentage' => $this->calculateChangePercentage($current, $previous),
            'by_status' => Project::select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray(),
            'disponible' => Project::where('status', 'Disponible')->count(),
            'vendido' => Project::where('status', 'Vendido')->count(),
            'reservado' => Project::where('status', 'Reservado')->count(),
            'total_properties' => DB::table('properties')
                ->whereNotNull('project_id')
                ->count(),
        ];
    }

    /**
     * Get client-related metrics
     */
    private function getClientMetrics(Carbon $startDate, Carbon $previousStart): array
    {
        $current = Client::where('created_at', '>=', $startDate)->count();
        $previous = Client::whereBetween('created_at', [$previousStart, $startDate])->count();
        
        return [
            'total' => Client::count(),
            'new_this_period' => $current,
            'change_percentage' => $this->calculateChangePercentage($current, $previous),
            'by_status' => Client::select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray(),
            'activo' => Client::where('status', 'activo')->count(),
            'prospecto' => Client::where('status', 'prospecto')->count(),
            'inactivo' => Client::where('status', 'inactivo')->count(),
            'with_visits' => Client::whereHas('visits')->count(),
        ];
    }

    /**
     * Get agent-related metrics
     */
    private function getAgentMetrics(Carbon $startDate, Carbon $previousStart): array
    {
        return [
            'total' => Agent::count(),
            'active' => Agent::where('is_active', true)->count(),
            'inactive' => Agent::where('is_active', false)->count(),
            'top_performers' => Agent::withCount(['visits' => function($query) use ($startDate) {
                $query->where('created_at', '>=', $startDate);
            }])
            ->orderBy('visits_count', 'desc')
            ->limit(5)
            ->get(['id', 'name', 'email', 'visits_count']),
            // Skip commission calculation for now as there's no commission_rate field
            'total_commission' => 0,
        ];
    }

    /**
     * Get visit-related metrics
     */
    private function getVisitMetrics(Carbon $startDate, Carbon $previousStart): array
    {
        $current = Visit::where('created_at', '>=', $startDate)->count();
        $previous = Visit::whereBetween('created_at', [$previousStart, $startDate])->count();
        
        return [
            'total' => Visit::count(),
            'new_this_period' => $current,
            'change_percentage' => $this->calculateChangePercentage($current, $previous),
            'by_status' => Visit::select('status', DB::raw('count(*) as count'))
                ->groupBy('status')
                ->pluck('count', 'status')
                ->toArray(),
            'scheduled' => Visit::where('status', 'scheduled')->count(),
            'completed' => Visit::where('status', 'completed')->count(),
            'cancelled' => Visit::where('status', 'cancelled')->count(),
            'no_show' => Visit::where('status', 'no_show')->count(),
            'today' => Visit::whereDate('scheduled_at', today())->count(),
            'this_week' => Visit::whereBetween('scheduled_at', [
                now()->startOfWeek(),
                now()->endOfWeek()
            ])->count(),
            'overdue' => Visit::where('status', 'scheduled')
                ->where('scheduled_at', '<', now())
                ->count(),
            'conversion_rate' => $this->getConversionRate($startDate),
        ];
    }

    /**
     * Get sales-related metrics
     */
    private function getSalesMetrics(Carbon $startDate, Carbon $previousStart): array
    {
        $completedVisits = Visit::where('status', 'completed')
            ->where('outcome', 'deal_closed')
            ->where('created_at', '>=', $startDate);
        
        $previousCompletedVisits = Visit::where('status', 'completed')
            ->where('outcome', 'deal_closed')
            ->whereBetween('created_at', [$previousStart, $startDate]);
        
        $currentSales = $completedVisits->sum('offered_price') ?? 0;
        $previousSales = $previousCompletedVisits->sum('offered_price') ?? 0;
        
        return [
            'total_sales' => $currentSales,
            'sales_count' => $completedVisits->count(),
            'change_percentage' => $this->calculateChangePercentage($currentSales, $previousSales),
            'average_sale_price' => $completedVisits->avg('offered_price') ?? 0,
            'pipeline_value' => Visit::where('outcome', 'offer_made')
                ->sum('offered_price') ?? 0,
            'interested_leads' => Visit::where('outcome', 'interested')->count(),
            'follow_up_required' => Visit::where('requires_follow_up', true)->count(),
        ];
    }

    /**
     * Get chart data for visualizations
     */
    private function getChartData(Carbon $startDate): array
    {
        return [
            'visits_timeline' => $this->getVisitsTimeline($startDate),
            'properties_by_type' => $this->getPropertiesByType(),
            'client_acquisition' => $this->getClientAcquisition($startDate),
            'sales_funnel' => $this->getSalesFunnel(),
            'agent_performance' => $this->getAgentPerformance($startDate),
        ];
    }

    /**
     * Get visits timeline for chart
     */
    private function getVisitsTimeline(Carbon $startDate): array
    {
        $visits = Visit::where('created_at', '>=', $startDate)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        return [
            'labels' => $visits->pluck('date')->toArray(),
            'data' => $visits->pluck('count')->toArray(),
        ];
    }

    /**
     * Get properties distribution by type
     */
    private function getPropertiesByType(): array
    {
        $data = Property::select('type', DB::raw('count(*) as count'))
            ->groupBy('type')
            ->get();
        
        return [
            'labels' => $data->pluck('type')->toArray(),
            'data' => $data->pluck('count')->toArray(),
        ];
    }

    /**
     * Get client acquisition over time
     */
    private function getClientAcquisition(Carbon $startDate): array
    {
        $clients = Client::where('created_at', '>=', $startDate)
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
        
        return [
            'labels' => $clients->pluck('date')->toArray(),
            'data' => $clients->pluck('count')->toArray(),
        ];
    }

    /**
     * Get sales funnel data
     */
    private function getSalesFunnel(): array
    {
        return [
            'labels' => ['Visitas', 'Interesados', 'Ofertas', 'Ventas'],
            'data' => [
                Visit::count(),
                Visit::where('outcome', 'interested')->count(),
                Visit::where('outcome', 'offer_made')->count(),
                Visit::where('outcome', 'deal_closed')->count(),
            ],
        ];
    }

    /**
     * Get agent performance data
     */
    private function getAgentPerformance(Carbon $startDate): array
    {
        $agents = Agent::withCount(['visits' => function($query) use ($startDate) {
            $query->where('created_at', '>=', $startDate)
                  ->where('status', 'completed');
        }])
        ->having('visits_count', '>', 0)
        ->orderBy('visits_count', 'desc')
        ->limit(10)
        ->get(['id', 'name', 'visits_count']);
        
        return [
            'labels' => $agents->pluck('name')->toArray(),
            'data' => $agents->pluck('visits_count')->toArray(),
        ];
    }

    /**
     * Get recent activity
     */
    private function getRecentActivity(): array
    {
        $recentVisits = Visit::with(['client', 'agent', 'property', 'project'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($visit) {
                // Add computed properties for the frontend
                $visit->visit_subject = $visit->property ? $visit->property->title : 
                                       ($visit->project ? $visit->project->name : 'Sin título');
                return $visit;
            });
        
        $recentClients = Client::orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        $recentProperties = Property::with('project')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        return [
            'visits' => $recentVisits,
            'clients' => $recentClients,
            'properties' => $recentProperties,
        ];
    }

    /**
     * Get important alerts
     */
    private function getAlerts(): array
    {
        $overdueVisits = Visit::where('status', 'scheduled')
            ->where('scheduled_at', '<', now())
            ->with(['client', 'property', 'project'])
            ->get()
            ->map(function ($visit) {
                $visit->visit_subject = $visit->property ? $visit->property->title : 
                                       ($visit->project ? $visit->project->name : 'Sin título');
                return $visit;
            });

        $followUpRequired = Visit::where('requires_follow_up', true)
            ->where('follow_up_date', '<=', today())
            ->with(['client', 'property', 'project'])
            ->get()
            ->map(function ($visit) {
                $visit->visit_subject = $visit->property ? $visit->property->title : 
                                       ($visit->project ? $visit->project->name : 'Sin título');
                return $visit;
            });

        // For now, we'll skip expiring properties as the expires_at field might not exist
        $expiringSoon = collect([]);

        return [
            'overdue_visits' => $overdueVisits,
            'follow_up_required' => $followUpRequired,
            'expiring_soon' => $expiringSoon,
        ];
    }

    /**
     * Get conversion rate
     */
    private function getConversionRate(Carbon $startDate): float
    {
        $totalVisits = Visit::where('created_at', '>=', $startDate)->count();
        $closedDeals = Visit::where('created_at', '>=', $startDate)
            ->where('outcome', 'deal_closed')
            ->count();
        
        return $totalVisits > 0 ? round(($closedDeals / $totalVisits) * 100, 2) : 0;
    }

    /**
     * Calculate percentage change
     */
    private function calculateChangePercentage(float $current, float $previous): float
    {
        if ($previous == 0) {
            return $current > 0 ? 100 : 0;
        }
        
        return round((($current - $previous) / $previous) * 100, 2);
    }

    /**
     * Get start date based on period
     */
    private function getStartDate(string $period): Carbon
    {
        return match($period) {
            '7' => now()->subDays(7),
            '30' => now()->subDays(30),
            '90' => now()->subDays(90),
            '180' => now()->subDays(180),
            '365' => now()->subDays(365),
            'ytd' => now()->startOfYear(),
            'mtd' => now()->startOfMonth(),
            default => now()->subDays(30),
        };
    }
}