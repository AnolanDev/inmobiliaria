<?php

namespace App\Console\Commands;

use App\Models\EmailCampaign;
use App\Jobs\SendEmailCampaign;
use App\Services\EmailMarketingService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ProcessScheduledCampaigns extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email-marketing:process-scheduled
                            {--force : Force processing even if conditions are not met}
                            {--campaign= : Process specific campaign ID}
                            {--dry-run : Show what would be processed without actually doing it}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process scheduled email campaigns and send them';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🚀 Starting Email Marketing Scheduled Campaigns Processor...');

        $isDryRun = $this->option('dry-run');
        $forceProcess = $this->option('force');
        $specificCampaign = $this->option('campaign');

        try {
            if ($specificCampaign) {
                $this->processCampaign($specificCampaign, $isDryRun, $forceProcess);
            } else {
                $this->processScheduledCampaigns($isDryRun, $forceProcess);
            }

            $this->info('✅ Email Marketing processing completed successfully!');
            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('❌ Error processing email campaigns: ' . $e->getMessage());
            Log::error('ProcessScheduledCampaigns failed: ' . $e->getMessage(), [
                'exception' => $e
            ]);
            return Command::FAILURE;
        }
    }

    /**
     * Process all scheduled campaigns that are ready to send.
     */
    private function processScheduledCampaigns(bool $isDryRun, bool $forceProcess): void
    {
        $now = Carbon::now();
        
        $query = EmailCampaign::where('status', 'scheduled')
            ->where('scheduled_at', '<=', $now);

        if (!$forceProcess) {
            // Only process if email marketing is enabled
            if (!config('emailmarketing.enabled', true)) {
                $this->warn('⚠️  Email marketing is disabled in configuration');
                return;
            }
        }

        $campaigns = $query->get();

        if ($campaigns->isEmpty()) {
            $this->info('📭 No scheduled campaigns ready to process');
            return;
        }

        $this->info("📧 Found {$campaigns->count()} scheduled campaigns ready to process:");

        $bar = $this->output->createProgressBar($campaigns->count());
        $bar->start();

        foreach ($campaigns as $campaign) {
            $this->line('');
            $this->processIndividualCampaign($campaign, $isDryRun, $forceProcess);
            $bar->advance();
        }

        $bar->finish();
        $this->line('');
    }

    /**
     * Process a specific campaign by ID.
     */
    private function processCampaign(string $campaignId, bool $isDryRun, bool $forceProcess): void
    {
        $campaign = EmailCampaign::find($campaignId);

        if (!$campaign) {
            $this->error("❌ Campaign with ID {$campaignId} not found");
            return;
        }

        $this->info("🎯 Processing specific campaign: {$campaign->name} (ID: {$campaign->id})");
        $this->processIndividualCampaign($campaign, $isDryRun, $forceProcess);
    }

    /**
     * Process an individual campaign.
     */
    private function processIndividualCampaign(EmailCampaign $campaign, bool $isDryRun, bool $forceProcess): void
    {
        $this->info("📤 Campaign: {$campaign->name}");
        $this->line("   Type: {$campaign->type}");
        $this->line("   Status: {$campaign->status}");
        $this->line("   Scheduled: {$campaign->scheduled_at}");

        // Validate campaign can be sent
        if (!$forceProcess && !$this->canCampaignBeSent($campaign)) {
            $this->warn("   ⚠️  Campaign skipped - validation failed");
            return;
        }

        // Calculate recipients
        try {
            $recipientsCount = $campaign->buildRecipientsQuery()->count();
            $this->line("   Recipients: {$recipientsCount}");

            if ($recipientsCount === 0) {
                $this->warn("   ⚠️  No recipients found - skipping");
                return;
            }

        } catch (\Exception $e) {
            $this->error("   ❌ Error calculating recipients: " . $e->getMessage());
            return;
        }

        if ($isDryRun) {
            $this->info("   🔍 DRY RUN: Would process {$recipientsCount} recipients");
            return;
        }

        // Start campaign processing
        try {
            // Update campaign status to sending
            $campaign->update([
                'status' => 'sending',
                'started_at' => now(),
                'actual_recipients' => $recipientsCount
            ]);

            // Dispatch job to queue
            SendEmailCampaign::dispatch($campaign);

            $this->info("   ✅ Campaign queued for processing");
            
            Log::info("Scheduled campaign started", [
                'campaign_id' => $campaign->id,
                'campaign_name' => $campaign->name,
                'recipients_count' => $recipientsCount
            ]);

        } catch (\Exception $e) {
            $this->error("   ❌ Failed to start campaign: " . $e->getMessage());
            
            // Reset campaign status
            $campaign->update(['status' => 'scheduled']);
            
            Log::error("Failed to start scheduled campaign", [
                'campaign_id' => $campaign->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Check if campaign can be sent.
     */
    private function canCampaignBeSent(EmailCampaign $campaign): bool
    {
        // Check if campaign has template
        if (!$campaign->emailTemplate) {
            $this->error("   ❌ No email template assigned");
            return false;
        }

        // Check if template is active
        if ($campaign->emailTemplate->status !== 'active') {
            $this->error("   ❌ Email template is not active");
            return false;
        }

        // Check scheduled time
        if ($campaign->scheduled_at && $campaign->scheduled_at->isFuture()) {
            $this->warn("   ⚠️  Scheduled time is in the future");
            return false;
        }

        // Check if campaign is already sent or sending
        if (in_array($campaign->status, ['sent', 'sending'])) {
            $this->warn("   ⚠️  Campaign is already sent or sending");
            return false;
        }

        return true;
    }

    /**
     * Show system status and statistics.
     */
    private function showSystemStatus(): void
    {
        $service = app(EmailMarketingService::class);
        $stats = $service->getStats();

        $this->info('📊 Email Marketing System Status:');
        $this->table([
            'Metric', 'Value'
        ], [
            ['Emails sent today', $stats['emails_sent_today']],
            ['Emails sent this week', $stats['emails_sent_this_week']],
            ['Emails sent this month', $stats['emails_sent_this_month']],
            ['Total campaigns', $stats['total_campaigns']],
            ['Active campaigns', $stats['active_campaigns']],
            ['Total templates', $stats['total_templates']],
            ['Total subscribers', $stats['total_subscribers']],
            ['Delivery rate', $stats['delivery_rate'] . '%'],
            ['Open rate', $stats['open_rate'] . '%'],
            ['Click rate', $stats['click_rate'] . '%'],
        ]);
    }
}
