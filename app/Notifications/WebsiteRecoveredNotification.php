<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Tenant\Website;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WebsiteRecoveredNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Website $website,
        public ?int $responseTimeMs = null,
        public ?\Carbon\Carbon $downtimeStart = null
    ) {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $mail = (new MailMessage)
            ->success()
            ->subject("✅ Website Recovered: {$this->website->name}")
            ->greeting("Good News! {$this->website->name} is back online")
            ->line("Your website **{$this->website->name}** has recovered and is now responding to health checks.")
            ->line("**URL:** {$this->website->url}")
            ->line("**Environment:** " . $this->website->environment->label());

        if ($this->responseTimeMs !== null) {
            $mail->line("**Response Time:** {$this->responseTimeMs}ms");
        }

        if ($this->downtimeStart) {
            $downtime = $this->downtimeStart->diffForHumans(now(), true);
            $mail->line("**Downtime Duration:** {$downtime}");
        }

        $mail->line("**Recovered At:** " . $this->website->last_checked_at->format('Y-m-d H:i:s T'))
            ->action('View Website Details', url('/websites/' . $this->website->id))
            ->line('Your website is now accessible and functioning normally.');

        return $mail;
    }

    /**
     * Get the database representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        return [
            'website_id' => $this->website->id,
            'website_name' => $this->website->name,
            'website_url' => $this->website->url,
            'environment' => $this->website->environment->value,
            'response_time_ms' => $this->responseTimeMs,
            'downtime_start' => $this->downtimeStart,
            'recovered_at' => $this->website->last_checked_at,
        ];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return $this->toDatabase($notifiable);
    }
}
