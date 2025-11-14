<?php

namespace App\Livewire\Tenant\Projects;

use App\Actions\Tenant\Projects\DeleteProjectAction;
use App\Enums\ProjectStatus;
use App\Models\Tenant\Project;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class ProjectList extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url]
    public string $status = 'active';

    #[Url]
    public string $priority = 'all';

    #[Url]
    public string $sortBy = 'created_at';

    #[Url]
    public string $sortDirection = 'desc';

    public bool $showFilters = false;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function updatingPriority(): void
    {
        $this->resetPage();
    }

    public function archive(string $projectId): void
    {
        $project = Project::findOrFail($projectId);

        $action = new DeleteProjectAction();
        $action($project);

        session()->flash('success', 'Project archived successfully.');
    }

    public function render()
    {
        $projects = Project::query()
            ->with(['client'])
            ->when($this->search, fn($q) =>
                $q->where('name', 'ilike', "%{$this->search}%")
                    ->orWhere('description', 'ilike', "%{$this->search}%")
                    ->orWhereHas('client', function ($query) {
                        $query->where('name', 'ilike', "%{$this->search}%");
                    })
            )
            ->when($this->status === 'archived', fn($q) =>
                $q->whereNotNull('archived_at')
            )
            ->when($this->status !== 'archived' && $this->status !== 'all', fn($q) =>
                $q->whereNull('archived_at')
                    ->where('status', $this->status)
            )
            ->when($this->status === 'active', fn($q) =>
                $q->whereNull('archived_at')
                    ->whereIn('status', [ProjectStatus::PLANNING->value, ProjectStatus::IN_PROGRESS->value])
            )
            ->when($this->priority !== 'all', fn($q) =>
                $q->where('priority', $this->priority)
            )
            ->withCount(['websites'])
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(24);

        return view('livewire.tenant.projects.project-list', [
            'projects' => $projects,
        ])->layout('layouts.tenant', ['header' => 'Projects']);
    }
}
