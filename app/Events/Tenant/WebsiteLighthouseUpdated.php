<?php

declare(strict_types=1);

namespace App\Events\Tenant;

use App\Models\Tenant\Website;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class WebsiteLighthouseUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Website $website,
        public array $previousScores,
        public array $newScores
    ) {
    }

    /**
     * Check if performance score improved significantly (by 10+ points).
     */
    public function performanceImproved(): bool
    {
        if ($this->previousScores['performance'] === null || $this->newScores['performance'] === null) {
            return false;
        }

        return ($this->newScores['performance'] - $this->previousScores['performance']) >= 10;
    }

    /**
     * Check if performance score degraded significantly (by 10+ points).
     */
    public function performanceDegraded(): bool
    {
        if ($this->previousScores['performance'] === null || $this->newScores['performance'] === null) {
            return false;
        }

        return ($this->previousScores['performance'] - $this->newScores['performance']) >= 10;
    }

    /**
     * Check if any score is critically low (below 50).
     */
    public function hasCriticalScores(): bool
    {
        return ($this->newScores['performance'] !== null && $this->newScores['performance'] < 50)
            || ($this->newScores['accessibility'] !== null && $this->newScores['accessibility'] < 50)
            || ($this->newScores['seo'] !== null && $this->newScores['seo'] < 50);
    }
}
