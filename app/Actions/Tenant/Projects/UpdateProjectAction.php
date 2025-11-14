<?php

declare(strict_types=1);

namespace App\Actions\Tenant\Projects;

use App\Data\Tenant\Projects\UpdateProjectData;
use App\Events\Tenant\ProjectUpdated;
use App\Models\Tenant\Project;
use Illuminate\Support\Facades\DB;

class UpdateProjectAction
{
    public function __invoke(Project $project, UpdateProjectData $data): Project
    {
        return DB::transaction(function () use ($project, $data) {
            $project->update([
                'name' => $data->name,
                'description' => $data->description,
                'status' => $data->status,
                'priority' => $data->priority,
                'budget_cents' => $data->budget_cents,
                'currency' => $data->currency,
                'estimated_hours' => $data->estimated_hours,
                'starts_at' => $data->starts_at,
                'due_at' => $data->due_at,
                'billable' => $data->billable,
                'hourly_rate_cents' => $data->hourly_rate_cents,
            ]);

            ProjectUpdated::dispatch($project);

            return $project->fresh();
        });
    }
}
