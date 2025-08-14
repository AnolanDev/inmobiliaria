<?php

namespace App\Services;

use App\Models\EmailCampaign;
use App\Models\EmailTemplate;
use App\Models\EmailSend;
use App\Models\Lead;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Carbon\Carbon;

class EmailMarketingService
{
    /**
     * Send a single email for a campaign.
     */
    public function sendCampaignEmail(EmailCampaign $campaign, Lead $lead): bool
    {
        try {
            // Check rate limiting
            if (!$this->checkRateLimit()) {
                Log::warning('Rate limit exceeded for email marketing');
                return false;
            }

            // Create email send record
            $emailSend = EmailSend::create([
                'email_campaign_id' => $campaign->id,
                'recipient_type' => Lead::class,
                'recipient_id' => $lead->id,
                'recipient_email' => $lead->email,
                'recipient_name' => $lead->full_name,
                'subject' => $campaign->subject_override ?: $campaign->emailTemplate->subject,
                'status' => 'queued',
                'tracking_token' => Str::uuid(),
            ]);

            // Get template and render content
            $template = $campaign->emailTemplate;
            $variables = $this->getVariablesForLead($lead, $emailSend);
            $renderedContent = $template->renderContent($variables);

            // Override subject if campaign has custom subject
            if ($campaign->subject_override) {
                $renderedContent['subject'] = $this->processVariables($campaign->subject_override, $variables);
            }

            // Add tracking and unsubscribe
            $renderedContent = $this->addTracking($renderedContent, $emailSend);

            // Send email
            $this->sendEmail(
                $lead->email,
                $lead->full_name,
                $renderedContent['subject'],
                $renderedContent['html_content'],
                $renderedContent['text_content'] ?? strip_tags($renderedContent['html_content'])
            );

            // Update email send record
            $emailSend->update([
                'status' => 'sent',
                'sent_at' => now(),
                'subject' => $renderedContent['subject'],
                'html_content' => $renderedContent['html_content'],
                'text_content' => $renderedContent['text_content'] ?? strip_tags($renderedContent['html_content']),
            ]);

            // Update campaign counters
            $campaign->increment('emails_sent');

            // Apply rate limiting delay
            $this->applyRateLimit();

            return true;

        } catch (\Exception $e) {
            Log::error('Failed to send campaign email', [
                'campaign_id' => $campaign->id,
                'lead_id' => $lead->id,
                'error' => $e->getMessage()
            ]);

            // Update email send record with error
            if (isset($emailSend)) {
                $emailSend->update([
                    'status' => 'failed',
                    'error_message' => $e->getMessage(),
                    'failed_at' => now(),
                ]);
            }

            return false;
        }
    }

    /**
     * Send a test email.
     */
    public function sendTestEmail(EmailTemplate $template, string $email, array $variables = []): bool
    {
        try {
            $variables = array_merge($this->getDefaultVariables(), $variables);
            $renderedContent = $template->renderContent($variables);

            $this->sendEmail(
                $email,
                'Test Recipient',
                '[TEST] ' . $renderedContent['subject'],
                $renderedContent['html_content'],
                $renderedContent['text_content'] ?? strip_tags($renderedContent['html_content'])
            );

            return true;

        } catch (\Exception $e) {
            Log::error('Failed to send test email', [
                'template_id' => $template->id,
                'email' => $email,
                'error' => $e->getMessage()
            ]);

            return false;
        }
    }

    /**
     * Send email using configured provider.
     */
    private function sendEmail(string $email, string $name, string $subject, string $htmlContent, string $textContent = null): void
    {
        $fromEmail = config('emailmarketing.defaults.from_email') ?: config('mail.from.address', 'noreply@inmoapp.com');
        $fromName = config('emailmarketing.defaults.from_name') ?: config('mail.from.name', config('app.name', 'InmoApp'));
        $replyTo = config('emailmarketing.defaults.reply_to') ?: $fromEmail;

        // Ensure we have valid email addresses
        if (empty($fromEmail)) {
            $fromEmail = 'noreply@inmoapp.com';
        }
        if (empty($fromName)) {
            $fromName = config('app.name', 'InmoApp');
        }

        Mail::html($htmlContent, function ($message) use ($email, $name, $subject, $textContent, $fromEmail, $fromName, $replyTo) {
            $message->to($email, $name)
                   ->from($fromEmail, $fromName)
                   ->replyTo($replyTo)
                   ->subject($subject);

            if ($textContent) {
                $message->text($textContent);
            }

            // Add List-Unsubscribe header for compliance
            if (config('emailmarketing.compliance.list_unsubscribe_header')) {
                $unsubscribeUrl = url('/unsubscribe/' . Str::random(32));
                $message->getHeaders()->addTextHeader('List-Unsubscribe', "<{$unsubscribeUrl}>");
                $message->getHeaders()->addTextHeader('List-Unsubscribe-Post', 'List-Unsubscribe=One-Click');
            }
        });
    }

    /**
     * Add tracking pixels and links to email content.
     */
    private function addTracking(array $content, EmailSend $emailSend): array
    {
        if (!config('emailmarketing.tracking.enabled')) {
            return $content;
        }

        $trackingToken = $emailSend->tracking_token;

        // Add open tracking pixel
        if (config('emailmarketing.tracking.open_tracking')) {
            $trackingPixel = '<img src="' . route('email.track', $trackingToken) . '" width="1" height="1" style="display:block" />';
            $content['html_content'] = str_replace('</body>', $trackingPixel . '</body>', $content['html_content']);
        }

        // Add click tracking
        if (config('emailmarketing.tracking.click_tracking')) {
            $content['html_content'] = $this->addClickTracking($content['html_content'], $trackingToken);
        }

        // Add unsubscribe link if not present
        if (config('emailmarketing.compliance.unsubscribe_footer')) {
            $unsubscribeUrl = route('email.unsubscribe.form', $trackingToken);
            $unsubscribeFooter = '<hr style="margin: 20px 0;"><p style="font-size: 12px; color: #666; text-align: center;">
                Si no deseas recibir más emails, <a href="' . $unsubscribeUrl . '">haz clic aquí para darte de baja</a>.
            </p>';
            
            if (!str_contains($content['html_content'], 'unsubscribe')) {
                $content['html_content'] = str_replace('</body>', $unsubscribeFooter . '</body>', $content['html_content']);
            }
        }

        return $content;
    }

    /**
     * Add click tracking to links in HTML content.
     */
    private function addClickTracking(string $html, string $trackingToken): string
    {
        return preg_replace_callback(
            '/<a\s+[^>]*href=["\']([^"\']+)["\'][^>]*>/i',
            function ($matches) use ($trackingToken) {
                $originalUrl = $matches[1];
                
                // Skip unsubscribe and tracking URLs
                if (str_contains($originalUrl, 'unsubscribe') || str_contains($originalUrl, 'email/track')) {
                    return $matches[0];
                }

                $trackingUrl = route('email.click', [
                    'token' => $trackingToken,
                    'url' => base64_encode($originalUrl)
                ]);

                return str_replace($originalUrl, $trackingUrl, $matches[0]);
            },
            $html
        );
    }

    /**
     * Get variables for a specific lead.
     */
    private function getVariablesForLead(Lead $lead, EmailSend $emailSend = null): array
    {
        $variables = $this->getDefaultVariables();

        // Lead variables
        $variables = array_merge($variables, [
            'recipient_name' => $lead->full_name,
            'recipient_email' => $lead->email,
            'lead_first_name' => $lead->first_name,
            'lead_last_name' => $lead->last_name,
            'lead_full_name' => $lead->full_name,
            'lead_status' => $lead->status_label,
            'lead_source' => $lead->source_label,
            'lead_budget_min' => $lead->budget_min ? '$' . number_format($lead->budget_min) : '',
            'lead_budget_max' => $lead->budget_max ? '$' . number_format($lead->budget_max) : '',
            'lead_interests' => $lead->interests ?: '',
            'lead_phone' => $lead->phone ?: '',
            'lead_created_date' => $lead->created_at->format('d/m/Y'),
        ]);

        // Agent variables
        if ($lead->agent) {
            $variables['assigned_agent_name'] = $lead->agent->full_name;
            $variables['assigned_agent_email'] = $lead->agent->email;
            $variables['assigned_agent_phone'] = $lead->agent->phone ?: '';
        }

        // Unsubscribe token
        if ($emailSend) {
            $variables['unsubscribe_url'] = route('email.unsubscribe.form', $emailSend->tracking_token);
        }

        return $variables;
    }

    /**
     * Get default template variables.
     */
    private function getDefaultVariables(): array
    {
        return array_merge(config('emailmarketing.defaults.template_variables', []), [
            'current_date' => now()->format('d/m/Y'),
            'current_time' => now()->format('H:i'),
            'current_year' => now()->year,
            'current_month' => now()->format('F'),
            'current_day' => now()->format('l'),
        ]);
    }

    /**
     * Process variables in a string.
     */
    private function processVariables(string $content, array $variables): string
    {
        foreach ($variables as $key => $value) {
            $content = str_replace('{{' . $key . '}}', $value, $content);
        }

        return $content;
    }

    /**
     * Check if we can send more emails (rate limiting).
     */
    private function checkRateLimit(): bool
    {
        if (!config('emailmarketing.rate_limiting.enabled')) {
            return true;
        }

        $minute = now()->format('Y-m-d H:i');
        $hour = now()->format('Y-m-d H');
        $day = now()->format('Y-m-d');

        $emailsThisMinute = Cache::get("email_marketing:rate_limit:minute:{$minute}", 0);
        $emailsThisHour = Cache::get("email_marketing:rate_limit:hour:{$hour}", 0);
        $emailsThisDay = Cache::get("email_marketing:rate_limit:day:{$day}", 0);

        return $emailsThisMinute < config('emailmarketing.rate_limiting.emails_per_minute') &&
               $emailsThisHour < config('emailmarketing.rate_limiting.emails_per_hour') &&
               $emailsThisDay < config('emailmarketing.rate_limiting.emails_per_day');
    }

    /**
     * Apply rate limiting by incrementing counters and adding delay.
     */
    private function applyRateLimit(): void
    {
        if (!config('emailmarketing.rate_limiting.enabled')) {
            return;
        }

        $minute = now()->format('Y-m-d H:i');
        $hour = now()->format('Y-m-d H');
        $day = now()->format('Y-m-d');

        // Increment counters
        Cache::increment("email_marketing:rate_limit:minute:{$minute}");
        Cache::increment("email_marketing:rate_limit:hour:{$hour}");
        Cache::increment("email_marketing:rate_limit:day:{$day}");

        // Set expiration times
        Cache::put("email_marketing:rate_limit:minute:{$minute}", Cache::get("email_marketing:rate_limit:minute:{$minute}"), 60);
        Cache::put("email_marketing:rate_limit:hour:{$hour}", Cache::get("email_marketing:rate_limit:hour:{$hour}"), 3600);
        Cache::put("email_marketing:rate_limit:day:{$day}", Cache::get("email_marketing:rate_limit:day:{$day}"), 86400);

        // Apply delay
        $delay = config('emailmarketing.rate_limiting.delay_between_emails', 1);
        if ($delay > 0) {
            sleep($delay);
        }
    }

    /**
     * Get email marketing statistics.
     */
    public function getStats(): array
    {
        $today = now()->startOfDay();
        $thisWeek = now()->startOfWeek();
        $thisMonth = now()->startOfMonth();

        $totalEmailsSent = EmailSend::where('status', 'sent')->count();
        $deliveryRate = $this->calculateDeliveryRate();
        $openRate = $this->calculateOpenRate();
        $clickRate = $this->calculateClickRate();

        return [
            'emails_sent_today' => EmailSend::where('status', 'sent')
                ->where('sent_at', '>=', $today)
                ->count(),
            
            'emails_sent_this_week' => EmailSend::where('status', 'sent')
                ->where('sent_at', '>=', $thisWeek)
                ->count(),
            
            'emails_sent_this_month' => EmailSend::where('status', 'sent')
                ->where('sent_at', '>=', $thisMonth)
                ->count(),
            
            'total_emails_sent' => $totalEmailsSent,
            'total_campaigns' => EmailCampaign::count(),
            'active_campaigns' => EmailCampaign::whereIn('status', ['sending', 'scheduled'])->count(),
            'total_templates' => EmailTemplate::count(),
            'total_subscribers' => Lead::where('unsubscribed', false)->count(),
            
            'delivery_rate' => $deliveryRate . '%',
            'open_rate' => $openRate . '%',
            'click_rate' => $clickRate . '%',
        ];
    }

    /**
     * Calculate delivery rate percentage.
     */
    private function calculateDeliveryRate(): float
    {
        $totalAttempted = EmailSend::whereIn('status', ['sent', 'failed', 'bounced'])->count();
        $delivered = EmailSend::where('status', 'sent')->count();

        return $totalAttempted > 0 ? round(($delivered / $totalAttempted) * 100, 2) : 0;
    }

    /**
     * Calculate open rate percentage.
     */
    private function calculateOpenRate(): float
    {
        $delivered = EmailSend::where('status', 'sent')->count();
        $opened = EmailSend::where('status', 'sent')->where('opened', true)->count();

        return $delivered > 0 ? round(($opened / $delivered) * 100, 2) : 0;
    }

    /**
     * Calculate click rate percentage.
     */
    private function calculateClickRate(): float
    {
        $delivered = EmailSend::where('status', 'sent')->count();
        $clicked = EmailSend::where('status', 'sent')->where('clicked', true)->count();

        return $delivered > 0 ? round(($clicked / $delivered) * 100, 2) : 0;
    }
}