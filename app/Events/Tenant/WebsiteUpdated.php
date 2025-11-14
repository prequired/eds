<?php

declare(strict_types=1);

namespace App\Events\Tenant;

use App\Models\Tenant\Website;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WebsiteUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Website $website
    ) {
    }
}
