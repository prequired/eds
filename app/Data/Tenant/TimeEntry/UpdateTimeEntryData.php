<?php

declare(strict_types=1);

namespace App\Data\Tenant\TimeEntry;

use Carbon\Carbon;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class UpdateTimeEntryData extends Data
{
    public function __construct(
        public string|Optional $description = new Optional(),
        public Carbon|Optional $start_time = new Optional(),
        public Carbon|null|Optional $end_time = new Optional(),
        public string|null|Optional $project_id = new Optional(),
        public string|null|Optional $client_id = new Optional(),
        public bool|Optional $billable = new Optional(),
        public float|null|Optional $hourly_rate = new Optional(),
        public string|null|Optional $notes = new Optional(),
    ) {}
}
