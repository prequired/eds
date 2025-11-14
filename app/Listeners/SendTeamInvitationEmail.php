<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Events\Tenant\TeamInvitationSent;
use App\Notifications\TeamInvitationNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;

class SendTeamInvitationEmail implements ShouldQueue
{
    public function __construct() {}

    public function handle(TeamInvitationSent $event): void
    {
        $invitation = $event->invitation;

        // Get organization name from tenant
        $organizationName = tenancy()->tenant?->company_name ?? 'Edison Tech Platform';

        // Send notification to the invited email
        Notification::route('mail', $invitation->email)
            ->notify(new TeamInvitationNotification($invitation, $organizationName));
    }
}
