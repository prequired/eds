<?php

declare(strict_types=1);

namespace App\Actions\Tenant\Projects;

use App\Data\Tenant\Projects\CreateProjectData;
use App\Enums\ProjectPriority;
use App\Enums\ProjectStatus;
use App\Events\Tenant\ProjectCreated;
use App\Models\Tenant\Project;
use Illuminate\Support\Facades\DB;

class CreateProjectAction
{
    public function __invoke(CreateProjectData $data): Project
    {
        return DB::transaction(function () use ($data) {
            $project = Project::create([
                'client_id' => $data->client_id,
                'name' => $data->name,
                'description' => $data->description,
                'status' => $data->status ?? ProjectStatus::PLANNING,
                'priority' => $data->priority ?? ProjectPriority::MEDIUM,
                'budget_cents' => $data->budget_cents,
                'currency' => $data->currency ?? 'USD',
                'estimated_hours' => $data->estimated_hours,
                'actual_hours' => 0,
                'starts_at' => $data->starts_at,
                'due_at' => $data->due_at,
                'billable' => $data->billable ?? true,
                'hourly_rate_cents' => $data->hourly_rate_cents,
            ]);

            ProjectCreated::dispatch($project);

            return $project;
        });
    }
}
