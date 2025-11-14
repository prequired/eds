<?php

declare(strict_types=1);

namespace App\Actions\Tenant\Projects;

use App\Events\Tenant\ProjectDeleted;
use App\Models\Tenant\Project;
use Illuminate\Support\Facades\DB;

class DeleteProjectAction
{
    public function __invoke(Project $project): void
    {
        DB::transaction(function () use ($project) {
            $project->update([
                'archived_at' => now(),
            ]);

            ProjectDeleted::dispatch($project);
        });
    }
}
