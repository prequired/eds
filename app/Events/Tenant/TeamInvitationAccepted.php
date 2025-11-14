<?php

declare(strict_types=1);

namespace App\Events\Tenant;

use App\Models\Tenant\TeamInvitation;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TeamInvitationAccepted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public TeamInvitation $invitation,
        public User $user
    ) {}
}
