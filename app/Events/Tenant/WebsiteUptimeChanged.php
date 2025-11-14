<?php

declare(strict_types=1);

namespace App\Events\Tenant;

use App\Enums\UptimeStatus;
use App\Models\Tenant\Website;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WebsiteUptimeChanged
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Website $website,
        public UptimeStatus $previousStatus,
        public UptimeStatus $newStatus
    ) {
    }

    /**
     * Check if the website went down.
     */
    public function wentDown(): bool
    {
        return $this->previousStatus === UptimeStatus::UP
            && $this->newStatus === UptimeStatus::DOWN;
    }

    /**
     * Check if the website came back up.
     */
    public function cameUp(): bool
    {
        return $this->previousStatus === UptimeStatus::DOWN
            && $this->newStatus === UptimeStatus::UP;
    }
}
