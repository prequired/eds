<?php

declare(strict_types=1);

namespace App\Actions\Tenant\TimeEntry;

use App\Models\Tenant\TimeEntry;

class DeleteTimeEntryAction
{
    public function __invoke(TimeEntry $timeEntry): void
    {
        if (!$timeEntry->status->canDelete()) {
            throw new \Exception('Cannot delete a time entry that has been invoiced.');
        }

        $timeEntry->delete();
    }
}
