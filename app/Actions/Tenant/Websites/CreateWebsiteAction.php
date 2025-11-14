<?php

declare(strict_types=1);

namespace App\Actions\Tenant\Websites;

use App\Data\Tenant\Websites\CreateWebsiteData;
use App\Enums\DeploymentStatus;
use App\Enums\UptimeStatus;
use App\Enums\WebsiteEnvironment;
use App\Enums\WebsiteStatus;
use App\Events\Tenant\WebsiteCreated;
use App\Models\Tenant\Website;
use Illuminate\Support\Facades\DB;

class CreateWebsiteAction
{
    public function __invoke(CreateWebsiteData $data): Website
    {
        return DB::transaction(function () use ($data) {
            $website = Website::create([
                'client_id' => $data->client_id,
                'project_id' => $data->project_id,
                'name' => $data->name,
                'url' => $data->url,
                'environment' => $data->environment ?? WebsiteEnvironment::PRODUCTION,
                'status' => $data->status ?? WebsiteStatus::ACTIVE,
                'server_provider' => $data->server_provider,
                'server_id' => $data->server_id,
                'server_ip' => $data->server_ip,
                'repository_provider' => $data->repository_provider,
                'repository_url' => $data->repository_url,
                'repository_branch' => $data->repository_branch ?? 'main',
                'deployment_method' => $data->deployment_method,
                'deployment_status' => DeploymentStatus::IDLE,
                'uptime_status' => UptimeStatus::UNKNOWN,
                'notes' => $data->notes,
            ]);

            WebsiteCreated::dispatch($website);

            return $website;
        });
    }
}
