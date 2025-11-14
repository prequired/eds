<?php

declare(strict_types=1);

use App\Actions\Tenant\Websites\CheckWebsiteUptimeAction;
use App\Enums\UptimeStatus;
use App\Enums\WebsiteEnvironment;
use App\Enums\WebsiteStatus;
use App\Events\Tenant\WebsiteUptimeChanged;
use App\Models\Tenant\Client;
use App\Models\Tenant\UptimeCheck;
use App\Models\Tenant\Website;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;

uses()->group('uptime-monitoring');

beforeEach(function () {
    // Initialize tenancy for testing
    $this->tenant = createTenant();
    tenancy()->initialize($this->tenant);

    // Create test client
    $this->client = Client::factory()->create();
});

afterEach(function () {
    tenancy()->end();
});

test('can check website uptime successfully', function () {
    Http::fake([
        'https://example.com' => Http::response('OK', 200),
    ]);

    $website = Website::factory()->create([
        'client_id' => $this->client->id,
        'url' => 'https://example.com',
        'status' => WebsiteStatus::ACTIVE,
        'uptime_status' => UptimeStatus::UNKNOWN,
    ]);

    $action = new CheckWebsiteUptimeAction();
    $result = $action($website);

    expect($result['status'])->toBe(UptimeStatus::UP)
        ->and($result['status_code'])->toBe(200)
        ->and($result['response_time_ms'])->toBeGreaterThan(0);

    // Verify website was updated
    $website->refresh();
    expect($website->uptime_status)->toBe(UptimeStatus::UP)
        ->and($website->response_time_ms)->toBeGreaterThan(0)
        ->and($website->last_checked_at)->not()->toBeNull();
});

test('can detect website down', function () {
    Http::fake([
        'https://example.com' => Http::response('Server Error', 500),
    ]);

    $website = Website::factory()->create([
        'client_id' => $this->client->id,
        'url' => 'https://example.com',
        'status' => WebsiteStatus::ACTIVE,
        'uptime_status' => UptimeStatus::UP,
    ]);

    $action = new CheckWebsiteUptimeAction();
    $result = $action($website);

    expect($result['status'])->toBe(UptimeStatus::DOWN)
        ->and($result['status_code'])->toBe(500);

    $website->refresh();
    expect($website->uptime_status)->toBe(UptimeStatus::DOWN);
});

test('can handle connection timeout', function () {
    Http::fake(function () {
        throw new \Exception('Connection timeout');
    });

    $website = Website::factory()->create([
        'client_id' => $this->client->id,
        'url' => 'https://example.com',
        'status' => WebsiteStatus::ACTIVE,
    ]);

    $action = new CheckWebsiteUptimeAction();
    $result = $action($website);

    expect($result['status'])->toBe(UptimeStatus::DOWN)
        ->and($result['error_message'])->toContain('timeout');

    $website->refresh();
    expect($website->uptime_status)->toBe(UptimeStatus::DOWN);
});

test('creates uptime check record in history', function () {
    Http::fake([
        'https://example.com' => Http::response('OK', 200),
    ]);

    $website = Website::factory()->create([
        'client_id' => $this->client->id,
        'url' => 'https://example.com',
        'status' => WebsiteStatus::ACTIVE,
    ]);

    expect(UptimeCheck::count())->toBe(0);

    $action = new CheckWebsiteUptimeAction();
    $action($website);

    expect(UptimeCheck::count())->toBe(1);

    $check = UptimeCheck::first();
    expect($check->website_id)->toBe($website->id)
        ->and($check->status)->toBe(UptimeStatus::UP)
        ->and($check->status_code)->toBe(200)
        ->and($check->response_time_ms)->toBeGreaterThan(0)
        ->and($check->checked_at)->not()->toBeNull();
});

test('dispatches uptime changed event when status changes', function () {
    Event::fake([WebsiteUptimeChanged::class]);

    Http::fake([
        'https://example.com' => Http::response('Server Error', 500),
    ]);

    $website = Website::factory()->create([
        'client_id' => $this->client->id,
        'url' => 'https://example.com',
        'status' => WebsiteStatus::ACTIVE,
        'uptime_status' => UptimeStatus::UP,
    ]);

    $action = new CheckWebsiteUptimeAction();
    $action($website);

    Event::assertDispatched(WebsiteUptimeChanged::class, function ($event) use ($website) {
        return $event->website->id === $website->id
            && $event->previousStatus === UptimeStatus::UP
            && $event->newStatus === UptimeStatus::DOWN
            && $event->wentDown();
    });
});

test('does not dispatch event when status unchanged', function () {
    Event::fake([WebsiteUptimeChanged::class]);

    Http::fake([
        'https://example.com' => Http::response('OK', 200),
    ]);

    $website = Website::factory()->create([
        'client_id' => $this->client->id,
        'url' => 'https://example.com',
        'status' => WebsiteStatus::ACTIVE,
        'uptime_status' => UptimeStatus::UP,
    ]);

    $action = new CheckWebsiteUptimeAction();
    $action($website);

    Event::assertNotDispatched(WebsiteUptimeChanged::class);
});

test('can track uptime check history over time', function () {
    Http::fake([
        'https://example.com' => Http::response('OK', 200),
    ]);

    $website = Website::factory()->create([
        'client_id' => $this->client->id,
        'url' => 'https://example.com',
        'status' => WebsiteStatus::ACTIVE,
    ]);

    $action = new CheckWebsiteUptimeAction();

    // Perform 5 checks
    for ($i = 0; $i < 5; $i++) {
        $action($website);
    }

    expect(UptimeCheck::where('website_id', $website->id)->count())->toBe(5);

    // Verify relationship
    expect($website->uptimeChecks)->toHaveCount(5)
        ->and($website->uptimeChecks->first()->wasSuccessful())->toBeTrue();
});
