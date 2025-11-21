<?php

use App\Http\Controllers\ActivityController;
use App\Http\Controllers\AgentController;
use App\Http\Controllers\CampaignController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmailCampaignController;
use App\Http\Controllers\EmailMarketingConfigController;
use App\Http\Controllers\EmailTemplateController;
use App\Http\Controllers\EmailTrackingController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\PropertyController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\VisitController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// CSRF token refresh endpoint
Route::get('/refresh-csrf', function () {
    return response()->json([
        'token' => csrf_token()
    ]);
});

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
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

    // Project management routes - FIXED ORDER: create routes before {project} routes
    Route::middleware('permission:projects:view')->group(function () {
        Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
        Route::get('/projects-select', [ProjectController::class, 'getForSelect'])->name('projects.select');
        
        Route::middleware('permission:projects:create')->group(function () {
            Route::get('/projects/create', [ProjectController::class, 'create'])->name('projects.create');
            Route::post('/projects/test', function(\Illuminate\Http\Request $request) {
                \Log::info('Test route reached', ['data' => $request->all()]);
                return response()->json(['status' => 'success', 'message' => 'Test route working']);
            })->name('projects.test');
            Route::post('/projects', [ProjectController::class, 'store'])->middleware('debug.request')->name('projects.store');
            Route::post('/projects-quick', [ProjectController::class, 'quickStore'])->name('projects.quick');
        });
        
        Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('projects.show');
        
        Route::middleware('permission:projects:edit')->group(function () {
            Route::get('/projects/{project}/edit', [ProjectController::class, 'edit'])->name('projects.edit');
            Route::patch('/projects/{project}', [ProjectController::class, 'update'])->name('projects.update');
            Route::post('/projects/update-order', [ProjectController::class, 'updateOrder'])->name('projects.updateOrder');
            Route::patch('/projects/{project}/toggle-visibility', [ProjectController::class, 'toggleVisibility'])->name('projects.toggleVisibility');
        });
        
        Route::middleware('permission:projects:delete')->group(function () {
            Route::delete('/projects/{project}', [ProjectController::class, 'destroy'])->name('projects.destroy');
        });
    });

    // Property management routes - FIXED ORDER: create routes before {property} routes
    Route::middleware('permission:properties:view')->group(function () {
        Route::get('/properties', [PropertyController::class, 'index'])->name('properties.index');
        
        Route::middleware('permission:properties:create')->group(function () {
            Route::get('/properties/create', [PropertyController::class, 'create'])->name('properties.create');
            Route::post('/properties', [PropertyController::class, 'store'])->name('properties.store');
            Route::post('/properties/projects/quick', [PropertyController::class, 'quickCreateProject'])->name('properties.projects.quick');
        });
        
        Route::get('/properties/{property}', [PropertyController::class, 'show'])->name('properties.show');
        
        Route::middleware('permission:properties:edit')->group(function () {
            Route::get('/properties/{property}/edit', [PropertyController::class, 'edit'])->name('properties.edit');
            Route::patch('/properties/{property}', [PropertyController::class, 'update'])->name('properties.update');
            Route::patch('/properties/{property}/toggle-status', [PropertyController::class, 'toggleStatus'])->name('properties.toggle-status');
            Route::post('/properties/update-order', [PropertyController::class, 'updateOrder'])->name('properties.updateOrder');
            Route::patch('/properties/{property}/toggle-visibility', [PropertyController::class, 'toggleVisibility'])->name('properties.toggleVisibility');
        });
        
        Route::middleware('permission:properties:delete')->group(function () {
            Route::delete('/properties/{property}', [PropertyController::class, 'destroy'])->name('properties.destroy');
        });
    });

    // Agent management routes - FIXED ORDER: create routes before {agent} routes
    Route::middleware('permission:agents:view')->group(function () {
        Route::get('/agents', [AgentController::class, 'index'])->name('agents.index');
        
        Route::middleware('permission:agents:create')->group(function () {
            Route::get('/agents/create', [AgentController::class, 'create'])->name('agents.create');
            Route::post('/agents', [AgentController::class, 'store'])->name('agents.store');
            Route::post('/agents/quick', [AgentController::class, 'quickCreate'])->name('agents.quick');
        });
        
        Route::get('/agents/{agent}', [AgentController::class, 'show'])->name('agents.show');
        
        Route::middleware('permission:agents:edit')->group(function () {
            Route::get('/agents/{agent}/edit', [AgentController::class, 'edit'])->name('agents.edit');
            Route::patch('/agents/{agent}', [AgentController::class, 'update'])->name('agents.update');
            Route::patch('/agents/{agent}/toggle-status', [AgentController::class, 'toggleStatus'])->name('agents.toggle-status');
            Route::post('/agents/update-order', [AgentController::class, 'updateOrder'])->name('agents.updateOrder');
            Route::patch('/agents/{agent}/toggle-visibility', [AgentController::class, 'toggleVisibility'])->name('agents.toggleVisibility');
        });
        
        Route::middleware('permission:agents:delete')->group(function () {
            Route::delete('/agents/{agent}', [AgentController::class, 'destroy'])->name('agents.destroy');
        });
    });

    // Client management routes - FIXED ORDER: create and specific routes before {client} routes
    Route::middleware('permission:clients:view')->group(function () {
        Route::get('/clients', [ClientController::class, 'index'])->name('clients.index');
        Route::get('/clients-select', [ClientController::class, 'getForSelect'])->name('clients.select');
        
        Route::middleware('permission:clients:create')->group(function () {
            Route::get('/clients/create', [ClientController::class, 'create'])->name('clients.create');
            Route::post('/clients', [ClientController::class, 'store'])->name('clients.store');
            Route::post('/clients/quick', [ClientController::class, 'quickCreate'])->name('clients.quick');
        });
        
        Route::middleware('permission:clients:export')->group(function () {
            Route::get('/clients/export/excel', [ClientController::class, 'exportExcel'])->name('clients.export.excel');
            Route::get('/clients/export/pdf', [ClientController::class, 'exportPdf'])->name('clients.export.pdf');
        });
        
        Route::get('/clients/{client}', [ClientController::class, 'show'])->name('clients.show');
        
        Route::middleware('permission:clients:edit')->group(function () {
            Route::get('/clients/{client}/edit', [ClientController::class, 'edit'])->name('clients.edit');
            Route::patch('/clients/{client}', [ClientController::class, 'update'])->name('clients.update');
            Route::post('/clients/{client}/associate-property', [ClientController::class, 'associateProperty'])->name('clients.associate-property');
        });
        
        Route::middleware('permission:clients:delete')->group(function () {
            Route::delete('/clients/{client}', [ClientController::class, 'destroy'])->name('clients.destroy');
        });
    });

    // Visit management routes - FIXED ORDER: create routes before {visit} routes
    Route::middleware('permission:visits:view')->group(function () {
        Route::get('/visits', [VisitController::class, 'index'])->name('visits.index');
        
        Route::middleware('permission:visits:create')->group(function () {
            Route::get('/visits/create', [VisitController::class, 'create'])->name('visits.create');
            Route::post('/visits', [VisitController::class, 'store'])->name('visits.store');
        });
        
        Route::get('/visits/{visit}', [VisitController::class, 'show'])->name('visits.show');
        
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

    // Marketing management routes - FIXED ORDER: create routes before {id} routes
    Route::middleware('permission:marketing:view')->group(function () {
        // Campaign routes
        Route::get('/campaigns', [CampaignController::class, 'index'])->name('campaigns.index');
        Route::get('/campaigns/create', [CampaignController::class, 'create'])->name('campaigns.create')->middleware('permission:campaigns:create');
        Route::post('/campaigns', [CampaignController::class, 'store'])->name('campaigns.store')->middleware('permission:campaigns:create');
        Route::get('/campaigns/{campaign}', [CampaignController::class, 'show'])->name('campaigns.show');
        Route::get('/campaigns/{campaign}/edit', [CampaignController::class, 'edit'])->name('campaigns.edit')->middleware('permission:campaigns:edit');
        Route::patch('/campaigns/{campaign}', [CampaignController::class, 'update'])->name('campaigns.update')->middleware('permission:campaigns:edit');
        Route::delete('/campaigns/{campaign}', [CampaignController::class, 'destroy'])->name('campaigns.destroy')->middleware('permission:campaigns:delete');
        Route::patch('/campaigns/{campaign}/toggle-status', [CampaignController::class, 'toggleStatus'])->name('campaigns.toggle-status')->middleware('permission:campaigns:edit');
        Route::post('/campaigns/{campaign}/duplicate', [CampaignController::class, 'duplicate'])->name('campaigns.duplicate')->middleware('permission:campaigns:create');
        Route::get('/campaigns/{campaign}/analytics', [CampaignController::class, 'analytics'])->name('campaigns.analytics');

        // Lead routes
        Route::get('/leads', [LeadController::class, 'index'])->name('leads.index');
        Route::get('/leads-select', [LeadController::class, 'getForSelect'])->name('leads.select');
        Route::get('/leads/create', [LeadController::class, 'create'])->name('leads.create')->middleware('permission:leads:create');
        Route::post('/leads', [LeadController::class, 'store'])->name('leads.store')->middleware('permission:leads:create');
        Route::get('/leads/export', [LeadController::class, 'export'])->name('leads.export')->middleware('permission:leads:export');
        Route::get('/leads/analytics', [LeadController::class, 'analytics'])->name('leads.analytics');
        Route::get('/leads/{lead}', [LeadController::class, 'show'])->name('leads.show');
        Route::get('/leads/{lead}/edit', [LeadController::class, 'edit'])->name('leads.edit')->middleware('permission:leads:edit');
        Route::patch('/leads/{lead}', [LeadController::class, 'update'])->name('leads.update')->middleware('permission:leads:edit');
        Route::delete('/leads/{lead}', [LeadController::class, 'destroy'])->name('leads.destroy')->middleware('permission:leads:delete');
        Route::post('/leads/{lead}/assign-agent', [LeadController::class, 'assignAgent'])->name('leads.assign-agent')->middleware('permission:leads:edit');
        Route::patch('/leads/{lead}/update-status', [LeadController::class, 'updateStatus'])->name('leads.update-status')->middleware('permission:leads:edit');
        Route::post('/leads/{lead}/convert-to-client', [LeadController::class, 'convertToClient'])->name('leads.convert-to-client')->middleware('permission:leads:edit');
        Route::post('/leads/{lead}/schedule-follow-up', [LeadController::class, 'scheduleFollowUp'])->name('leads.schedule-follow-up')->middleware('permission:leads:edit');
    });

    // Blog Routes
    Route::middleware('permission:blogs:view')->group(function () {
        Route::get('/blogs', [App\Http\Controllers\BlogController::class, 'index'])->name('blogs.index');
        
        Route::middleware('permission:blogs:create')->group(function () {
            Route::get('/blogs/create', [App\Http\Controllers\BlogController::class, 'create'])->name('blogs.create');
            Route::post('/blogs', [App\Http\Controllers\BlogController::class, 'store'])->name('blogs.store');
            Route::post('/blogs/{blog}/duplicate', [App\Http\Controllers\BlogController::class, 'duplicate'])->name('blogs.duplicate');
        });
        
        Route::get('/blogs/{blog}', [App\Http\Controllers\BlogController::class, 'show'])->name('blogs.show');
        Route::get('/blogs/{blog}/analytics', [App\Http\Controllers\BlogController::class, 'analytics'])->name('blogs.analytics');
        
        Route::middleware('permission:blogs:edit')->group(function () {
            Route::get('/blogs/{blog}/edit', [App\Http\Controllers\BlogController::class, 'edit'])->name('blogs.edit');
            Route::patch('/blogs/{blog}', [App\Http\Controllers\BlogController::class, 'update'])->name('blogs.update');
            Route::post('/blogs/update-order', [App\Http\Controllers\BlogController::class, 'updateOrder'])->name('blogs.updateOrder');
            Route::patch('/blogs/{blog}/toggle-visibility', [App\Http\Controllers\BlogController::class, 'toggleVisibility'])->name('blogs.toggleVisibility');
            Route::patch('/blogs/{blog}/toggle-status', [App\Http\Controllers\BlogController::class, 'toggleStatus'])->name('blogs.toggleStatus');
        });
        
        Route::middleware('permission:blogs:delete')->group(function () {
            Route::delete('/blogs/{blog}', [App\Http\Controllers\BlogController::class, 'destroy'])->name('blogs.destroy');
        });
    });

    // Activities Routes
    Route::middleware('permission:activities:view')->group(function () {
        Route::get('/activities', [ActivityController::class, 'index'])->name('activities.index');
        Route::get('/activities/dashboard', [ActivityController::class, 'dashboard'])->name('activities.dashboard');
        Route::get('/activities/leads/{lead}', [ActivityController::class, 'getForLead'])->name('activities.for-lead');
        
        Route::middleware('permission:activities:create')->group(function () {
            Route::get('/activities/create', [ActivityController::class, 'create'])->name('activities.create');
            Route::post('/activities', [ActivityController::class, 'store'])->name('activities.store');
            Route::post('/activities/{activity}/follow-up', [ActivityController::class, 'createFollowUp'])->name('activities.follow-up');
        });
        
        Route::get('/activities/{activity}', [ActivityController::class, 'show'])->name('activities.show');
        
        Route::middleware('permission:activities:edit')->group(function () {
            Route::get('/activities/{activity}/edit', [ActivityController::class, 'edit'])->name('activities.edit');
            Route::patch('/activities/{activity}', [ActivityController::class, 'update'])->name('activities.update');
            Route::post('/activities/{activity}/complete', [ActivityController::class, 'markCompleted'])->name('activities.complete');
            Route::post('/activities/{activity}/cancel', [ActivityController::class, 'markCancelled'])->name('activities.cancel');
        });
        
        Route::middleware('permission:activities:delete')->group(function () {
            Route::delete('/activities/{activity}', [ActivityController::class, 'destroy'])->name('activities.destroy');
        });
    });
});

// Email Templates Routes
Route::middleware(['auth', 'permission:email-marketing-view'])->group(function () {
        Route::get('/email-templates', [EmailTemplateController::class, 'index'])->name('email-templates.index');
        Route::get('/email-templates/{emailTemplate}/preview', [EmailTemplateController::class, 'preview'])->name('email-templates.preview');
        Route::get('/email-templates/variables', [EmailTemplateController::class, 'getVariables'])->name('email-templates.variables');
        
        Route::middleware('permission:email-marketing-create')->group(function () {
            Route::get('/email-templates/create', [EmailTemplateController::class, 'create'])->name('email-templates.create');
            Route::post('/email-templates', [EmailTemplateController::class, 'store'])->name('email-templates.store');
            Route::post('/email-templates/{emailTemplate}/duplicate', [EmailTemplateController::class, 'duplicate'])->name('email-templates.duplicate');
        });
        
        Route::get('/email-templates/{emailTemplate}', [EmailTemplateController::class, 'show'])->name('email-templates.show');
        Route::get('/email-templates/{emailTemplate}/test', [EmailTemplateController::class, 'test'])->name('email-templates.test');
        
        Route::middleware('permission:email-marketing-edit')->group(function () {
            Route::get('/email-templates/{emailTemplate}/edit', [EmailTemplateController::class, 'edit'])->name('email-templates.edit');
            Route::patch('/email-templates/{emailTemplate}', [EmailTemplateController::class, 'update'])->name('email-templates.update');
        });
        
        Route::middleware('permission:email-marketing-delete')->group(function () {
            Route::delete('/email-templates/{emailTemplate}', [EmailTemplateController::class, 'destroy'])->name('email-templates.destroy');
        });
    });

// Email Campaigns Routes
Route::middleware(['auth', 'permission:email-marketing-view'])->group(function () {
        Route::get('/email-campaigns', [EmailCampaignController::class, 'index'])->name('email-campaigns.index');
        Route::get('/email-campaigns/{emailCampaign}/preview', [EmailCampaignController::class, 'preview'])->name('email-campaigns.preview');
        Route::get('/email-campaigns/{emailCampaign}/recipients', [EmailCampaignController::class, 'recipients'])->name('email-campaigns.recipients');
        Route::post('/email-campaigns/calculate-recipients', [EmailCampaignController::class, 'calculateRecipients'])->name('email-campaigns.calculate-recipients');
        
        Route::middleware('permission:email-marketing-create')->group(function () {
            Route::get('/email-campaigns/create', [EmailCampaignController::class, 'create'])->name('email-campaigns.create');
            Route::post('/email-campaigns', [EmailCampaignController::class, 'store'])->name('email-campaigns.store');
            Route::post('/email-campaigns/{emailCampaign}/duplicate', [EmailCampaignController::class, 'duplicate'])->name('email-campaigns.duplicate');
        });
        
        Route::get('/email-campaigns/{emailCampaign}', [EmailCampaignController::class, 'show'])->name('email-campaigns.show');
        
        Route::middleware('permission:email-marketing-edit')->group(function () {
            Route::get('/email-campaigns/{emailCampaign}/edit', [EmailCampaignController::class, 'edit'])->name('email-campaigns.edit');
            Route::patch('/email-campaigns/{emailCampaign}', [EmailCampaignController::class, 'update'])->name('email-campaigns.update');
            Route::post('/email-campaigns/{emailCampaign}/send', [EmailCampaignController::class, 'send'])->name('email-campaigns.send');
            Route::post('/email-campaigns/{emailCampaign}/schedule', [EmailCampaignController::class, 'schedule'])->name('email-campaigns.schedule');
            Route::post('/email-campaigns/{emailCampaign}/pause', [EmailCampaignController::class, 'pause'])->name('email-campaigns.pause');
            Route::post('/email-campaigns/{emailCampaign}/resume', [EmailCampaignController::class, 'resume'])->name('email-campaigns.resume');
            Route::post('/email-campaigns/{emailCampaign}/cancel', [EmailCampaignController::class, 'cancel'])->name('email-campaigns.cancel');
        });
        
        Route::middleware('permission:email-marketing-delete')->group(function () {
            Route::delete('/email-campaigns/{emailCampaign}', [EmailCampaignController::class, 'destroy'])->name('email-campaigns.destroy');
        });
    });

// Email Tracking Routes (public, no auth required)
Route::get('/email/track/{token}', [EmailTrackingController::class, 'trackOpen'])->name('email.track');
Route::get('/email/click/{token}', [EmailTrackingController::class, 'trackClick'])->name('email.click');
Route::get('/unsubscribe/{token}', [EmailTrackingController::class, 'showUnsubscribeForm'])->name('email.unsubscribe.form');
Route::post('/unsubscribe/{token}', [EmailTrackingController::class, 'processUnsubscribe'])->name('email.unsubscribe.process');
Route::get('/unsubscribe/{token}/direct', [EmailTrackingController::class, 'unsubscribe'])->name('email.unsubscribe.direct');

// Email Analytics API Routes (auth required)
Route::middleware('auth')->group(function () {
    Route::get('/api/email/stats', [EmailTrackingController::class, 'getStats'])->name('email.stats');
    Route::get('/api/email/clicks', [EmailTrackingController::class, 'getClickHeatmap'])->name('email.clicks');
    Route::get('/api/email/geo', [EmailTrackingController::class, 'getGeoData'])->name('email.geo');
    Route::get('/api/email/devices', [EmailTrackingController::class, 'getDeviceStats'])->name('email.devices');
});

// Email Marketing Configuration Routes
Route::middleware(['auth', 'permission:email-marketing-config'])->group(function () {
    Route::get('/email-marketing/config', [EmailMarketingConfigController::class, 'index'])->name('email-marketing.config');
    Route::post('/email-marketing/config', [EmailMarketingConfigController::class, 'update'])->name('email-marketing.config.update');
    Route::post('/email-marketing/config/test', [EmailMarketingConfigController::class, 'test'])->name('email-marketing.config.test');
});

require __DIR__.'/auth.php';
