<?php

declare(strict_types=1);

namespace App\Actions\Tenant\Websites;

use App\Events\Tenant\WebsiteDeleted;
use App\Models\Tenant\Website;
use Illuminate\Support\Facades\DB;

class DeleteWebsiteAction
{
    public function __invoke(Website $website): void
    {
        DB::transaction(function () use ($website) {
            $website->update([
                'archived_at' => now(),
            ]);

            WebsiteDeleted::dispatch($website);
        });
    }
}
