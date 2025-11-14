<?php

declare(strict_types=1);

namespace App\Actions\Tenant\Team;

use App\Models\Tenant\TeamInvitation;

class CancelTeamInvitationAction
{
    public function __invoke(TeamInvitation $invitation): void
    {
        if ($invitation->isAccepted()) {
            throw new \Exception('Cannot cancel an invitation that has already been accepted.');
        }

        $invitation->delete();
    }
}
