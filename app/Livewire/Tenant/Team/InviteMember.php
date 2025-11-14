<?php

declare(strict_types=1);

namespace App\Livewire\Tenant\Team;

use App\Actions\Tenant\Team\SendTeamInvitationAction;
use App\Data\Tenant\Team\SendTeamInvitationData;
use App\Enums\UserRole;
use Livewire\Attributes\Validate;
use Livewire\Component;

class InviteMember extends Component
{
    #[Validate('required|email|max:255')]
    public string $email = '';

    #[Validate('required|in:owner,admin,member')]
    public string $role = 'member';

    public array $customPermissions = [];

    public function save()
    {
        // Check permission
        if (!auth()->user()->can('team.manage')) {
            $this->dispatch('notification', [
                'type' => 'error',
                'message' => 'You do not have permission to invite team members.',
            ]);
            return;
        }

        $this->validate();

        try {
            $data = SendTeamInvitationData::from([
                'email' => $this->email,
                'role' => UserRole::from($this->role),
                'permissions' => !empty($this->customPermissions) ? $this->customPermissions : null,
            ]);

            $action = new SendTeamInvitationAction();
            $invitation = $action($data, auth()->user());

            $this->dispatch('notification', [
                'type' => 'success',
                'message' => 'Invitation sent successfully to ' . $this->email,
            ]);

            return $this->redirect(route('team.index'), navigate: true);
        } catch (\Exception $e) {
            $this->dispatch('notification', [
                'type' => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function render()
    {
        return view('livewire.tenant.team.invite-member')->layout('layouts.tenant', [
            'title' => 'Invite Team Member',
            'header' => 'Invite Team Member',
        ]);
    }
}
