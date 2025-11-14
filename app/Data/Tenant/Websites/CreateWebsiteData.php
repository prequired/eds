<?php

declare(strict_types=1);

namespace App\Data\Tenant\Websites;

use App\Enums\DeploymentStatus;
use App\Enums\UptimeStatus;
use App\Enums\WebsiteEnvironment;
use App\Enums\WebsiteStatus;
use Spatie\LaravelData\Attributes\Validation\Enum;
use Spatie\LaravelData\Attributes\Validation\Exists;
use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Attributes\Validation\Min;
use Spatie\LaravelData\Attributes\Validation\Nullable;
use Spatie\LaravelData\Attributes\Validation\Required;
use Spatie\LaravelData\Attributes\Validation\Url;
use Spatie\LaravelData\Attributes\Validation\Uuid;
use Spatie\LaravelData\Data;

class CreateWebsiteData extends Data
{
    public function __construct(
        #[Required, Uuid, Exists('clients', 'id')]
        public string $client_id,

        #[Nullable, Uuid, Exists('projects', 'id')]
        public ?string $project_id,

        #[Required, Min(2), Max(255)]
        public string $name,

        #[Required, Url, Max(255)]
        public string $url,

        #[Nullable, Enum(WebsiteEnvironment::class)]
        public ?WebsiteEnvironment $environment,

        #[Nullable, Enum(WebsiteStatus::class)]
        public ?WebsiteStatus $status,

        #[Nullable, Max(50)]
        public ?string $server_provider,

        #[Nullable, Max(255)]
        public ?string $server_id,

        #[Nullable, Max(255)]
        public ?string $server_ip,

        #[Nullable, Max(50)]
        public ?string $repository_provider,

        #[Nullable, Url, Max(255)]
        public ?string $repository_url,

        #[Nullable, Max(100)]
        public ?string $repository_branch,

        #[Nullable, Max(50)]
        public ?string $deployment_method,

        #[Nullable, Max(5000)]
        public ?string $notes,
    ) {
    }
}
