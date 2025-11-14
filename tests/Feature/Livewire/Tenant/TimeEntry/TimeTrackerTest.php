<?php

use App\Livewire\Tenant\TimeEntry\TimeTracker;
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
    Livewire::test(TimeTracker::class)
        ->assertStatus(200);
});

it('shows start timer form when no timer is running', function () {
    Livewire::test(TimeTracker::class)
        ->assertSee('Start Timer')
        ->assertDontSee('Stop Timer');
});

it('shows stop timer button when timer is running', function () {
    TimeEntry::factory()->running()->create([
        'user_id' => $this->user->id,
        'description' => 'Test Timer',
    ]);

    Livewire::test(TimeTracker::class)
        ->assertSee('Stop Timer')
        ->assertSee('Test Timer')
        ->assertDontSee('Start Timer');
});

it('starts a timer with required fields', function () {
    Livewire::test(TimeTracker::class)
        ->set('description', 'Working on feature')
        ->set('billable', true)
        ->call('startTimer')
        ->assertDispatched('timer-started')
        ->assertDispatched('notification');

    expect(TimeEntry::where('user_id', $this->user->id)->count())->toBe(1);
    $entry = TimeEntry::where('user_id', $this->user->id)->first();
    expect($entry->description)->toBe('Working on feature');
    expect($entry->isRunning())->toBeTrue();
});

it('starts a timer with project and client', function () {
    $project = Project::factory()->create();
    $client = Client::factory()->create();

    Livewire::test(TimeTracker::class)
        ->set('description', 'Working on project')
        ->set('project_id', $project->id)
        ->set('client_id', $client->id)
        ->set('billable', true)
        ->set('hourly_rate', 75.50)
        ->call('startTimer')
        ->assertDispatched('timer-started');

    $entry = TimeEntry::where('user_id', $this->user->id)->first();
    expect($entry->project_id)->toBe($project->id);
    expect($entry->client_id)->toBe($client->id);
    expect($entry->billable)->toBeTrue();
    expect((float) $entry->hourly_rate)->toBe(75.50);
});

it('requires description to start timer', function () {
    Livewire::test(TimeTracker::class)
        ->set('description', '')
        ->call('startTimer')
        ->assertHasErrors(['description']);
});

it('validates description minimum length', function () {
    Livewire::test(TimeTracker::class)
        ->set('description', 'ab') // Less than 3 characters
        ->call('startTimer')
        ->assertHasErrors(['description']);
});

it('stops running timer', function () {
    $entry = TimeEntry::factory()->running()->create([
        'user_id' => $this->user->id,
        'description' => 'Test Timer',
        'start_time' => now()->subHours(2),
    ]);

    Livewire::test(TimeTracker::class)
        ->call('stopTimer')
        ->assertDispatched('timer-stopped')
        ->assertDispatched('notification');

    $entry->refresh();
    expect($entry->isRunning())->toBeFalse();
    expect($entry->end_time)->not->toBeNull();
});

it('calculates elapsed time correctly', function () {
    $entry = TimeEntry::factory()->running()->create([
        'user_id' => $this->user->id,
        'description' => 'Test Timer',
        'start_time' => now()->subSeconds(3665), // 1 hour, 1 minute, 5 seconds ago
    ]);

    $component = Livewire::test(TimeTracker::class);
    $component->call('refreshTimer');

    expect($component->get('elapsedSeconds'))->toBeGreaterThanOrEqual(3665);
});

it('formats elapsed time as HH:MM:SS', function () {
    $entry = TimeEntry::factory()->running()->create([
        'user_id' => $this->user->id,
        'description' => 'Test Timer',
        'start_time' => now()->subSeconds(7325), // 2 hours, 2 minutes, 5 seconds
    ]);

    $component = Livewire::test(TimeTracker::class);
    $component->call('refreshTimer');

    $formatted = $component->get('formattedElapsed');
    expect($formatted)->toMatch('/\d{2}:\d{2}:\d{2}/'); // HH:MM:SS format
});

it('stops any existing timers when starting a new one', function () {
    $oldEntry = TimeEntry::factory()->running()->create([
        'user_id' => $this->user->id,
        'description' => 'Old Timer',
        'start_time' => now()->subHours(1),
    ]);

    Livewire::test(TimeTracker::class)
        ->set('description', 'New Timer')
        ->call('startTimer')
        ->assertDispatched('timer-started');

    $oldEntry->refresh();
    expect($oldEntry->isRunning())->toBeFalse();

    $newEntry = TimeEntry::where('description', 'New Timer')->first();
    expect($newEntry->isRunning())->toBeTrue();
});

it('resets form after stopping timer', function () {
    $entry = TimeEntry::factory()->running()->create([
        'user_id' => $this->user->id,
        'description' => 'Test Timer',
        'start_time' => now()->subHours(1),
    ]);

    Livewire::test(TimeTracker::class)
        ->call('stopTimer')
        ->assertSet('description', '')
        ->assertSet('project_id', null)
        ->assertSet('client_id', null)
        ->assertSet('runningTimer', null);
});

it('loads running timer on mount', function () {
    $entry = TimeEntry::factory()->running()->create([
        'user_id' => $this->user->id,
        'description' => 'Test Timer',
        'start_time' => now()->subHours(1),
    ]);

    $component = Livewire::test(TimeTracker::class);

    expect($component->get('runningTimer'))->not->toBeNull();
    expect($component->get('description'))->toBe('Test Timer');
});

it('loads timer with project and client on mount', function () {
    $project = Project::factory()->create();
    $client = Client::factory()->create();

    $entry = TimeEntry::factory()->running()->create([
        'user_id' => $this->user->id,
        'description' => 'Test Timer',
        'project_id' => $project->id,
        'client_id' => $client->id,
        'billable' => true,
        'hourly_rate' => 100,
        'start_time' => now()->subHours(1),
    ]);

    $component = Livewire::test(TimeTracker::class);

    expect($component->get('project_id'))->toBe($project->id);
    expect($component->get('client_id'))->toBe($client->id);
    expect($component->get('billable'))->toBeTrue();
    expect($component->get('hourly_rate'))->toBe(100.0);
});
