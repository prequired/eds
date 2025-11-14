<?php

declare(strict_types=1);

namespace App\Actions\Tenant\TimeEntry;

use App\Data\Tenant\TimeEntry\UpdateTimeEntryData;
use App\Models\Tenant\TimeEntry;
use Illuminate\Support\Facades\DB;
use Spatie\LaravelData\Optional;

class UpdateTimeEntryAction
{
    public function __invoke(TimeEntry $timeEntry, UpdateTimeEntryData $data): TimeEntry
    {
        if (!$timeEntry->status->canEdit()) {
            throw new \Exception('This time entry cannot be edited in its current status.');
        }

        return DB::transaction(function () use ($timeEntry, $data) {
            $updates = [];

            if (!$data->description instanceof Optional) {
                $updates['description'] = $data->description;
            }
            if (!$data->start_time instanceof Optional) {
                $updates['start_time'] = $data->start_time;
            }
            if (!$data->end_time instanceof Optional) {
                $updates['end_time'] = $data->end_time;
            }
            if (!$data->project_id instanceof Optional) {
                $updates['project_id'] = $data->project_id;
            }
            if (!$data->client_id instanceof Optional) {
                $updates['client_id'] = $data->client_id;
            }
            if (!$data->billable instanceof Optional) {
                $updates['billable'] = $data->billable;
            }
            if (!$data->hourly_rate instanceof Optional) {
                $updates['hourly_rate'] = $data->hourly_rate;
            }
            if (!$data->notes instanceof Optional) {
                $updates['notes'] = $data->notes;
            }

            $timeEntry->update($updates);

            return $timeEntry;
        });
    }
}
