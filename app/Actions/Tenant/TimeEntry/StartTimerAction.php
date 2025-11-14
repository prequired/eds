<?php

declare(strict_types=1);

namespace App\Actions\Tenant\TimeEntry;

use App\Data\Tenant\TimeEntry\StartTimerData;
use App\Models\Tenant\TimeEntry;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class StartTimerAction
{
    public function __invoke(StartTimerData $data, User $user): TimeEntry
    {
        return DB::transaction(function () use ($data, $user) {
            // Stop any currently running timers for this user
            TimeEntry::forUser($user->id)
                ->running()
                ->get()
                ->each(fn(TimeEntry $entry) => $entry->stop());

            // Create new time entry with running timer
            return TimeEntry::create([
                'user_id' => $user->id,
                'project_id' => $data->project_id instanceof \Spatie\LaravelData\Optional ? null : $data->project_id,
                'client_id' => $data->client_id instanceof \Spatie\LaravelData\Optional ? null : $data->client_id,
                'description' => $data->description,
                'start_time' => $data->start_time instanceof \Spatie\LaravelData\Optional ? now() : $data->start_time,
                'end_time' => null, // Timer is running
                'billable' => $data->billable,
                'hourly_rate' => $data->hourly_rate instanceof \Spatie\LaravelData\Optional ? null : $data->hourly_rate,
            ]);
        });
    }
}
