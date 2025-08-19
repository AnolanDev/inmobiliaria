<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Api\PublicApiController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

/*
|--------------------------------------------------------------------------
| Public API Routes
|--------------------------------------------------------------------------
|
| These routes are accessible without authentication and are intended
| for the public Vue SPA frontend application.
|
*/

Route::prefix('public')->group(function () {
    
    // Projects
    Route::get('/projects', [PublicApiController::class, 'getProjects']);
    Route::get('/projects/{id}', [PublicApiController::class, 'getProject']);
    
    // Properties
    Route::get('/properties', [PublicApiController::class, 'getProperties']);
    Route::get('/properties/{id}', [PublicApiController::class, 'getProperty']);
    
    // Agents
    Route::get('/agents', [PublicApiController::class, 'getAgents']);
    Route::get('/agents/{id}', [PublicApiController::class, 'getAgent']);
    
    // Appointments & Contact
    Route::post('/appointments', [PublicApiController::class, 'scheduleAppointment']);
    Route::post('/contact', [PublicApiController::class, 'submitContact']);
    
    // Filter Options
    Route::get('/filter-options', [PublicApiController::class, 'getFilterOptions']);
    
    // Blogs
    Route::get('/blogs', [PublicApiController::class, 'getBlogs']);
    Route::get('/blogs/featured', [PublicApiController::class, 'getFeaturedBlogs']);
    Route::get('/blogs/categories', [PublicApiController::class, 'getBlogCategories']);
    Route::get('/blogs/tags', [PublicApiController::class, 'getBlogTags']);
    Route::get('/blogs/category/{category}', [PublicApiController::class, 'getBlogsByCategory']);
    Route::get('/blogs/tag/{tag}', [PublicApiController::class, 'getBlogsByTag']);
    Route::get('/blogs/{identifier}', [PublicApiController::class, 'getBlog']);
    Route::get('/blogs/{id}/related', [PublicApiController::class, 'getRelatedBlogs']);
    
});

/*
|--------------------------------------------------------------------------
| Authentication API Routes
|--------------------------------------------------------------------------
|
| These routes handle authentication for accessing the admin panel
| from the Vue SPA.
|
*/

Route::prefix('auth')->group(function () {
    
    Route::post('/login', function (Request $request) {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($request->only('email', 'password'))) {
            $user = Auth::user();
            $token = $user->createToken('SPA Token')->plainTextToken;
            
            return response()->json([
                'message' => 'Autenticación exitosa',
                'user' => $user,
                'token' => $token,
                'admin_url' => config('app.url') // URL del backoffice
            ]);
        }

        return response()->json([
            'message' => 'Credenciales inválidas'
        ], 401);
    });
    
    Route::middleware('auth:sanctum')->post('/logout', function (Request $request) {
        $request->user()->currentAccessToken()->delete();
        
        return response()->json([
            'message' => 'Sesión cerrada exitosamente'
        ]);
    });
    
    Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
        return response()->json($request->user());
    });
    
});