<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\Tenant\WebsiteUptimeChanged;
use App\Notifications\WebsiteDownNotification;
use App\Notifications\WebsiteRecoveredNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;

class SendWebsiteUptimeNotifications implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(WebsiteUptimeChanged $event): void
    {
        $website = $event->website;

        // Skip if no notification emails configured
        if (empty($website->notification_emails)) {
            return;
        }

        // Handle website going down
        if ($event->wentDown() && $website->notify_on_downtime) {
            $this->sendDowntimeNotification($event);
        }

        // Handle website recovering
        if ($event->cameUp() && $website->notify_on_recovery) {
            $this->sendRecoveryNotification($event);
        }

        // Update last notified timestamp
        $website->update(['last_notified_at' => now()]);
    }

    /**
     * Send downtime notification.
     */
    protected function sendDowntimeNotification(WebsiteUptimeChanged $event): void
    {
        $website = $event->website;

        // Get latest uptime check for error details
        $latestCheck = $website->uptimeChecks()->first();

        Notification::route('mail', $website->notification_emails)
            ->notify(new WebsiteDownNotification(
                website: $website,
                errorMessage: $latestCheck?->error_message,
                responseTimeMs: $website->response_time_ms
            ));
    }

    /**
     * Send recovery notification.
     */
    protected function sendRecoveryNotification(WebsiteUptimeChanged $event): void
    {
        $website = $event->website;

        // Calculate downtime duration (time since last DOWN check)
        $lastDownCheck = $website->uptimeChecks()
            ->where('status', 'down')
            ->first();

        $downtimeStart = $lastDownCheck?->checked_at;

        Notification::route('mail', $website->notification_emails)
            ->notify(new WebsiteRecoveredNotification(
                website: $website,
                responseTimeMs: $website->response_time_ms,
                downtimeStart: $downtimeStart
            ));
    }
}
