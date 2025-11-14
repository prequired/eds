<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\Tenant\TeamInvitation;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TeamInvitationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public TeamInvitation $invitation,
        public string $organizationName
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $inviterName = $this->invitation->invitedBy->name;
        $acceptUrl = url("/invitations/accept/{$this->invitation->token}");

        return (new MailMessage)
            ->subject("You've been invited to join {$this->organizationName}")
            ->greeting("Hello!")
            ->line("{$inviterName} has invited you to join **{$this->organizationName}** on Edison Tech Platform.")
            ->line("**Role:** {$this->invitation->role->label()}")
            ->line("This invitation will expire in 7 days.")
            ->action('Accept Invitation', $acceptUrl)
            ->line('If you did not expect this invitation, no further action is required.');
    }
}
