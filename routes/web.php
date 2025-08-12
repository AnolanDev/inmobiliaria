<?php

use App\Http\Controllers\AgentController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
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

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified', 'permission:dashboard:view'])
    ->name('dashboard');


Route::middleware('auth')->group(function () {
    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // User management routes
    Route::get('/users', [UserController::class, 'index'])->name('users.index')->middleware('permission:users:view');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create')->middleware('permission:users:create');
    Route::get('/users/export', [UserController::class, 'export'])->name('users.export')->middleware('permission:users:view');
    Route::post('/users', [UserController::class, 'store'])->name('users.store')->middleware('permission:users:create');
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show')->middleware('permission:users:view');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit')->middleware('permission:users:edit');
    Route::patch('/users/{user}', [UserController::class, 'update'])->name('users.update')->middleware('permission:users:edit');
    Route::patch('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status')->middleware('permission:users:edit');
    Route::post('/users/{user}/assign-roles', [UserController::class, 'assignRoles'])->name('users.assign-roles')->middleware('permission:users:edit');
    Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy')->middleware('permission:users:delete');

    // Role management routes
    Route::get('/roles', [RoleController::class, 'index'])->name('roles.index')->middleware('permission:roles:view');
    Route::get('/roles/create', [RoleController::class, 'create'])->name('roles.create')->middleware('permission:roles:create');
    Route::get('/roles/permissions', [RoleController::class, 'permissions'])->name('roles.permissions')->middleware('permission:roles:view');
    Route::get('/roles/export', [RoleController::class, 'export'])->name('roles.export')->middleware('permission:roles:view');
    Route::post('/roles', [RoleController::class, 'store'])->name('roles.store')->middleware('permission:roles:create');
    Route::get('/roles/{role}', [RoleController::class, 'show'])->name('roles.show')->middleware('permission:roles:view');
    Route::get('/roles/{role}/edit', [RoleController::class, 'edit'])->name('roles.edit')->middleware('permission:roles:edit');
    Route::patch('/roles/{role}', [RoleController::class, 'update'])->name('roles.update')->middleware('permission:roles:edit');
    Route::patch('/roles/{role}/toggle-status', [RoleController::class, 'toggleStatus'])->name('roles.toggle-status')->middleware('permission:roles:edit');
    Route::post('/roles/{role}/assign-permissions', [RoleController::class, 'assignPermissions'])->name('roles.assign-permissions')->middleware('permission:roles:edit');
    Route::post('/roles/{role}/duplicate', [RoleController::class, 'duplicate'])->name('roles.duplicate')->middleware('permission:roles:create');
    Route::delete('/roles/{role}', [RoleController::class, 'destroy'])->name('roles.destroy')->middleware('permission:roles:delete');

    // User profile management
    Route::get('/my-profile', [UserController::class, 'profile'])->name('user.profile');
    Route::patch('/my-profile', [UserController::class, 'updateProfile'])->name('user.profile.update');

    // Project management routes
    Route::middleware('permission:projects:view')->group(function () {
        Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
        Route::get('/projects-select', [ProjectController::class, 'getForSelect'])->name('projects.select');
        Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('projects.show');
        
        Route::middleware('permission:projects:create')->group(function () {
            Route::get('/projects/create', [ProjectController::class, 'create'])->name('projects.create');
            Route::post('/projects', [ProjectController::class, 'store'])->name('projects.store');
            Route::post('/projects-quick', [ProjectController::class, 'quickStore'])->name('projects.quick');
        });
        
        Route::middleware('permission:projects:edit')->group(function () {
            Route::get('/projects/{project}/edit', [ProjectController::class, 'edit'])->name('projects.edit');
            Route::patch('/projects/{project}', [ProjectController::class, 'update'])->name('projects.update');
        });
        
        Route::middleware('permission:projects:delete')->group(function () {
            Route::delete('/projects/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy');
        });
    });

    // Property management routes
    Route::middleware('permission:properties:view')->group(function () {
        Route::get('/properties', [PropertyController::class, 'index'])->name('properties.index');
        Route::get('/properties/{property}', [PropertyController::class, 'show'])->name('properties.show');
        
        Route::middleware('permission:properties:create')->group(function () {
            Route::get('/properties/create', [PropertyController::class, 'create'])->name('properties.create');
            Route::post('/properties', [PropertyController::class, 'store'])->name('properties.store');
            Route::post('/properties/projects/quick', [PropertyController::class, 'quickCreateProject'])->name('properties.projects.quick');
        });
        
        Route::middleware('permission:properties:edit')->group(function () {
            Route::get('/properties/{property}/edit', [PropertyController::class, 'edit'])->name('properties.edit');
            Route::patch('/properties/{property}', [PropertyController::class, 'update'])->name('properties.update');
        });
        
        Route::middleware('permission:properties:delete')->group(function () {
            Route::delete('/properties/{property}', [PropertyController::class, 'destroy'])->name('properties.destroy');
        });
    });

    // Agent management routes
    Route::middleware('permission:agents:view')->group(function () {
        Route::get('/agents', [AgentController::class, 'index'])->name('agents.index');
        Route::get('/agents/{agent}', [AgentController::class, 'show'])->name('agents.show');
        
        Route::middleware('permission:agents:create')->group(function () {
            Route::get('/agents/create', [AgentController::class, 'create'])->name('agents.create');
            Route::post('/agents', [AgentController::class, 'store'])->name('agents.store');
            Route::post('/agents/quick', [AgentController::class, 'quickCreate'])->name('agents.quick');
        });
        
        Route::middleware('permission:agents:edit')->group(function () {
            Route::get('/agents/{agent}/edit', [AgentController::class, 'edit'])->name('agents.edit');
            Route::patch('/agents/{agent}', [AgentController::class, 'update'])->name('agents.update');
            Route::patch('/agents/{agent}/toggle-status', [AgentController::class, 'toggleStatus'])->name('agents.toggle-status');
        });
        
        Route::middleware('permission:agents:delete')->group(function () {
            Route::delete('/agents/{agent}', [AgentController::class, 'destroy'])->name('agents.destroy');
        });
    });

    // Client management routes
    Route::middleware('permission:clients:view')->group(function () {
        Route::get('/clients', [ClientController::class, 'index'])->name('clients.index');
        Route::get('/clients/{client}', [ClientController::class, 'show'])->name('clients.show');
        Route::get('/clients-select', [ClientController::class, 'getForSelect'])->name('clients.select');
        
        Route::middleware('permission:clients:create')->group(function () {
            Route::get('/clients/create', [ClientController::class, 'create'])->name('clients.create');
            Route::post('/clients', [ClientController::class, 'store'])->name('clients.store');
            Route::post('/clients/quick', [ClientController::class, 'quickCreate'])->name('clients.quick');
        });
        
        Route::middleware('permission:clients:edit')->group(function () {
            Route::get('/clients/{client}/edit', [ClientController::class, 'edit'])->name('clients.edit');
            Route::patch('/clients/{client}', [ClientController::class, 'update'])->name('clients.update');
            Route::post('/clients/{client}/associate-property', [ClientController::class, 'associateProperty'])->name('clients.associate-property');
        });
        
        Route::middleware('permission:clients:export')->group(function () {
            Route::get('/clients/export/excel', [ClientController::class, 'exportExcel'])->name('clients.export.excel');
            Route::get('/clients/export/pdf', [ClientController::class, 'exportPdf'])->name('clients.export.pdf');
        });
        
        Route::middleware('permission:clients:delete')->group(function () {
            Route::delete('/clients/{client}', [ClientController::class, 'destroy'])->name('clients.destroy');
        });
    });

    // Visit management routes
    Route::middleware('permission:visits:view')->group(function () {
        Route::get('/visits', [VisitController::class, 'index'])->name('visits.index');
        Route::get('/visits/{visit}', [VisitController::class, 'show'])->name('visits.show');
        
        Route::middleware('permission:visits:create')->group(function () {
            Route::get('/visits/create', [VisitController::class, 'create'])->name('visits.create');
            Route::post('/visits', [VisitController::class, 'store'])->name('visits.store');
        });
        
        Route::middleware('permission:visits:edit')->group(function () {
            Route::get('/visits/{visit}/edit', [VisitController::class, 'edit'])->name('visits.edit');
            Route::patch('/visits/{visit}', [VisitController::class, 'update'])->name('visits.update');
            Route::patch('/visits/{visit}/complete', [VisitController::class, 'markCompleted'])->name('visits.complete');
            Route::patch('/visits/{visit}/cancel', [VisitController::class, 'markCancelled'])->name('visits.cancel');
            Route::patch('/visits/{visit}/no-show', [VisitController::class, 'markNoShow'])->name('visits.no-show');
            Route::post('/visits/{visit}/reminder', [VisitController::class, 'sendReminder'])->name('visits.send-reminder');
            Route::post('/visits/{visit}/follow-up', [VisitController::class, 'scheduleFollowUp'])->name('visits.schedule-follow-up');
        });
        
        Route::middleware('permission:visits:delete')->group(function () {
            Route::delete('/visits/{visit}', [VisitController::class, 'destroy'])->name('visits.destroy');
        });
    });
});

require __DIR__.'/auth.php';
