<?php

declare(strict_types=1);

namespace App\Livewire\Tenant\Team;

use App\Actions\Tenant\Team\CancelTeamInvitationAction;
use App\Models\Tenant\TeamInvitation;
use App\Models\User;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class TeamList extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $tab = 'members'; // members or invitations

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function cancelInvitation(string $invitationId): void
    {
        $invitation = TeamInvitation::findOrFail($invitationId);

        // Check permission
        if (!auth()->user()->can('team.manage')) {
            $this->dispatch('notification', [
                'type' => 'error',
                'message' => 'You do not have permission to cancel invitations.',
            ]);
            return;
        }

        $action = new CancelTeamInvitationAction();
        $action($invitation);

        $this->dispatch('notification', [
            'type' => 'success',
            'message' => 'Invitation cancelled successfully.',
        ]);
    }

    public function resendInvitation(string $invitationId): void
    {
        $invitation = TeamInvitation::findOrFail($invitationId);

        // Check permission
        if (!auth()->user()->can('team.manage')) {
            $this->dispatch('notification', [
                'type' => 'error',
                'message' => 'You do not have permission to resend invitations.',
            ]);
            return;
        }

        // Re-dispatch the invitation sent event to send email again
        \App\Events\Tenant\TeamInvitationSent::dispatch($invitation);

        $this->dispatch('notification', [
            'type' => 'success',
            'message' => 'Invitation resent successfully.',
        ]);
    }

    public function render()
    {
        $members = User::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'ilike', "%{$this->search}%")
                        ->orWhere('email', 'ilike', "%{$this->search}%");
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $invitations = TeamInvitation::query()
            ->pending()
            ->when($this->search, function ($query) {
                $query->where('email', 'ilike', "%{$this->search}%");
            })
            ->with('invitedBy')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('livewire.tenant.team.team-list', [
            'members' => $members,
            'invitations' => $invitations,
        ])->layout('layouts.tenant', [
            'title' => 'Team',
            'header' => 'Team Management',
        ]);
    }
}
