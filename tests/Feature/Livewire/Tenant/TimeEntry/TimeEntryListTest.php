<?php

use App\Enums\TimeEntryStatus;
use App\Livewire\Tenant\TimeEntry\TimeEntryList;
use App\Models\Tenant\Client;
use App\Models\Tenant\Project;
use App\Models\Tenant\TimeEntry;
use App\Models\Tenant\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
});

it('renders successfully', function () {
    Livewire::test(TimeEntryList::class)
        ->assertStatus(200);
});

it('displays time entries for authenticated user', function () {
    $entry = TimeEntry::factory()->create([
        'user_id' => $this->user->id,
        'description' => 'Test Entry',
        'start_time' => now()->subHours(2),
        'end_time' => now(),
    ]);

    Livewire::test(TimeEntryList::class)
        ->assertSee('Test Entry');
});

it('filters time entries by search query', function () {
    TimeEntry::factory()->create([
        'user_id' => $this->user->id,
        'description' => 'Writing documentation',
        'start_time' => now()->subHours(2),
        'end_time' => now(),
    ]);

    TimeEntry::factory()->create([
        'user_id' => $this->user->id,
        'description' => 'Code review',
        'start_time' => now()->subHours(1),
        'end_time' => now(),
    ]);

    Livewire::test(TimeEntryList::class)
        ->set('search', 'documentation')
        ->assertSee('Writing documentation')
        ->assertDontSee('Code review');
});

it('filters time entries by project', function () {
    $project1 = Project::factory()->create(['name' => 'Project A']);
    $project2 = Project::factory()->create(['name' => 'Project B']);

    TimeEntry::factory()->create([
        'user_id' => $this->user->id,
        'project_id' => $project1->id,
        'description' => 'Project A work',
        'start_time' => now()->subHours(2),
        'end_time' => now(),
    ]);

    TimeEntry::factory()->create([
        'user_id' => $this->user->id,
        'project_id' => $project2->id,
        'description' => 'Project B work',
        'start_time' => now()->subHours(1),
        'end_time' => now(),
    ]);

    Livewire::test(TimeEntryList::class)
        ->set('projectFilter', $project1->id)
        ->assertSee('Project A work')
        ->assertDontSee('Project B work');
});

it('filters time entries by client', function () {
    $client1 = Client::factory()->create(['name' => 'Client A']);
    $client2 = Client::factory()->create(['name' => 'Client B']);

    TimeEntry::factory()->create([
        'user_id' => $this->user->id,
        'client_id' => $client1->id,
        'description' => 'Client A work',
        'start_time' => now()->subHours(2),
        'end_time' => now(),
    ]);

    TimeEntry::factory()->create([
        'user_id' => $this->user->id,
        'client_id' => $client2->id,
        'description' => 'Client B work',
        'start_time' => now()->subHours(1),
        'end_time' => now(),
    ]);

    Livewire::test(TimeEntryList::class)
        ->set('clientFilter', $client1->id)
        ->assertSee('Client A work')
        ->assertDontSee('Client B work');
});

it('filters time entries by billable status', function () {
    TimeEntry::factory()->create([
        'user_id' => $this->user->id,
        'billable' => true,
        'description' => 'Billable work',
        'start_time' => now()->subHours(2),
        'end_time' => now(),
    ]);

    TimeEntry::factory()->create([
        'user_id' => $this->user->id,
        'billable' => false,
        'description' => 'Non-billable work',
        'start_time' => now()->subHours(1),
        'end_time' => now(),
    ]);

    Livewire::test(TimeEntryList::class)
        ->set('billableFilter', 'yes')
        ->assertSee('Billable work')
        ->assertDontSee('Non-billable work');
});

it('filters time entries by status', function () {
    TimeEntry::factory()->create([
        'user_id' => $this->user->id,
        'status' => TimeEntryStatus::DRAFT,
        'description' => 'Draft entry',
        'start_time' => now()->subHours(2),
        'end_time' => now(),
    ]);

    TimeEntry::factory()->create([
        'user_id' => $this->user->id,
        'status' => TimeEntryStatus::APPROVED,
        'description' => 'Approved entry',
        'start_time' => now()->subHours(1),
        'end_time' => now(),
    ]);

    Livewire::test(TimeEntryList::class)
        ->set('statusFilter', TimeEntryStatus::DRAFT->value)
        ->assertSee('Draft entry')
        ->assertDontSee('Approved entry');
});

it('calculates total hours correctly', function () {
    // Entry 1: 2 hours
    TimeEntry::factory()->create([
        'user_id' => $this->user->id,
        'start_time' => now()->subHours(2),
        'end_time' => now(),
        'duration' => 7200, // 2 hours
    ]);

    // Entry 2: 3 hours
    TimeEntry::factory()->create([
        'user_id' => $this->user->id,
        'start_time' => now()->subHours(3),
        'end_time' => now(),
        'duration' => 10800, // 3 hours
    ]);

    Livewire::test(TimeEntryList::class)
        ->assertSet('totalHours', 5.0);
});

it('calculates total amount correctly', function () {
    // Billable entry: 2 hours @ $50/hr = $100
    TimeEntry::factory()->create([
        'user_id' => $this->user->id,
        'billable' => true,
        'hourly_rate' => 50,
        'start_time' => now()->subHours(2),
        'end_time' => now(),
        'duration' => 7200,
    ]);

    // Billable entry: 3 hours @ $75/hr = $225
    TimeEntry::factory()->create([
        'user_id' => $this->user->id,
        'billable' => true,
        'hourly_rate' => 75,
        'start_time' => now()->subHours(3),
        'end_time' => now(),
        'duration' => 10800,
    ]);

    Livewire::test(TimeEntryList::class)
        ->assertSet('totalAmount', 325.0);
});

it('allows user to delete their own entry', function () {
    $entry = TimeEntry::factory()->create([
        'user_id' => $this->user->id,
        'description' => 'My Entry',
        'start_time' => now()->subHours(2),
        'end_time' => now(),
    ]);

    $this->user->givePermissionTo('time_entries.delete');

    Livewire::test(TimeEntryList::class)
        ->call('deleteEntry', $entry->id)
        ->assertDispatched('notification');

    expect(TimeEntry::find($entry->id))->toBeNull();
});

it('prevents user from deleting another users entry', function () {
    $otherUser = User::factory()->create();
    $entry = TimeEntry::factory()->create([
        'user_id' => $otherUser->id,
        'description' => 'Other User Entry',
        'start_time' => now()->subHours(2),
        'end_time' => now(),
    ]);

    $this->user->givePermissionTo('time_entries.delete');

    Livewire::test(TimeEntryList::class)
        ->call('deleteEntry', $entry->id)
        ->assertDispatched('notification');

    expect(TimeEntry::find($entry->id))->not->toBeNull();
});

it('shows only user entries for non-admin users', function () {
    $otherUser = User::factory()->create();

    TimeEntry::factory()->create([
        'user_id' => $this->user->id,
        'description' => 'My Entry',
        'start_time' => now()->subHours(2),
        'end_time' => now(),
    ]);

    TimeEntry::factory()->create([
        'user_id' => $otherUser->id,
        'description' => 'Other User Entry',
        'start_time' => now()->subHours(1),
        'end_time' => now(),
    ]);

    Livewire::test(TimeEntryList::class)
        ->assertSee('My Entry')
        ->assertDontSee('Other User Entry');
});

it('refreshes entries when timer is stopped', function () {
    $entry = TimeEntry::factory()->create([
        'user_id' => $this->user->id,
        'description' => 'Test Entry',
        'start_time' => now()->subHours(2),
        'end_time' => now(),
    ]);

    Livewire::test(TimeEntryList::class)
        ->dispatch('timer-stopped')
        ->assertSee('Test Entry');
});

it('resets page when search is updated', function () {
    Livewire::test(TimeEntryList::class)
        ->set('search', 'test')
        ->assertMethodWasCalled('resetPage');
});
