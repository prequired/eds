<?php

declare(strict_types=1);

namespace App\Livewire\Tenant\TimeEntry;

use App\Actions\Tenant\TimeEntry\DeleteTimeEntryAction;
use App\Enums\TimeEntryStatus;
use App\Models\Tenant\Client;
use App\Models\Tenant\Project;
use App\Models\Tenant\TimeEntry;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class TimeEntryList extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'project')]
    public string $projectFilter = 'all';

    #[Url(as: 'client')]
    public string $clientFilter = 'all';

    #[Url(as: 'billable')]
    public string $billableFilter = 'all';

    #[Url(as: 'status')]
    public string $statusFilter = 'all';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function deleteEntry(string $entryId)
    {
        if (!auth()->user()->can('time_entries.delete')) {
            $this->dispatch('notification', [
                'type' => 'error',
                'message' => 'You do not have permission to delete time entries.',
            ]);
            return;
        }

        $entry = TimeEntry::findOrFail($entryId);

        // Users can only delete their own entries
        if ($entry->user_id !== auth()->id() && !auth()->user()->isOwner() && !auth()->user()->isAdmin()) {
            $this->dispatch('notification', [
                'type' => 'error',
                'message' => 'You can only delete your own time entries.',
            ]);
            return;
        }

        try {
            $action = new DeleteTimeEntryAction();
            $action($entry);

            $this->dispatch('notification', [
                'type' => 'success',
                'message' => 'Time entry deleted successfully.',
            ]);
        } catch (\Exception $e) {
            $this->dispatch('notification', [
                'type' => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }

    #[On('timer-stopped')]
    public function refreshEntries()
    {
        // Just trigger re-render
    }

    public function render()
    {
        $query = TimeEntry::with(['user', 'project', 'client'])
            ->when($this->search, function ($q) {
                $q->where('description', 'ilike', "%{$this->search}%");
            })
            ->when($this->projectFilter !== 'all', function ($q) {
                $q->where('project_id', $this->projectFilter);
            })
            ->when($this->clientFilter !== 'all', function ($q) {
                $q->where('client_id', $this->clientFilter);
            })
            ->when($this->billableFilter !== 'all', function ($q) {
                $q->where('billable', $this->billableFilter === 'yes');
            })
            ->when($this->statusFilter !== 'all', function ($q) {
                $q->where('status', $this->statusFilter);
            });

        // Non-admin users only see their own entries
        if (!auth()->user()->isOwner() && !auth()->user()->isAdmin()) {
            $query->forUser(auth()->id());
        }

        $entries = $query->orderBy('start_time', 'desc')->paginate(20);

        // Calculate totals
        $totalHours = $entries->sum('duration_in_hours');
        $totalAmount = $entries->sum('total_amount');

        $projects = Project::orderBy('name')->get();
        $clients = Client::orderBy('name')->get();
        $statuses = TimeEntryStatus::cases();

        return view('livewire.tenant.time-entry.time-entry-list', [
            'entries' => $entries,
            'totalHours' => $totalHours,
            'totalAmount' => $totalAmount,
            'projects' => $projects,
            'clients' => $clients,
            'statuses' => $statuses,
        ])->layout('layouts.tenant', [
            'title' => 'Time Tracking',
            'header' => 'Time Tracking',
        ]);
    }
}
