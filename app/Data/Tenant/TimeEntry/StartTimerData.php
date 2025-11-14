<?php

declare(strict_types=1);

namespace App\Data\Tenant\TimeEntry;

use Carbon\Carbon;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class StartTimerData extends Data
{
    public function __construct(
        public string $description,
        public string|Optional $project_id = new Optional(),
        public string|Optional $client_id = new Optional(),
        public bool $billable = true,
        public float|Optional $hourly_rate = new Optional(),
        public Carbon|Optional $start_time = new Optional(),
    ) {}
}
