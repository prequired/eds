<?php

use App\Enums\TimeEntryStatus;
use App\Models\Tenant\Client;
use App\Models\Tenant\Project;
use App\Models\Tenant\TimeEntry;
use App\Models\Tenant\User;

beforeEach(function () {
    $this->user = User::factory()->create();
});

it('auto-calculates duration when saving with end_time', function () {
    $startTime = now();
    $endTime = now()->addHours(2)->addMinutes(30); // 2.5 hours

    $entry = TimeEntry::create([
        'user_id' => $this->user->id,
        'description' => 'Test Entry',
        'start_time' => $startTime,
        'end_time' => $endTime,
        'billable' => false,
    ]);

    expect($entry->duration)->toBe(9000); // 2.5 hours = 9000 seconds
});

it('calculates duration in hours correctly', function () {
    $entry = TimeEntry::factory()->create([
        'user_id' => $this->user->id,
        'duration' => 7200, // 2 hours
    ]);

    expect($entry->duration_in_hours)->toBe(2.0);
});

it('calculates duration in hours for partial hours', function () {
    $entry = TimeEntry::factory()->create([
        'user_id' => $this->user->id,
        'duration' => 5400, // 1.5 hours
    ]);

    expect($entry->duration_in_hours)->toBe(1.5);
});

it('formats duration correctly for hours and minutes', function () {
    $entry = TimeEntry::factory()->create([
        'user_id' => $this->user->id,
        'duration' => 9000, // 2 hours and 30 minutes
    ]);

    expect($entry->formatted_duration)->toBe('2h 30m');
});

it('formats duration correctly for minutes only', function () {
    $entry = TimeEntry::factory()->create([
        'user_id' => $this->user->id,
        'duration' => 1800, // 30 minutes
    ]);

    expect($entry->formatted_duration)->toBe('30m 00s');
});

it('formats duration correctly for seconds only', function () {
    $entry = TimeEntry::factory()->create([
        'user_id' => $this->user->id,
        'duration' => 45, // 45 seconds
    ]);

    expect($entry->formatted_duration)->toBe('45s');
});

it('calculates total amount for billable entries', function () {
    $entry = TimeEntry::factory()->create([
        'user_id' => $this->user->id,
        'duration' => 7200, // 2 hours
        'billable' => true,
        'hourly_rate' => 75.50,
    ]);

    expect($entry->total_amount)->toBe(151.0); // 2 * 75.50
});

it('returns zero total amount for non-billable entries', function () {
    $entry = TimeEntry::factory()->create([
        'user_id' => $this->user->id,
        'duration' => 7200,
        'billable' => false,
        'hourly_rate' => null,
    ]);

    expect($entry->total_amount)->toBe(0.0);
});

it('returns zero total amount when hourly rate is not set', function () {
    $entry = TimeEntry::factory()->create([
        'user_id' => $this->user->id,
        'duration' => 7200,
        'billable' => true,
        'hourly_rate' => null,
    ]);

    expect($entry->total_amount)->toBe(0.0);
});

it('correctly identifies running timers', function () {
    $entry = TimeEntry::factory()->running()->create([
        'user_id' => $this->user->id,
    ]);

    expect($entry->isRunning())->toBeTrue();
});

it('correctly identifies stopped timers', function () {
    $entry = TimeEntry::factory()->completed()->create([
        'user_id' => $this->user->id,
    ]);

    expect($entry->isRunning())->toBeFalse();
});

it('can stop a running timer', function () {
    $entry = TimeEntry::factory()->running()->create([
        'user_id' => $this->user->id,
        'start_time' => now()->subHours(2),
    ]);

    expect($entry->isRunning())->toBeTrue();

    $entry->stop();

    expect($entry->isRunning())->toBeFalse();
    expect($entry->end_time)->not->toBeNull();
    expect($entry->duration)->toBeGreaterThan(0);
});

it('can check if status allows editing', function () {
    $draftEntry = TimeEntry::factory()->draft()->create(['user_id' => $this->user->id]);
    $submittedEntry = TimeEntry::factory()->submitted()->create(['user_id' => $this->user->id]);
    $approvedEntry = TimeEntry::factory()->approved()->create(['user_id' => $this->user->id]);
    $invoicedEntry = TimeEntry::factory()->invoiced()->create(['user_id' => $this->user->id]);

    expect($draftEntry->status->canEdit())->toBeTrue();
    expect($submittedEntry->status->canEdit())->toBeTrue();
    expect($approvedEntry->status->canEdit())->toBeFalse();
    expect($invoicedEntry->status->canEdit())->toBeFalse();
});

it('can check if status allows deleting', function () {
    $draftEntry = TimeEntry::factory()->draft()->create(['user_id' => $this->user->id]);
    $submittedEntry = TimeEntry::factory()->submitted()->create(['user_id' => $this->user->id]);
    $approvedEntry = TimeEntry::factory()->approved()->create(['user_id' => $this->user->id]);
    $invoicedEntry = TimeEntry::factory()->invoiced()->create(['user_id' => $this->user->id]);

    expect($draftEntry->status->canDelete())->toBeTrue();
    expect($submittedEntry->status->canDelete())->toBeTrue();
    expect($approvedEntry->status->canDelete())->toBeTrue();
    expect($invoicedEntry->status->canDelete())->toBeFalse();
});

it('filters running entries with scope', function () {
    TimeEntry::factory()->running()->create(['user_id' => $this->user->id]);
    TimeEntry::factory()->running()->create(['user_id' => $this->user->id]);
    TimeEntry::factory()->completed()->create(['user_id' => $this->user->id]);

    $runningEntries = TimeEntry::running()->get();

    expect($runningEntries->count())->toBe(2);
    expect($runningEntries->every->isRunning())->toBeTrue();
});

it('filters completed entries with scope', function () {
    TimeEntry::factory()->running()->create(['user_id' => $this->user->id]);
    TimeEntry::factory()->completed()->create(['user_id' => $this->user->id]);
    TimeEntry::factory()->completed()->create(['user_id' => $this->user->id]);

    $completedEntries = TimeEntry::completed()->get();

    expect($completedEntries->count())->toBe(2);
    expect($completedEntries->every(fn ($entry) => !$entry->isRunning()))->toBeTrue();
});

it('filters billable entries with scope', function () {
    TimeEntry::factory()->billable()->create(['user_id' => $this->user->id]);
    TimeEntry::factory()->billable()->create(['user_id' => $this->user->id]);
    TimeEntry::factory()->nonBillable()->create(['user_id' => $this->user->id]);

    $billableEntries = TimeEntry::billable()->get();

    expect($billableEntries->count())->toBe(2);
    expect($billableEntries->every(fn ($entry) => $entry->billable))->toBeTrue();
});

it('filters entries for specific user with scope', function () {
    $user1 = $this->user;
    $user2 = User::factory()->create();

    TimeEntry::factory()->create(['user_id' => $user1->id]);
    TimeEntry::factory()->create(['user_id' => $user1->id]);
    TimeEntry::factory()->create(['user_id' => $user2->id]);

    $user1Entries = TimeEntry::forUser($user1->id)->get();

    expect($user1Entries->count())->toBe(2);
    expect($user1Entries->every(fn ($entry) => $entry->user_id === $user1->id))->toBeTrue();
});

it('filters entries for specific project with scope', function () {
    $project = Project::factory()->create();

    TimeEntry::factory()->forProject($project)->create(['user_id' => $this->user->id]);
    TimeEntry::factory()->forProject($project)->create(['user_id' => $this->user->id]);
    TimeEntry::factory()->create(['user_id' => $this->user->id, 'project_id' => null]);

    $projectEntries = TimeEntry::forProject($project->id)->get();

    expect($projectEntries->count())->toBe(2);
    expect($projectEntries->every(fn ($entry) => $entry->project_id === $project->id))->toBeTrue();
});

it('filters entries for specific client with scope', function () {
    $client = Client::factory()->create();

    TimeEntry::factory()->forClient($client)->create(['user_id' => $this->user->id]);
    TimeEntry::factory()->forClient($client)->create(['user_id' => $this->user->id]);
    TimeEntry::factory()->create(['user_id' => $this->user->id, 'client_id' => null]);

    $clientEntries = TimeEntry::forClient($client->id)->get();

    expect($clientEntries->count())->toBe(2);
    expect($clientEntries->every(fn ($entry) => $entry->client_id === $client->id))->toBeTrue();
});

it('filters entries between dates with scope', function () {
    TimeEntry::factory()->create([
        'user_id' => $this->user->id,
        'start_time' => now()->subDays(5),
        'end_time' => now()->subDays(5)->addHours(2),
    ]);
    TimeEntry::factory()->create([
        'user_id' => $this->user->id,
        'start_time' => now()->subDays(3),
        'end_time' => now()->subDays(3)->addHours(2),
    ]);
    TimeEntry::factory()->create([
        'user_id' => $this->user->id,
        'start_time' => now()->subDays(10),
        'end_time' => now()->subDays(10)->addHours(2),
    ]);

    $entries = TimeEntry::betweenDates(
        now()->subDays(7)->startOfDay(),
        now()->startOfDay()
    )->get();

    expect($entries->count())->toBe(2);
});

it('filters not invoiced entries with scope', function () {
    TimeEntry::factory()->draft()->create(['user_id' => $this->user->id]);
    TimeEntry::factory()->approved()->create(['user_id' => $this->user->id]);
    TimeEntry::factory()->invoiced()->create(['user_id' => $this->user->id]);

    $notInvoiced = TimeEntry::notInvoiced()->get();

    expect($notInvoiced->count())->toBe(2);
    expect($notInvoiced->every(fn ($entry) => $entry->status !== TimeEntryStatus::INVOICED))->toBeTrue();
});
