<?php

namespace App\Http\Controllers;

use App\Models\EmailSend;
use App\Models\EmailClick;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;

class EmailTrackingController extends Controller
{
    /**
     * Track email open via tracking pixel.
     */
    public function trackOpen(Request $request, string $token): Response
    {
        try {
            $emailSend = EmailSend::where('tracking_token', $token)->first();
            
            if ($emailSend && $emailSend->status === 'sent') {
                $emailSend->trackOpen();
                Log::info("Email open tracked for send ID: {$emailSend->id}");
            }
        } catch (\Exception $e) {
            Log::error("Error tracking email open for token {$token}: " . $e->getMessage());
        }

        // Return 1x1 transparent pixel
        $pixel = base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7');
        
        return response($pixel)
            ->header('Content-Type', 'image/gif')
            ->header('Content-Length', strlen($pixel))
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    /**
     * Track email click and redirect to original URL.
     */
    public function trackClick(Request $request, string $token)
    {
        $url = $request->get('url');
        
        if (!$url) {
            abort(400, 'URL parameter is required');
        }

        try {
            $emailSend = EmailSend::where('tracking_token', $token)->first();
            
            if ($emailSend && $emailSend->status === 'sent') {
                // Get click metadata
                $metadata = [
                    'link_text' => $request->get('text'),
                    'link_position' => $request->get('position'),
                    'user_agent' => $request->userAgent(),
                    'ip_address' => $request->ip(),
                    'referrer' => $request->header('referer')
                ];

                $emailSend->trackClick($url, $metadata);
                Log::info("Email click tracked for send ID: {$emailSend->id}, URL: {$url}");
            }
        } catch (\Exception $e) {
            Log::error("Error tracking email click for token {$token}: " . $e->getMessage());
        }

        // Redirect to original URL
        return redirect($url);
    }

    /**
     * Handle email unsubscribe.
     */
    public function unsubscribe(Request $request, string $token)
    {
        $emailSend = EmailSend::where('unsubscribe_token', $token)->first();
        
        if (!$emailSend) {
            abort(404, 'Invalid unsubscribe token');
        }

        // Process unsubscribe
        $emailSend->unsubscribe();
        
        // Return unsubscribe confirmation page
        return view('emails.unsubscribed', [
            'recipientEmail' => $emailSend->recipient_email,
            'companyName' => config('app.name', 'InmoApp')
        ]);
    }

    /**
     * Show unsubscribe form (for cases where we want confirmation).
     */
    public function showUnsubscribeForm(string $token)
    {
        $emailSend = EmailSend::where('unsubscribe_token', $token)->first();
        
        if (!$emailSend) {
            abort(404, 'Invalid unsubscribe token');
        }

        if ($emailSend->unsubscribed) {
            return view('emails.already-unsubscribed', [
                'recipientEmail' => $emailSend->recipient_email,
                'companyName' => config('app.name', 'InmoApp')
            ]);
        }

        return view('emails.unsubscribe-form', [
            'token' => $token,
            'recipientEmail' => $emailSend->recipient_email,
            'companyName' => config('app.name', 'InmoApp')
        ]);
    }

    /**
     * Process unsubscribe form submission.
     */
    public function processUnsubscribe(Request $request, string $token)
    {
        $emailSend = EmailSend::where('unsubscribe_token', $token)->first();
        
        if (!$emailSend) {
            abort(404, 'Invalid unsubscribe token');
        }

        if ($request->get('confirm') === 'yes') {
            $emailSend->unsubscribe();
            
            return view('emails.unsubscribed', [
                'recipientEmail' => $emailSend->recipient_email,
                'companyName' => config('app.name', 'InmoApp')
            ]);
        }

        return redirect('/')->with('message', 'Unsubscribe cancelled');
    }

    /**
     * Get email statistics for analytics.
     */
    public function getStats(Request $request)
    {
        $campaignId = $request->get('campaign_id');
        $days = $request->get('days', 30);
        
        $query = EmailSend::query();
        
        if ($campaignId) {
            $query->where('email_campaign_id', $campaignId);
        }
        
        $query->where('created_at', '>=', now()->subDays($days));
        
        $stats = [
            'total_sent' => $query->clone()->where('status', 'sent')->count(),
            'total_delivered' => $query->clone()->whereIn('status', ['sent', 'opened'])->count(),
            'total_opened' => $query->clone()->where('opened', true)->count(),
            'total_clicked' => $query->clone()->where('clicked', true)->count(),
            'total_bounced' => $query->clone()->where('status', 'bounced')->count(),
            'total_unsubscribed' => $query->clone()->where('unsubscribed', true)->count(),
            'unique_opens' => $query->clone()->where('opened', true)->count(),
            'unique_clicks' => $query->clone()->where('clicked', true)->count(),
            'total_opens' => $query->clone()->sum('open_count'),
            'total_clicks' => $query->clone()->sum('click_count')
        ];
        
        // Calculate rates
        $delivered = $stats['total_delivered'];
        if ($delivered > 0) {
            $stats['open_rate'] = round(($stats['total_opened'] / $delivered) * 100, 2);
            $stats['click_rate'] = round(($stats['total_clicked'] / $delivered) * 100, 2);
        } else {
            $stats['open_rate'] = 0;
            $stats['click_rate'] = 0;
        }
        
        $sent = $stats['total_sent'];
        if ($sent > 0) {
            $stats['bounce_rate'] = round(($stats['total_bounced'] / $sent) * 100, 2);
            $stats['delivery_rate'] = round(($delivered / $sent) * 100, 2);
        } else {
            $stats['bounce_rate'] = 0;
            $stats['delivery_rate'] = 0;
        }
        
        return response()->json($stats);
    }

    /**
     * Get click heatmap data.
     */
    public function getClickHeatmap(Request $request)
    {
        $campaignId = $request->get('campaign_id');
        $days = $request->get('days', 30);
        
        $query = EmailClick::query()
            ->join('email_sends', 'email_clicks.email_send_id', '=', 'email_sends.id')
            ->where('email_clicks.created_at', '>=', now()->subDays($days));
        
        if ($campaignId) {
            $query->where('email_sends.email_campaign_id', $campaignId);
        }
        
        $clickData = $query
            ->selectRaw('link_url, COUNT(*) as clicks, COUNT(DISTINCT email_sends.id) as unique_clicks')
            ->groupBy('link_url')
            ->orderByDesc('clicks')
            ->take(20)
            ->get();
        
        return response()->json($clickData);
    }

    /**
     * Get geographic data for opens/clicks.
     */
    public function getGeoData(Request $request)
    {
        // This would typically integrate with a GeoIP service
        // For now, return mock data structure
        return response()->json([
            'countries' => [
                ['name' => 'Mexico', 'opens' => 45, 'clicks' => 12],
                ['name' => 'United States', 'opens' => 23, 'clicks' => 8],
                ['name' => 'Spain', 'opens' => 15, 'clicks' => 4],
            ],
            'cities' => [
                ['name' => 'Ciudad de México', 'opens' => 25, 'clicks' => 7],
                ['name' => 'Guadalajara', 'opens' => 12, 'clicks' => 3],
                ['name' => 'Monterrey', 'opens' => 8, 'clicks' => 2],
            ]
        ]);
    }

    /**
     * Get device/browser statistics.
     */
    public function getDeviceStats(Request $request)
    {
        $campaignId = $request->get('campaign_id');
        $days = $request->get('days', 30);
        
        $query = EmailClick::query()
            ->join('email_sends', 'email_clicks.email_send_id', '=', 'email_sends.id')
            ->where('email_clicks.created_at', '>=', now()->subDays($days));
        
        if ($campaignId) {
            $query->where('email_sends.email_campaign_id', $campaignId);
        }
        
        // Get browser stats
        $browserStats = $query->clone()
            ->selectRaw('
                CASE 
                    WHEN user_agent LIKE "%Chrome%" THEN "Chrome"
                    WHEN user_agent LIKE "%Firefox%" THEN "Firefox" 
                    WHEN user_agent LIKE "%Safari%" THEN "Safari"
                    WHEN user_agent LIKE "%Edge%" THEN "Edge"
                    ELSE "Other"
                END as browser,
                COUNT(*) as clicks
            ')
            ->groupBy('browser')
            ->get();
        
        return response()->json([
            'browsers' => $browserStats,
            'devices' => [
                ['type' => 'Desktop', 'clicks' => 45],
                ['type' => 'Mobile', 'clicks' => 32],
                ['type' => 'Tablet', 'clicks' => 8],
            ]
        ]);
    }
}