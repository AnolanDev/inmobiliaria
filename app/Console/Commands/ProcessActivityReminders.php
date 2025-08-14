<?php

namespace App\Console\Commands;

use App\Models\Activity;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ProcessActivityReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'activities:process-reminders {--dry-run : Show what would be processed without sending}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process pending activity reminders and send notifications';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Processing activity reminders...');
        
        $isDryRun = $this->option('dry-run');
        $now = Carbon::now();
        
        // Get activities with reminders that are due
        $activities = Activity::with(['user', 'assignedUser', 'related'])
            ->where('has_reminder', true)
            ->where('reminder_sent', false)
            ->where('status', 'pending')
            ->whereNotNull('reminder_at')
            ->where('reminder_at', '<=', $now)
            ->get();

        if ($activities->isEmpty()) {
            $this->info('No reminders to process.');
            return Command::SUCCESS;
        }

        $this->info("Found {$activities->count()} reminders to process.");

        $processed = 0;
        $failed = 0;

        foreach ($activities as $activity) {
            try {
                if ($isDryRun) {
                    $this->line("Would process: {$activity->subject} (ID: {$activity->id})");
                    $processed++;
                    continue;
                }

                // Send notification (simplified - would integrate with your notification system)
                $this->sendReminderNotification($activity);
                
                // Mark reminder as sent
                $activity->update(['reminder_sent' => true]);
                
                $this->line("✓ Processed reminder for: {$activity->subject}");
                $processed++;
                
            } catch (\Exception $e) {
                $this->error("✗ Failed to process reminder for activity {$activity->id}: {$e->getMessage()}");
                Log::error('Activity reminder processing failed', [
                    'activity_id' => $activity->id,
                    'error' => $e->getMessage()
                ]);
                $failed++;
            }
        }

        $this->info("\nSummary:");
        $this->info("Processed: {$processed}");
        if ($failed > 0) {
            $this->warn("Failed: {$failed}");
        }

        return Command::SUCCESS;
    }

    /**
     * Send reminder notification for an activity
     */
    private function sendReminderNotification(Activity $activity)
    {
        $recipient = $activity->assignedUser ?: $activity->user;
        
        if (!$recipient || !$recipient->email) {
            throw new \Exception('No valid recipient email found');
        }

        // For now, log the notification (in production, you'd send actual emails/SMS)
        Log::info('Activity reminder notification', [
            'activity_id' => $activity->id,
            'activity_subject' => $activity->subject,
            'recipient_email' => $recipient->email,
            'scheduled_at' => $activity->scheduled_at,
            'reminder_at' => $activity->reminder_at
        ]);

        // Simulate email sending delay
        usleep(100000); // 0.1 seconds
    }
}
