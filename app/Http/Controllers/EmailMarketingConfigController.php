<?php

namespace App\Http\Controllers;

use App\Services\EmailMarketingService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class EmailMarketingConfigController extends Controller
{
    /**
     * Show email marketing configuration page.
     */
    public function index(): Response
    {
        $service = app(EmailMarketingService::class);
        
        // Load configuration from database
        $configRecord = \DB::table('email_marketing_configs')
            ->where('key', 'main_config')
            ->first();
            
        $savedConfig = $configRecord ? json_decode($configRecord->settings, true) : [];
        
        return Inertia::render('EmailMarketing/Config/Index', [
            'config' => array_merge([
                'enabled' => config('emailmarketing.enabled', true),
                'provider' => config('emailmarketing.provider', 'smtp'),
                'rate_limiting' => config('emailmarketing.rate_limiting'),
                'bounce_handling' => config('emailmarketing.bounce_handling'),
                'tracking' => config('emailmarketing.tracking'),
                'compliance' => config('emailmarketing.compliance'),
                'queue' => config('emailmarketing.queue'),
                // Provider configurations
                'smtp_config' => [],
                'sendgrid_config' => [],
                'mailgun_config' => [],
                'ses_config' => [],
                'postmark_config' => [],
                'mailtrap_config' => [],
            ], $savedConfig),
            'providers' => [
                'smtp' => 'SMTP',
                'sendgrid' => 'SendGrid',
                'mailgun' => 'Mailgun',
                'ses' => 'Amazon SES',
                'postmark' => 'Postmark',
                'mailtrap' => 'Mailtrap (Testing)',
            ],
            'stats' => $service->getStats(),
            'system_status' => $this->getSystemStatus(),
            'queueStats' => $this->getQueueStats(),
        ]);
    }

    /**
     * Update email marketing configuration.
     */
    public function update(Request $request)
    {
        $rules = [
            'enabled' => 'boolean',
            'provider' => 'string|in:smtp,sendgrid,mailgun,ses,postmark,mailtrap',
            
            // SMTP Configuration
            'smtp_config.host' => 'nullable|string',
            'smtp_config.port' => 'nullable|integer',
            'smtp_config.username' => 'nullable|email',
            'smtp_config.password' => 'nullable|string',
            'smtp_config.encryption' => 'nullable|in:tls,ssl',
            'smtp_config.from_address' => 'nullable|email',
            
            // SendGrid Configuration
            'sendgrid_config.api_key' => 'nullable|string',
            'sendgrid_config.from_address' => 'nullable|email',
            'sendgrid_config.from_name' => 'nullable|string',
            
            // Mailgun Configuration
            'mailgun_config.domain' => 'nullable|string',
            'mailgun_config.secret' => 'nullable|string',
            'mailgun_config.endpoint' => 'nullable|string',
            'mailgun_config.from_address' => 'nullable|email',
            
            // Amazon SES Configuration
            'ses_config.key' => 'nullable|string',
            'ses_config.secret' => 'nullable|string',
            'ses_config.region' => 'nullable|string',
            'ses_config.from_address' => 'nullable|email',
            
            // Postmark Configuration
            'postmark_config.token' => 'nullable|string',
            'postmark_config.from_address' => 'nullable|email',
            'postmark_config.from_name' => 'nullable|string',
            
            // Mailtrap Configuration
            'mailtrap_config.username' => 'nullable|string',
            'mailtrap_config.password' => 'nullable|string',
            'mailtrap_config.from_address' => 'nullable|email',
            
            // Other configurations
            'rate_limiting.enabled' => 'boolean',
            'rate_limiting.emails_per_minute' => 'integer|min:1|max:100',
            'rate_limiting.emails_per_hour' => 'integer|min:1|max:5000',
            'rate_limiting.emails_per_day' => 'integer|min:1|max:50000',
            'rate_limiting.delay_between_emails' => 'integer|min:0|max:60',
            'bounce_handling.enabled' => 'boolean',
            'bounce_handling.max_soft_bounces' => 'integer|min:1|max:10',
            'bounce_handling.max_hard_bounces' => 'integer|min:1|max:5',
            'bounce_handling.auto_unsubscribe_on_hard_bounce' => 'boolean',
            'bounce_handling.auto_unsubscribe_on_spam' => 'boolean',
            'tracking.enabled' => 'boolean',
            'tracking.open_tracking' => 'boolean',
            'tracking.click_tracking' => 'boolean',
            'compliance.require_double_opt_in' => 'boolean',
            'compliance.include_physical_address' => 'boolean',
            'compliance.unsubscribe_footer' => 'boolean',
            'compliance.list_unsubscribe_header' => 'boolean',
        ];

        $validated = $request->validate($rules);

        // Save configuration to database or config files
        $this->saveEmailMarketingConfig($validated);
        
        return redirect()->back()->with('success', 'Configuración actualizada exitosamente.');
    }

    /**
     * Save email marketing configuration.
     */
    private function saveEmailMarketingConfig(array $config)
    {
        // Create/update email marketing configuration record
        $configData = [
            'enabled' => $config['enabled'] ?? true,
            'provider' => $config['provider'] ?? 'smtp',
            'settings' => json_encode($config),
            'updated_at' => now(),
        ];

        // In a production environment, you would save this to a dedicated table
        // For now, we'll save to a simple config file or use Laravel's config system
        
        // Example: Save to a custom config table
        \DB::table('email_marketing_configs')
            ->updateOrInsert(['key' => 'main_config'], $configData);
            
        // Also update .env values for the selected provider
        $this->updateEnvForProvider($config['provider'], $config);
    }

    /**
     * Update .env file with provider-specific configuration.
     */
    private function updateEnvForProvider(string $provider, array $config)
    {
        $envUpdates = [];
        
        switch ($provider) {
            case 'smtp':
                if (!empty($config['smtp_config'])) {
                    $envUpdates = [
                        'MAIL_MAILER' => 'smtp',
                        'MAIL_HOST' => $config['smtp_config']['host'] ?? '',
                        'MAIL_PORT' => $config['smtp_config']['port'] ?? '587',
                        'MAIL_USERNAME' => $config['smtp_config']['username'] ?? '',
                        'MAIL_PASSWORD' => $config['smtp_config']['password'] ?? '',
                        'MAIL_ENCRYPTION' => $config['smtp_config']['encryption'] ?? 'tls',
                        'MAIL_FROM_ADDRESS' => $config['smtp_config']['from_address'] ?? '',
                    ];
                }
                break;
                
            case 'sendgrid':
                if (!empty($config['sendgrid_config'])) {
                    $envUpdates = [
                        'MAIL_MAILER' => 'sendgrid',
                        'SENDGRID_API_KEY' => $config['sendgrid_config']['api_key'] ?? '',
                        'MAIL_FROM_ADDRESS' => $config['sendgrid_config']['from_address'] ?? '',
                        'MAIL_FROM_NAME' => $config['sendgrid_config']['from_name'] ?? '',
                    ];
                }
                break;
                
            case 'mailgun':
                if (!empty($config['mailgun_config'])) {
                    $envUpdates = [
                        'MAIL_MAILER' => 'mailgun',
                        'MAILGUN_DOMAIN' => $config['mailgun_config']['domain'] ?? '',
                        'MAILGUN_SECRET' => $config['mailgun_config']['secret'] ?? '',
                        'MAILGUN_ENDPOINT' => $config['mailgun_config']['endpoint'] ?? 'api.mailgun.net',
                        'MAIL_FROM_ADDRESS' => $config['mailgun_config']['from_address'] ?? '',
                    ];
                }
                break;
                
            case 'ses':
                if (!empty($config['ses_config'])) {
                    $envUpdates = [
                        'MAIL_MAILER' => 'ses',
                        'AWS_ACCESS_KEY_ID' => $config['ses_config']['key'] ?? '',
                        'AWS_SECRET_ACCESS_KEY' => $config['ses_config']['secret'] ?? '',
                        'AWS_DEFAULT_REGION' => $config['ses_config']['region'] ?? 'us-east-1',
                        'MAIL_FROM_ADDRESS' => $config['ses_config']['from_address'] ?? '',
                    ];
                }
                break;
                
            case 'postmark':
                if (!empty($config['postmark_config'])) {
                    $envUpdates = [
                        'MAIL_MAILER' => 'postmark',
                        'POSTMARK_TOKEN' => $config['postmark_config']['token'] ?? '',
                        'MAIL_FROM_ADDRESS' => $config['postmark_config']['from_address'] ?? '',
                        'MAIL_FROM_NAME' => $config['postmark_config']['from_name'] ?? '',
                    ];
                }
                break;
                
            case 'mailtrap':
                if (!empty($config['mailtrap_config'])) {
                    $envUpdates = [
                        'MAIL_MAILER' => 'smtp',
                        'MAIL_HOST' => 'smtp.mailtrap.io',
                        'MAIL_PORT' => '2525',
                        'MAIL_USERNAME' => $config['mailtrap_config']['username'] ?? '',
                        'MAIL_PASSWORD' => $config['mailtrap_config']['password'] ?? '',
                        'MAIL_ENCRYPTION' => 'tls',
                        'MAIL_FROM_ADDRESS' => $config['mailtrap_config']['from_address'] ?? '',
                    ];
                }
                break;
        }

        // Note: Updating .env programmatically requires careful handling
        // In production, consider using Laravel's config cache or database configuration
        foreach ($envUpdates as $key => $value) {
            $this->updateEnvVariable($key, $value);
        }
    }

    /**
     * Update an environment variable in .env file.
     */
    private function updateEnvVariable(string $key, string $value): void
    {
        $envPath = base_path('.env');
        
        if (!file_exists($envPath)) {
            return;
        }
        
        $env = file_get_contents($envPath);
        $pattern = "/^{$key}=.*/m";
        $replacement = "{$key}=" . (str_contains($value, ' ') ? "\"{$value}\"" : $value);
        
        if (preg_match($pattern, $env)) {
            $env = preg_replace($pattern, $replacement, $env);
        } else {
            $env .= "\n{$replacement}";
        }
        
        file_put_contents($envPath, $env);
    }

    /**
     * Test email configuration.
     */
    public function test(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        try {
            $service = app(EmailMarketingService::class);
            
            // Use a simple template for testing
            $testTemplate = new \App\Models\EmailTemplate([
                'subject' => 'Test de Configuración - {{company_name}}',
                'html_content' => '<h1>¡Configuración Exitosa!</h1><p>Este es un email de prueba desde {{company_name}}. Si recibes este mensaje, la configuración de email marketing está funcionando correctamente.</p><p>Fecha: {{current_date}}</p>',
                'text_content' => 'Configuración Exitosa! Este es un email de prueba desde {{company_name}}. Fecha: {{current_date}}'
            ]);

            $success = $service->sendTestEmail($testTemplate, $request->email, [
                'company_name' => config('app.name', 'InmoApp'),
                'current_date' => now()->format('d/m/Y H:i')
            ]);

            if ($success) {
                return response()->json(['message' => 'Email de prueba enviado exitosamente.']);
            } else {
                return response()->json(['message' => 'Error al enviar email de prueba.'], 500);
            }

        } catch (\Exception $e) {
            return response()->json(['message' => 'Error: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Get system status information.
     */
    private function getSystemStatus(): array
    {
        return [
            'mail_configured' => !empty(config('mail.mailers.smtp.host')),
            'queue_configured' => config('queue.default') !== 'sync',
            'database_accessible' => $this->isDatabaseAccessible(),
            'cache_working' => $this->isCacheWorking(),
            'queue_workers_running' => $this->areQueueWorkersRunning(),
        ];
    }

    /**
     * Check if database is accessible.
     */
    private function isDatabaseAccessible(): bool
    {
        try {
            \DB::connection()->getPdo();
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Check if cache is working.
     */
    private function isCacheWorking(): bool
    {
        try {
            $testKey = 'email_marketing_cache_test';
            \Cache::put($testKey, 'working', 1);
            $result = \Cache::get($testKey) === 'working';
            \Cache::forget($testKey);
            return $result;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Check if queue workers are running.
     */
    private function areQueueWorkersRunning(): bool
    {
        try {
            // Check if there are jobs being processed recently
            $recentJobs = \DB::table('jobs')
                ->where('queue', 'email-marketing')
                ->where('created_at', '>', now()->subMinutes(5))
                ->count();

            $processingJobs = \DB::table('jobs')
                ->where('queue', 'email-marketing')
                ->whereNotNull('reserved_at')
                ->count();

            // If there are processing jobs or recent activity, workers are likely running
            return $processingJobs > 0 || $recentJobs > 0;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get queue statistics for dashboard.
     */
    private function getQueueStats(): array
    {
        try {
            $processing = \DB::table('jobs')
                ->where('queue', 'email-marketing')
                ->whereNotNull('reserved_at')
                ->count();

            $pending = \DB::table('jobs')
                ->where('queue', 'email-marketing')
                ->whereNull('reserved_at')
                ->count();

            $failed = \DB::table('failed_jobs')
                ->where('queue', 'email-marketing')
                ->where('failed_at', '>', now()->subDay())
                ->count();

            $total = $processing + $pending;

            return [
                'processing' => $processing,
                'pending' => $pending,
                'failed' => $failed,
                'total' => max($total, 1), // Avoid division by zero
            ];
        } catch (\Exception $e) {
            return [
                'processing' => 0,
                'pending' => 0,
                'failed' => 0,
                'total' => 1,
            ];
        }
    }
}
