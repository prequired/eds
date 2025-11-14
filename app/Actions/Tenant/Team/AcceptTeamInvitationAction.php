<?php

declare(strict_types=1);

namespace App\Actions\Tenant\Team;

use App\Data\Tenant\Team\AcceptTeamInvitationData;
use App\Events\Tenant\TeamInvitationAccepted;
use App\Models\Tenant\TeamInvitation;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AcceptTeamInvitationAction
{
    public function __invoke(TeamInvitation $invitation, AcceptTeamInvitationData $data): User
    {
        // Validate invitation
        if ($invitation->isExpired()) {
            throw new \Exception('This invitation has expired.');
        }

        if ($invitation->isAccepted()) {
            throw new \Exception('This invitation has already been accepted.');
        }

        return DB::transaction(function () use ($invitation, $data) {
            // Check if user already exists
            $existingUser = User::where('email', $invitation->email)->first();
            if ($existingUser) {
                throw new \Exception('User with this email already exists.');
            }

            // Create user
            $user = User::create([
                'name' => $data->name,
                'email' => $invitation->email,
                'password' => Hash::make($data->password),
                'role' => $invitation->role,
                'permissions' => $invitation->permissions,
                'email_verified_at' => now(), // Auto-verify since they accepted invitation
            ]);

            // Mark invitation as accepted
            $invitation->update([
                'accepted_at' => now(),
            ]);

            // Dispatch event
            TeamInvitationAccepted::dispatch($invitation, $user);

            return $user;
        });
    }
}
