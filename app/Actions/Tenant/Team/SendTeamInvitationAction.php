<?php

declare(strict_types=1);

namespace App\Actions\Tenant\Team;

use App\Data\Tenant\Team\SendTeamInvitationData;
use App\Events\Tenant\TeamInvitationSent;
use App\Models\Tenant\TeamInvitation;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class SendTeamInvitationAction
{
    public function __invoke(SendTeamInvitationData $data, User $invitedBy): TeamInvitation
    {
        return DB::transaction(function () use ($data, $invitedBy) {
            // Check if user already exists
            $existingUser = User::where('email', $data->email)->first();
            if ($existingUser) {
                throw new \Exception('User with this email already exists in this organization.');
            }

            // Check if there's already a pending invitation
            $existingInvitation = TeamInvitation::where('email', $data->email)
                ->pending()
                ->first();

            if ($existingInvitation) {
                throw new \Exception('An invitation has already been sent to this email address.');
            }

            // Create invitation
            $invitation = TeamInvitation::create([
                'email' => $data->email,
                'role' => $data->role,
                'permissions' => $data->permissions,
                'invited_by' => $invitedBy->id,
                'token' => Str::random(64),
                'expires_at' => now()->addDays(7), // 7-day expiration
            ]);

            // Dispatch event
            TeamInvitationSent::dispatch($invitation);

            return $invitation;
        });
    }
}
