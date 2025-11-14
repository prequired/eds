<?php

declare(strict_types=1);

namespace App\Actions\Tenant\TimeEntry;

use App\Data\Tenant\TimeEntry\CreateTimeEntryData;
use App\Models\Tenant\TimeEntry;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class CreateTimeEntryAction
{
    public function __invoke(CreateTimeEntryData $data, User $user): TimeEntry
    {
        return DB::transaction(function () use ($data, $user) {
            return TimeEntry::create([
                'user_id' => $user->id,
                'project_id' => $data->project_id instanceof \Spatie\LaravelData\Optional ? null : $data->project_id,
                'client_id' => $data->client_id instanceof \Spatie\LaravelData\Optional ? null : $data->client_id,
                'description' => $data->description,
                'start_time' => $data->start_time,
                'end_time' => $data->end_time instanceof \Spatie\LaravelData\Optional ? null : $data->end_time,
                'billable' => $data->billable,
                'hourly_rate' => $data->hourly_rate instanceof \Spatie\LaravelData\Optional ? null : $data->hourly_rate,
                'status' => $data->status,
                'notes' => $data->notes instanceof \Spatie\LaravelData\Optional ? null : $data->notes,
            ]);
        });
    }
}
