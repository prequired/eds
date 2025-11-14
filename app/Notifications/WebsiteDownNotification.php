<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Tenant\Website;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class WebsiteDownNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Website $website,
        public ?string $errorMessage = null,
        public ?int $responseTimeMs = null
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
            ->error()
            ->subject("🔴 Website Down: {$this->website->name}")
            ->greeting("Website Alert: {$this->website->name}")
            ->line("Your website **{$this->website->name}** is currently down and not responding to health checks.")
            ->line("**URL:** {$this->website->url}")
            ->line("**Environment:** " . $this->website->environment->label());

        if ($this->errorMessage) {
            $mail->line("**Error:** {$this->errorMessage}");
        }

        if ($this->responseTimeMs !== null) {
            $mail->line("**Response Time:** {$this->responseTimeMs}ms");
        }

        $mail->line("**Checked At:** " . $this->website->last_checked_at->format('Y-m-d H:i:s T'))
            ->action('View Website Details', url('/websites/' . $this->website->id))
            ->line('Please investigate this issue as soon as possible to restore service.');

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
            'error_message' => $this->errorMessage,
            'response_time_ms' => $this->responseTimeMs,
            'checked_at' => $this->website->last_checked_at,
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
