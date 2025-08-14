<?php

namespace App\Jobs;

use App\Models\EmailCampaign;
use App\Models\Lead;
use App\Services\EmailMarketingService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendEmailCampaign implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 300; // 5 minutes
    public $tries = 3;
    public $queue = 'email-marketing';

    protected EmailCampaign $campaign;
    protected ?int $batchSize;

    /**
     * Create a new job instance.
     */
    public function __construct(EmailCampaign $campaign, ?int $batchSize = null)
    {
        $this->campaign = $campaign;
        $this->batchSize = $batchSize ?: config('emailmarketing.queue.batch_size', 100);
        $this->onQueue('email-marketing');
    }

    /**
     * Execute the job.
     */
    public function handle(EmailMarketingService $emailService): void
    {
        // Check if campaign can still be sent
        if (!in_array($this->campaign->status, ['sending', 'scheduled'])) {
            Log::info("Campaign {$this->campaign->id} is no longer in sending status, aborting.");
            return;
        }

        try {
            $this->processCampaign($emailService);
        } catch (\Exception $e) {
            Log::error("Error processing campaign {$this->campaign->id}: " . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Process the email campaign.
     */
    private function processCampaign(EmailMarketingService $emailService): void
    {
        // Ensure campaign is in sending status
        if ($this->campaign->status !== 'sending') {
            $this->campaign->start();
        }

        // Get recipients for this campaign in batches
        $query = $this->campaign->buildRecipientsQuery();
        $totalRecipients = $query->count();

        Log::info("Processing campaign {$this->campaign->id} for {$totalRecipients} recipients in batches of {$this->batchSize}");

        $successCount = 0;
        $failureCount = 0;
        $processed = 0;

        // Process recipients in batches
        $query->chunk($this->batchSize, function ($recipients) use ($emailService, &$successCount, &$failureCount, &$processed, $totalRecipients) {
            foreach ($recipients as $recipient) {
                // Check if campaign was paused or cancelled
                $this->campaign->refresh();
                if (in_array($this->campaign->status, ['paused', 'cancelled'])) {
                    Log::info("Campaign {$this->campaign->id} was paused/cancelled, stopping processing.");
                    return false; // Stop chunk processing
                }

                try {
                    $success = $emailService->sendCampaignEmail($this->campaign, $recipient);
                    if ($success) {
                        $successCount++;
                    } else {
                        $failureCount++;
                    }
                } catch (\Exception $e) {
                    Log::error("Failed to send email to {$recipient->email} for campaign {$this->campaign->id}: " . $e->getMessage());
                    $failureCount++;
                }

                $processed++;
                
                // Log progress every 50 emails
                if ($processed % 50 === 0) {
                    Log::info("Campaign {$this->campaign->id} progress: {$processed}/{$totalRecipients} emails processed");
                }
            }
            
            return true; // Continue processing
        });

        // Update campaign status if not paused/cancelled
        $this->campaign->refresh();
        if ($this->campaign->status === 'sending') {
            $this->campaign->complete();
            Log::info("Campaign {$this->campaign->id} completed. Success: {$successCount}, Failures: {$failureCount}");
        }
    }


    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("SendEmailCampaign job failed for campaign {$this->campaign->id}: " . $exception->getMessage());
        
        // Mark campaign as failed if it's still in sending status
        if ($this->campaign->status === 'sending') {
            $this->campaign->update([
                'status' => 'paused' // Pause instead of fail so it can be resumed
            ]);
        }
    }
}