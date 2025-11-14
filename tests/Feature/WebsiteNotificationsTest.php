<?php

declare(strict_types=1);

use App\Actions\Tenant\Websites\CheckWebsiteUptimeAction;
use App\Enums\UptimeStatus;
use App\Enums\WebsiteStatus;
use App\Events\Tenant\WebsiteUptimeChanged;
use App\Models\Tenant\Client;
use App\Models\Tenant\Website;
use App\Notifications\WebsiteDownNotification;
use App\Notifications\WebsiteRecoveredNotification;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Notification;

uses()->group('notifications');

beforeEach(function () {
    // Initialize tenancy for testing
    $this->tenant = createTenant();
    tenancy()->initialize($this->tenant);

    // Create test client
    $this->client = Client::factory()->create();

    // Fake notifications
    Notification::fake();
});

afterEach(function () {
    tenancy()->end();
});

test('sends downtime notification when website goes down', function () {
    Http::fake([
        'https://example.com' => Http::response('Server Error', 500),
    ]);

    $website = Website::factory()->create([
        'client_id' => $this->client->id,
        'url' => 'https://example.com',
        'status' => WebsiteStatus::ACTIVE,
        'uptime_status' => UptimeStatus::UP,
        'notification_emails' => ['admin@example.com', 'ops@example.com'],
        'notify_on_downtime' => true,
    ]);

    $action = new CheckWebsiteUptimeAction();
    $action($website);

    // Assert notification was sent
    Notification::assertSentOnDemand(
        WebsiteDownNotification::class,
        function ($notification, $channels, $notifiable) use ($website) {
            return in_array('admin@example.com', $notifiable->routes['mail'])
                && in_array('ops@example.com', $notifiable->routes['mail'])
                && $notification->website->id === $website->id;
        }
    );

    // Assert last notified timestamp was updated
    $website->refresh();
    expect($website->last_notified_at)->not()->toBeNull();
});

test('sends recovery notification when website comes back up', function () {
    Http::fake([
        'https://example.com' => Http::response('OK', 200),
    ]);

    $website = Website::factory()->create([
        'client_id' => $this->client->id,
        'url' => 'https://example.com',
        'status' => WebsiteStatus::ACTIVE,
        'uptime_status' => UptimeStatus::DOWN,
        'notification_emails' => ['admin@example.com'],
        'notify_on_recovery' => true,
    ]);

    $action = new CheckWebsiteUptimeAction();
    $action($website);

    Notification::assertSentOnDemand(
        WebsiteRecoveredNotification::class,
        function ($notification, $channels, $notifiable) use ($website) {
            return in_array('admin@example.com', $notifiable->routes['mail'])
                && $notification->website->id === $website->id;
        }
    );
});

test('does not send notification when emails not configured', function () {
    Http::fake([
        'https://example.com' => Http::response('Server Error', 500),
    ]);

    $website = Website::factory()->create([
        'client_id' => $this->client->id,
        'url' => 'https://example.com',
        'status' => WebsiteStatus::ACTIVE,
        'uptime_status' => UptimeStatus::UP,
        'notification_emails' => null, // No emails configured
        'notify_on_downtime' => true,
    ]);

    $action = new CheckWebsiteUptimeAction();
    $action($website);

    Notification::assertNothingSentOnDemand();
});

test('respects notify_on_downtime setting', function () {
    Http::fake([
        'https://example.com' => Http::response('Server Error', 500),
    ]);

    $website = Website::factory()->create([
        'client_id' => $this->client->id,
        'url' => 'https://example.com',
        'status' => WebsiteStatus::ACTIVE,
        'uptime_status' => UptimeStatus::UP,
        'notification_emails' => ['admin@example.com'],
        'notify_on_downtime' => false, // Disabled
    ]);

    $action = new CheckWebsiteUptimeAction();
    $action($website);

    Notification::assertNothingSentOnDemand();
});

test('respects notify_on_recovery setting', function () {
    Http::fake([
        'https://example.com' => Http::response('OK', 200),
    ]);

    $website = Website::factory()->create([
        'client_id' => $this->client->id,
        'url' => 'https://example.com',
        'status' => WebsiteStatus::ACTIVE,
        'uptime_status' => UptimeStatus::DOWN,
        'notification_emails' => ['admin@example.com'],
        'notify_on_recovery' => false, // Disabled
    ]);

    $action = new CheckWebsiteUptimeAction();
    $action($website);

    Notification::assertNothingSentOnDemand();
});

test('downtime notification includes error details', function () {
    Http::fake([
        'https://example.com' => Http::response('Server Error', 500),
    ]);

    $website = Website::factory()->create([
        'client_id' => $this->client->id,
        'url' => 'https://example.com',
        'status' => WebsiteStatus::ACTIVE,
        'uptime_status' => UptimeStatus::UP,
        'notification_emails' => ['admin@example.com'],
        'notify_on_downtime' => true,
    ]);

    $action = new CheckWebsiteUptimeAction();
    $action($website);

    Notification::assertSentOnDemand(
        WebsiteDownNotification::class,
        function ($notification) {
            return $notification->errorMessage !== null
                && str_contains($notification->errorMessage, '500');
        }
    );
});

test('recovery notification includes downtime duration', function () {
    // First, create a down check in history
    Http::fake([
        'https://example.com' => Http::response('Server Error', 500),
    ]);

    $website = Website::factory()->create([
        'client_id' => $this->client->id,
        'url' => 'https://example.com',
        'status' => WebsiteStatus::ACTIVE,
        'uptime_status' => UptimeStatus::UP,
        'notification_emails' => null, // Don't notify for this one
    ]);

    $action = new CheckWebsiteUptimeAction();
    $action($website); // This creates a DOWN check

    // Now recover
    Http::fake([
        'https://example.com' => Http::response('OK', 200),
    ]);

    $website->update([
        'notification_emails' => ['admin@example.com'],
        'notify_on_recovery' => true,
    ]);

    $action($website);

    Notification::assertSentOnDemand(
        WebsiteRecoveredNotification::class,
        function ($notification) {
            return $notification->downtimeStart !== null;
        }
    );
});

test('does not send notification when status unchanged', function () {
    Http::fake([
        'https://example.com' => Http::response('OK', 200),
    ]);

    $website = Website::factory()->create([
        'client_id' => $this->client->id,
        'url' => 'https://example.com',
        'status' => WebsiteStatus::ACTIVE,
        'uptime_status' => UptimeStatus::UP,
        'notification_emails' => ['admin@example.com'],
        'notify_on_downtime' => true,
        'notify_on_recovery' => true,
    ]);

    $action = new CheckWebsiteUptimeAction();
    $action($website);

    // No status change (UP -> UP), so no notification
    Notification::assertNothingSentOnDemand();
});
