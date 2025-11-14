<?php

declare(strict_types=1);

namespace App\Livewire\Tenant\TimeEntry;

use App\Actions\Tenant\TimeEntry\StartTimerAction;
use App\Actions\Tenant\TimeEntry\StopTimerAction;
use App\Data\Tenant\TimeEntry\StartTimerData;
use App\Models\Tenant\Client;
use App\Models\Tenant\Project;
use App\Models\Tenant\TimeEntry;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;

class TimeTracker extends Component
{
    public ?TimeEntry $runningTimer = null;
    public int $elapsedSeconds = 0;

    #[Validate('required|min:3|max:255')]
    public string $description = '';

    public ?string $project_id = null;
    public ?string $client_id = null;
    public bool $billable = true;
    public ?float $hourly_rate = null;

    public function mount(): void
    {
        $this->loadRunningTimer();
    }

    public function loadRunningTimer(): void
    {
        $this->runningTimer = TimeEntry::forUser(auth()->id())
            ->running()
            ->with(['project', 'client'])
            ->first();

        if ($this->runningTimer) {
            $this->description = $this->runningTimer->description;
            $this->project_id = $this->runningTimer->project_id;
            $this->client_id = $this->runningTimer->client_id;
            $this->billable = $this->runningTimer->billable;
            $this->hourly_rate = $this->runningTimer->hourly_rate ? (float) $this->runningTimer->hourly_rate : null;
            $this->elapsedSeconds = $this->runningTimer->start_time->diffInSeconds(now());
        }
    }

    public function startTimer(): void
    {
        $this->validate();

        try {
            $data = StartTimerData::from([
                'description' => $this->description,
                'project_id' => $this->project_id,
                'client_id' => $this->client_id,
                'billable' => $this->billable,
                'hourly_rate' => $this->hourly_rate,
            ]);

            $action = new StartTimerAction();
            $this->runningTimer = $action($data, auth()->user());

            $this->dispatch('timer-started');
            $this->dispatch('notification', [
                'type' => 'success',
                'message' => 'Timer started.',
            ]);
        } catch (\Exception $e) {
            $this->dispatch('notification', [
                'type' => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function stopTimer(): void
    {
        if (!$this->runningTimer) {
            return;
        }

        try {
            $action = new StopTimerAction();
            $action($this->runningTimer);

            $this->dispatch('timer-stopped');
            $this->dispatch('notification', [
                'type' => 'success',
                'message' => 'Timer stopped. Duration: ' . $this->runningTimer->formatted_duration,
            ]);

            $this->reset(['runningTimer', 'description', 'project_id', 'client_id', 'elapsedSeconds']);
        } catch (\Exception $e) {
            $this->dispatch('notification', [
                'type' => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }

    #[On('refresh-timer')]
    public function refreshTimer(): void
    {
        if ($this->runningTimer) {
            $this->elapsedSeconds = $this->runningTimer->start_time->diffInSeconds(now());
        }
    }

    public function getFormattedElapsedAttribute(): string
    {
        $hours = floor($this->elapsedSeconds / 3600);
        $minutes = floor(($this->elapsedSeconds % 3600) / 60);
        $seconds = $this->elapsedSeconds % 60;

        return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
    }

    public function render()
    {
        $projects = Project::orderBy('name')->get();
        $clients = Client::orderBy('name')->get();

        return view('livewire.tenant.time-entry.time-tracker', [
            'projects' => $projects,
            'clients' => $clients,
        ]);
    }
}
