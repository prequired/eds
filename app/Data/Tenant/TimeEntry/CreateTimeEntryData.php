<?php

declare(strict_types=1);

namespace App\Data\Tenant\TimeEntry;

use App\Enums\TimeEntryStatus;
use Carbon\Carbon;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class CreateTimeEntryData extends Data
{
    public function __construct(
        public string $description,
        public Carbon $start_time,
        public Carbon|null|Optional $end_time = new Optional(),
        public string|Optional $project_id = new Optional(),
        public string|Optional $client_id = new Optional(),
        public bool $billable = true,
        public float|Optional $hourly_rate = new Optional(),
        public TimeEntryStatus $status = TimeEntryStatus::DRAFT,
        public string|null|Optional $notes = new Optional(),
    ) {}
}
