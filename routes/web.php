<?php

use App\Http\Controllers\AgentController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\VisitController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::resource('projects', ProjectController::class);
    Route::get('/projects-select', [ProjectController::class, 'getForSelect'])->name('projects.select');
    Route::post('/projects-quick', [ProjectController::class, 'quickStore'])->name('projects.quick');
    
    Route::resource('properties', PropertyController::class);
    Route::post('/properties/projects/quick', [PropertyController::class, 'quickCreateProject'])->name('properties.projects.quick');
    
    Route::resource('agents', AgentController::class);
    Route::post('/agents/quick', [AgentController::class, 'quickCreate'])->name('agents.quick');
    Route::patch('/agents/{agent}/toggle-status', [AgentController::class, 'toggleStatus'])->name('agents.toggle-status');
    Route::resource('clients', ClientController::class);
    Route::resource('visits', VisitController::class);
});

require __DIR__.'/auth.php';
