<?php

declare(strict_types=1);

namespace App\Data\Tenant\Projects;

use App\Enums\ProjectPriority;
use App\Enums\ProjectStatus;
use Spatie\LaravelData\Attributes\Validation\Date;
use Spatie\LaravelData\Attributes\Validation\Enum;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Data;

class UpdateProjectData extends Data
{
    public function __construct(
        #[Required, Min(2), Max(255)]
        public string $name,

        #[Nullable, Max(5000)]
        public ?string $description,

        #[Nullable, Enum(ProjectStatus::class)]
        public ?ProjectStatus $status,

        #[Nullable, Enum(ProjectPriority::class)]
        public ?ProjectPriority $priority,

        #[Nullable, Min(0)]
        public ?int $budget_cents,

        #[Nullable, Max(3)]
        public ?string $currency,

        #[Nullable, Min(0)]
        public ?float $estimated_hours,

        #[Nullable, Date]
        public ?string $starts_at,

        #[Nullable, Date]
        public ?string $due_at,

        #[Nullable]
        public ?bool $billable,

        #[Nullable, Min(0)]
        public ?int $hourly_rate_cents,
    ) {
    }
}
