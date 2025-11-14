<?php

declare(strict_types=1);

namespace App\Actions\Tenant\Websites;

use App\Data\Tenant\Websites\UpdateWebsiteData;
use App\Events\Tenant\WebsiteUpdated;
use App\Models\Tenant\Website;
use Illuminate\Support\Facades\DB;

class UpdateWebsiteAction
{
    public function __invoke(Website $website, UpdateWebsiteData $data): Website
    {
        return DB::transaction(function () use ($website, $data) {
            $website->update([
                'name' => $data->name,
                'url' => $data->url,
                'environment' => $data->environment,
                'status' => $data->status,
                'server_provider' => $data->server_provider,
                'server_id' => $data->server_id,
                'server_ip' => $data->server_ip,
                'repository_provider' => $data->repository_provider,
                'repository_url' => $data->repository_url,
                'repository_branch' => $data->repository_branch,
                'deployment_method' => $data->deployment_method,
                'notes' => $data->notes,
            ]);

            WebsiteUpdated::dispatch($website);

            return $website->fresh();
        });
    }
}
