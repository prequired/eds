<?php

namespace App\Livewire\Tenant\Projects;

use App\Actions\Tenant\Projects\CreateProjectAction;
use App\Actions\Tenant\Projects\UpdateProjectAction;
use App\Data\Tenant\Projects\CreateProjectData;
use App\Data\Tenant\Projects\UpdateProjectData;
use App\Enums\ProjectPriority;
use App\Enums\ProjectStatus;
use App\Models\Tenant\Client;
use App\Models\Tenant\Project;
use Livewire\Attributes\Validate;
use Livewire\Component;

class ProjectForm extends Component
{
    public ?string $projectId = null;

    #[Validate('required|uuid|exists:clients,id')]
    public string $client_id = '';

    #[Validate('required|min:2|max:255')]
    public string $name = '';

    #[Validate('nullable|max:5000')]
    public ?string $description = null;

    #[Validate('nullable')]
    public ?string $status = null;

    #[Validate('nullable')]
    public ?string $priority = null;

    #[Validate('nullable|numeric|min:0')]
    public ?string $budget = null;

    #[Validate('nullable|max:3')]
    public string $currency = 'USD';

    #[Validate('nullable|numeric|min:0')]
    public ?string $estimated_hours = null;

    #[Validate('nullable|date')]
    public ?string $starts_at = null;

    #[Validate('nullable|date')]
    public ?string $due_at = null;

    #[Validate('nullable|boolean')]
    public bool $billable = true;

    #[Validate('nullable|numeric|min:0')]
    public ?string $hourly_rate = null;

    public function mount(?string $projectId = null): void
    {
        $this->projectId = $projectId;

        if ($projectId) {
            $project = Project::findOrFail($projectId);

            $this->client_id = $project->client_id;
            $this->name = $project->name;
            $this->description = $project->description;
            $this->status = $project->status->value;
            $this->priority = $project->priority->value;
            $this->budget = $project->budget_cents ? $project->budget_cents / 100 : null;
            $this->currency = $project->currency ?? 'USD';
            $this->estimated_hours = $project->estimated_hours;
            $this->starts_at = $project->starts_at?->format('Y-m-d');
            $this->due_at = $project->due_at?->format('Y-m-d');
            $this->billable = $project->billable;
            $this->hourly_rate = $project->hourly_rate_cents ? $project->hourly_rate_cents / 100 : null;
        }
    }

    public function save(): void
    {
        $this->validate();

        try {
            if ($this->projectId) {
                // Update existing project
                $project = Project::findOrFail($this->projectId);
                $data = UpdateProjectData::from([
                    'name' => $this->name,
                    'description' => $this->description,
                    'status' => $this->status ? ProjectStatus::from($this->status) : null,
                    'priority' => $this->priority ? ProjectPriority::from($this->priority) : null,
                    'budget_cents' => $this->budget ? (int)($this->budget * 100) : null,
                    'currency' => $this->currency,
                    'estimated_hours' => $this->estimated_hours ? (float)$this->estimated_hours : null,
                    'starts_at' => $this->starts_at,
                    'due_at' => $this->due_at,
                    'billable' => $this->billable,
                    'hourly_rate_cents' => $this->hourly_rate ? (int)($this->hourly_rate * 100) : null,
                ]);

                $action = new UpdateProjectAction();
                $action($project, $data);

                session()->flash('success', 'Project updated successfully.');
            } else {
                // Create new project
                $data = CreateProjectData::from([
                    'client_id' => $this->client_id,
                    'name' => $this->name,
                    'description' => $this->description,
                    'status' => $this->status ? ProjectStatus::from($this->status) : null,
                    'priority' => $this->priority ? ProjectPriority::from($this->priority) : null,
                    'budget_cents' => $this->budget ? (int)($this->budget * 100) : null,
                    'currency' => $this->currency,
                    'estimated_hours' => $this->estimated_hours ? (float)$this->estimated_hours : null,
                    'starts_at' => $this->starts_at,
                    'due_at' => $this->due_at,
                    'billable' => $this->billable,
                    'hourly_rate_cents' => $this->hourly_rate ? (int)($this->hourly_rate * 100) : null,
                ]);

                $action = new CreateProjectAction();
                $action($data);

                session()->flash('success', 'Project created successfully.');
            }

            $this->redirect(route('projects.index'), navigate: true);
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        $clients = Client::whereNull('archived_at')
            ->orderBy('name')
            ->get();

        return view('livewire.tenant.projects.project-form', [
            'clients' => $clients,
        ])->layout('layouts.tenant', [
            'header' => $this->projectId ? 'Edit Project' : 'Create Project'
        ]);
    }
}
