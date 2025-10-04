<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\ImageOptimizationService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class ImageController extends Controller
{
    protected ImageOptimizationService $imageService;

    public function __construct(ImageOptimizationService $imageService)
    {
        $this->imageService = $imageService;
    }

    /**
     * Serve optimized images with caching headers
     */
    public function serve(Request $request, string $type, string $id, string $filename)
    {
        try {
            // Validate parameters
            $allowedTypes = ['projects', 'properties', 'agents', 'blogs'];
            if (!in_array($type, $allowedTypes)) {
                return $this->notFound();
            }

            // Construct file path
            $path = "{$type}/{$id}/{$filename}";
            
            // Check if file exists
            if (!Storage::disk('public')->exists($path)) {
                Log::warning("Image not found: {$path}");
                return $this->serveFallback($type, $request);
            }

            // Get file info
            $file = Storage::disk('public')->get($path);
            $mimeType = Storage::disk('public')->mimeType($path);
            $lastModified = Storage::disk('public')->lastModified($path);

            // Set caching headers
            $response = response($file, 200, [
                'Content-Type' => $mimeType,
                'Cache-Control' => 'public, max-age=31536000, immutable', // 1 year
                'Last-Modified' => gmdate('D, d M Y H:i:s', $lastModified) . ' GMT',
                'ETag' => md5($file),
                'Expires' => gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT',
            ]);

            // Handle conditional requests
            if ($this->isNotModified($request, $lastModified, md5($file))) {
                return response('', 304, [
                    'Cache-Control' => 'public, max-age=31536000, immutable',
                    'Last-Modified' => gmdate('D, d M Y H:i:s', $lastModified) . ' GMT',
                    'ETag' => md5($file),
                ]);
            }

            return $response;

        } catch (\Exception $e) {
            Log::error('Image serving failed', [
                'type' => $type,
                'id' => $id,
                'filename' => $filename,
                'error' => $e->getMessage()
            ]);
            
            return $this->serveFallback($type, $request);
        }
    }

    /**
     * Serve placeholder/fallback images
     */
    public function serveFallback(string $type, Request $request)
    {
        $width = $request->query('w', 800);
        $height = $request->query('h', 600);
        
        // Generate fallback URL
        $categories = [
            'projects' => 'project',
            'properties' => 'property',
            'agents' => 'agent',
            'blogs' => 'blog'
        ];
        
        $category = $categories[$type] ?? 'default';
        $fallbackUrl = $this->imageService->getFallbackUrl($category, $width);
        
        return redirect($fallbackUrl, 302);
    }

    /**
     * Get image information
     */
    public function info(Request $request, string $type, string $id, string $filename)
    {
        try {
            $path = "{$type}/{$id}/{$filename}";
            $info = $this->imageService->getImageInfo($path);
            
            if (!$info) {
                return response()->json([
                    'error' => 'Image not found'
                ], 404);
            }
            
            return response()->json([
                'success' => true,
                'data' => $info
            ]);
            
        } catch (\Exception $e) {
            Log::error('Image info failed', [
                'type' => $type,
                'id' => $id,
                'filename' => $filename,
                'error' => $e->getMessage()
            ]);
            
            return response()->json([
                'error' => 'Failed to get image information'
            ], 500);
        }
    }

    /**
     * Serve responsive image sizes
     */
    public function responsive(Request $request, string $type, string $id, string $baseFilename)
    {
        $size = $request->query('size', 'medium');
        $allowedSizes = ['thumbnail', 'medium', 'large', 'original'];
        
        if (!in_array($size, $allowedSizes)) {
            $size = 'medium';
        }
        
        // Modify filename to include size suffix
        $pathInfo = pathinfo($baseFilename);
        $sizeSuffix = $size === 'original' ? '' : "_{$size}";
        $filename = $pathInfo['filename'] . $sizeSuffix . '.' . $pathInfo['extension'];
        
        return $this->serve($request, $type, $id, $filename);
    }

    /**
     * Check if resource is not modified
     */
    protected function isNotModified(Request $request, int $lastModified, string $etag): bool
    {
        $ifModifiedSince = $request->header('If-Modified-Since');
        $ifNoneMatch = $request->header('If-None-Match');
        
        if ($ifModifiedSince) {
            $ifModifiedSinceTime = strtotime($ifModifiedSince);
            if ($ifModifiedSinceTime >= $lastModified) {
                return true;
            }
        }
        
        if ($ifNoneMatch && $ifNoneMatch === $etag) {
            return true;
        }
        
        return false;
    }

    /**
     * Return 404 response
     */
    protected function notFound()
    {
        return response()->json([
            'error' => 'Image not found'
        ], 404);
    }

    /**
     * Proxy external images (for development)
     */
    public function proxy(Request $request)
    {
        $url = $request->query('url');
        
        if (!$url || !filter_var($url, FILTER_VALIDATE_URL)) {
            return response()->json(['error' => 'Invalid URL'], 400);
        }
        
        // Security: Only allow specific domains
        $allowedDomains = [
            'images.unsplash.com',
            'ui-avatars.com'
        ];
        
        $domain = parse_url($url, PHP_URL_HOST);
        if (!in_array($domain, $allowedDomains)) {
            return response()->json(['error' => 'Domain not allowed'], 403);
        }
        
        try {
            // Cache the proxied image
            $cacheKey = 'proxy_image_' . md5($url);
            $cached = Cache::get($cacheKey);
            
            if ($cached) {
                return response($cached['content'], 200, [
                    'Content-Type' => $cached['content_type'],
                    'Cache-Control' => 'public, max-age=3600'
                ]);
            }
            
            // Fetch the image
            $context = stream_context_create([
                'http' => [
                    'timeout' => 10,
                    'user_agent' => 'Mozilla/5.0 (compatible; TierraSonada/1.0)'
                ]
            ]);
            
            $content = file_get_contents($url, false, $context);
            
            if ($content === false) {
                return response()->json(['error' => 'Failed to fetch image'], 500);
            }
            
            // Get content type from headers
            $contentType = 'image/jpeg';
            foreach ($http_response_header as $header) {
                if (stripos($header, 'content-type:') === 0) {
                    $contentType = trim(substr($header, 13));
                    break;
                }
            }
            
            // Cache for 1 hour
            Cache::put($cacheKey, [
                'content' => $content,
                'content_type' => $contentType
            ], 3600);
            
            return response($content, 200, [
                'Content-Type' => $contentType,
                'Cache-Control' => 'public, max-age=3600'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Image proxy failed', [
                'url' => $url,
                'error' => $e->getMessage()
            ]);
            
            return response()->json(['error' => 'Proxy failed'], 500);
        }
    }
}