<?php

declare(strict_types=1);

namespace App\Actions\Tenant\TimeEntry;

use App\Models\Tenant\TimeEntry;
use Carbon\Carbon;

class StopTimerAction
{
    public function __invoke(TimeEntry $timeEntry, ?Carbon $endTime = null): TimeEntry
    {
        if (!$timeEntry->isRunning()) {
            throw new \Exception('This timer is not currently running.');
        }

        $timeEntry->end_time = $endTime ?? now();
        $timeEntry->save();

        return $timeEntry;
    }
}
