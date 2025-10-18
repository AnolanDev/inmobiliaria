<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DebugRequestMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Only log for project store requests
        if ($request->is('projects') && $request->isMethod('POST')) {
            \Log::info('Debug Request Data', [
                'method' => $request->method(),
                'url' => $request->fullUrl(),
                'headers' => $request->headers->all(),
                'all_data' => $request->all(),
                'files' => $request->allFiles(),
                'content_type' => $request->header('Content-Type'),
                'has_csrf' => $request->hasSession() ? $request->session()->token() : 'no session',
                'user' => $request->user() ? $request->user()->email : 'no user'
            ]);
        }

        try {
            $response = $next($request);
            
            // Log response status for project store
            if ($request->is('projects') && $request->isMethod('POST')) {
                \Log::info('Response Status', [
                    'status' => $response->getStatusCode(),
                    'headers' => $response->headers->all()
                ]);
            }
            
            return $response;
        } catch (\Exception $e) {
            if ($request->is('projects') && $request->isMethod('POST')) {
                \Log::error('Exception in request', [
                    'message' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'trace' => $e->getTraceAsString()
                ]);
            }
            throw $e;
        }
    }
}