<?php

declare(strict_types=1);

namespace App\Events\Tenant;

use App\Models\Tenant\Project;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProjectCreated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Project $project
    ) {
    }
}
