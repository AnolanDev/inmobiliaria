<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\DB;
use App\Models\EmailCampaign;
use App\Jobs\SendEmailCampaign;

class EmailMarketingQueue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email-marketing:queue
                            {action : Action to perform: status, start, stop, restart, clear, stats}
                            {--workers=1 : Number of workers to start}
                            {--timeout=60 : Timeout for workers in seconds}
                            {--memory=512 : Memory limit for workers in MB}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manage email marketing queue workers and monitor status';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $action = $this->argument('action');

        return match ($action) {
            'status' => $this->showStatus(),
            'start' => $this->startWorkers(),
            'stop' => $this->stopWorkers(),
            'restart' => $this->restartWorkers(),
            'clear' => $this->clearQueue(),
            'stats' => $this->showStats(),
            default => $this->showHelp()
        };
    }

    /**
     * Show queue status.
     */
    private function showStatus(): int
    {
        $this->info('📊 Email Marketing Queue Status');
        $this->line('');

        // Check if queues are configured
        $queueConnection = config('emailmarketing.queue.connection', 'database');
        $queueName = config('emailmarketing.queue.name', 'email-marketing');

        $this->line("Queue Connection: {$queueConnection}");
        $this->line("Queue Name: {$queueName}");
        $this->line('');

        // Get queue statistics
        try {
            $totalJobs = $this->getJobsCount('total');
            $pendingJobs = $this->getJobsCount('pending');
            $failedJobs = $this->getJobsCount('failed');
            $processingJobs = $this->getJobsCount('processing');

            $this->table([
                'Status', 'Count'
            ], [
                ['Total Jobs', $totalJobs],
                ['Pending Jobs', $pendingJobs],
                ['Processing Jobs', $processingJobs],
                ['Failed Jobs', $failedJobs],
            ]);

            // Show active campaigns
            $activeCampaigns = EmailCampaign::whereIn('status', ['sending', 'scheduled'])->count();
            $this->line("Active Campaigns: {$activeCampaigns}");

        } catch (\Exception $e) {
            $this->error("Error getting queue status: " . $e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    /**
     * Start queue workers.
     */
    private function startWorkers(): int
    {
        $workers = $this->option('workers');
        $timeout = $this->option('timeout');
        $memory = $this->option('memory');

        $this->info("🚀 Starting {$workers} email marketing queue worker(s)...");

        for ($i = 1; $i <= $workers; $i++) {
            $command = sprintf(
                'php artisan queue:work --queue=email-marketing --timeout=%d --memory=%d --sleep=3 --tries=3 --daemon',
                $timeout,
                $memory
            );

            $this->line("Starting worker {$i}: {$command}");
            
            if (PHP_OS_FAMILY === 'Windows') {
                pclose(popen("start /B " . $command, "r"));
            } else {
                exec($command . " > /dev/null 2>&1 &");
            }
        }

        $this->info("✅ Started {$workers} worker(s) for email marketing queue");
        $this->warn("💡 Workers are running in the background. Use 'email-marketing:queue status' to monitor.");

        return Command::SUCCESS;
    }

    /**
     * Stop queue workers.
     */
    private function stopWorkers(): int
    {
        $this->warn('⏹️  Stopping email marketing queue workers...');

        if (PHP_OS_FAMILY === 'Windows') {
            exec('taskkill /f /im php.exe');
        } else {
            exec("pkill -f 'queue:work.*email-marketing'");
        }

        $this->info('✅ Queue workers stopped');
        return Command::SUCCESS;
    }

    /**
     * Restart queue workers.
     */
    private function restartWorkers(): int
    {
        $this->info('🔄 Restarting email marketing queue workers...');
        
        $this->stopWorkers();
        sleep(2);
        return $this->startWorkers();
    }

    /**
     * Clear the queue.
     */
    private function clearQueue(): int
    {
        if (!$this->confirm('⚠️  Are you sure you want to clear the email marketing queue? This will delete all pending jobs.')) {
            $this->info('Operation cancelled');
            return Command::SUCCESS;
        }

        try {
            // Clear email-marketing queue jobs
            $deletedJobs = DB::table('jobs')
                ->where('queue', 'email-marketing')
                ->delete();

            // Clear failed jobs for email marketing
            $deletedFailedJobs = DB::table('failed_jobs')
                ->where('payload', 'like', '%SendEmailCampaign%')
                ->delete();

            $this->info("✅ Queue cleared: {$deletedJobs} pending jobs and {$deletedFailedJobs} failed jobs deleted");

        } catch (\Exception $e) {
            $this->error("Error clearing queue: " . $e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    /**
     * Show detailed statistics.
     */
    private function showStats(): int
    {
        $this->info('📈 Email Marketing Queue Statistics');
        $this->line('');

        try {
            // Queue statistics
            $stats = [
                'Total Jobs (All Time)' => $this->getJobsCount('total'),
                'Pending Jobs' => $this->getJobsCount('pending'),
                'Processing Jobs' => $this->getJobsCount('processing'),
                'Failed Jobs' => $this->getJobsCount('failed'),
                'Completed Jobs (Last 24h)' => $this->getCompletedJobsLast24h(),
            ];

            $this->table(['Metric', 'Value'], collect($stats)->map(fn($value, $key) => [$key, $value])->toArray());

            // Campaign statistics
            $this->line('');
            $this->info('📊 Campaign Statistics');
            
            $campaignStats = [
                'Total Campaigns' => EmailCampaign::count(),
                'Scheduled Campaigns' => EmailCampaign::where('status', 'scheduled')->count(),
                'Sending Campaigns' => EmailCampaign::where('status', 'sending')->count(),
                'Completed Campaigns' => EmailCampaign::where('status', 'sent')->count(),
                'Paused Campaigns' => EmailCampaign::where('status', 'paused')->count(),
                'Cancelled Campaigns' => EmailCampaign::where('status', 'cancelled')->count(),
            ];

            $this->table(['Status', 'Count'], collect($campaignStats)->map(fn($value, $key) => [$key, $value])->toArray());

            // Recent activity
            $this->line('');
            $this->info('📋 Recent Campaigns');
            
            $recentCampaigns = EmailCampaign::latest()
                ->take(5)
                ->get(['id', 'name', 'status', 'created_at', 'emails_sent']);

            if ($recentCampaigns->isNotEmpty()) {
                $this->table([
                    'ID', 'Name', 'Status', 'Created', 'Emails Sent'
                ], $recentCampaigns->map(fn($campaign) => [
                    $campaign->id,
                    str_limit($campaign->name, 30),
                    $campaign->status,
                    $campaign->created_at->diffForHumans(),
                    $campaign->emails_sent ?? 0
                ])->toArray());
            } else {
                $this->line('No recent campaigns found');
            }

        } catch (\Exception $e) {
            $this->error("Error getting statistics: " . $e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    /**
     * Show help information.
     */
    private function showHelp(): int
    {
        $this->error('❌ Invalid action specified');
        $this->line('');
        $this->info('Available actions:');
        $this->line('  status     - Show queue status and statistics');
        $this->line('  start      - Start queue workers');
        $this->line('  stop       - Stop all queue workers');
        $this->line('  restart    - Restart queue workers');
        $this->line('  clear      - Clear all pending jobs from queue');
        $this->line('  stats      - Show detailed statistics');
        $this->line('');
        $this->info('Examples:');
        $this->line('  php artisan email-marketing:queue status');
        $this->line('  php artisan email-marketing:queue start --workers=2 --timeout=120');
        $this->line('  php artisan email-marketing:queue clear');

        return Command::FAILURE;
    }

    /**
     * Get count of jobs by status.
     */
    private function getJobsCount(string $status): int
    {
        return match ($status) {
            'total' => DB::table('jobs')->where('queue', 'email-marketing')->count(),
            'pending' => DB::table('jobs')->where('queue', 'email-marketing')->whereNull('reserved_at')->count(),
            'processing' => DB::table('jobs')->where('queue', 'email-marketing')->whereNotNull('reserved_at')->count(),
            'failed' => DB::table('failed_jobs')->where('payload', 'like', '%SendEmailCampaign%')->count(),
            default => 0
        };
    }

    /**
     * Get completed jobs in last 24 hours.
     */
    private function getCompletedJobsLast24h(): int
    {
        // This is an approximation since completed jobs are typically removed from the jobs table
        // We could implement a separate tracking table for more accurate metrics
        return EmailCampaign::where('status', 'sent')
            ->where('completed_at', '>=', now()->subDay())
            ->count();
    }
}
