<?php

declare(strict_types=1);

use App\Actions\Tenant\Team\AcceptTeamInvitationAction;
use App\Actions\Tenant\Team\CancelTeamInvitationAction;
use App\Actions\Tenant\Team\SendTeamInvitationAction;
use App\Data\Tenant\Team\AcceptTeamInvitationData;
use App\Data\Tenant\Team\SendTeamInvitationData;
use App\Enums\UserRole;
use App\Events\Tenant\TeamInvitationAccepted;
use App\Events\Tenant\TeamInvitationSent;
use App\Models\Tenant\TeamInvitation;
use App\Models\User;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;

uses()->group('team');

beforeEach(function () {
    // Initialize tenancy for testing
    $this->tenant = createTenant();
    tenancy()->initialize($this->tenant);

    // Create test user (inviter)
    $this->inviter = User::factory()->create(['role' => UserRole::ADMIN]);

    // Fake notifications
    Notification::fake();
});

afterEach(function () {
    tenancy()->end();
});

test('can send team invitation', function () {
    Event::fake();

    $data = SendTeamInvitationData::from([
        'email' => 'newuser@example.com',
        'role' => UserRole::MEMBER,
        'permissions' => null,
    ]);

    $action = new SendTeamInvitationAction();
    $invitation = $action($data, $this->inviter);

    expect($invitation)->toBeInstanceOf(TeamInvitation::class)
        ->and($invitation->email)->toBe('newuser@example.com')
        ->and($invitation->role)->toBe(UserRole::MEMBER)
        ->and($invitation->invited_by)->toBe($this->inviter->id)
        ->and($invitation->token)->not()->toBeNull()
        ->and($invitation->expires_at)->not()->toBeNull()
        ->and($invitation->isPending())->toBeTrue();

    Event::assertDispatched(TeamInvitationSent::class);
});

test('invitation has 7-day expiration', function () {
    $data = SendTeamInvitationData::from([
        'email' => 'newuser@example.com',
        'role' => UserRole::MEMBER,
    ]);

    $action = new SendTeamInvitationAction();
    $invitation = $action($data, $this->inviter);

    expect($invitation->expires_at->diffInDays(now()))->toBe(7);
});

test('cannot send invitation to existing user', function () {
    // Create existing user
    User::factory()->create(['email' => 'existing@example.com']);

    $data = SendTeamInvitationData::from([
        'email' => 'existing@example.com',
        'role' => UserRole::MEMBER,
    ]);

    $action = new SendTeamInvitationAction();

    expect(fn() => $action($data, $this->inviter))
        ->toThrow(\Exception::class, 'User with this email already exists');
});

test('cannot send duplicate pending invitation', function () {
    // Create first invitation
    $data = SendTeamInvitationData::from([
        'email' => 'newuser@example.com',
        'role' => UserRole::MEMBER,
    ]);

    $action = new SendTeamInvitationAction();
    $action($data, $this->inviter);

    // Try to create duplicate
    expect(fn() => $action($data, $this->inviter))
        ->toThrow(\Exception::class, 'An invitation has already been sent');
});

test('can accept team invitation', function () {
    Event::fake();

    // Create invitation
    $invitation = TeamInvitation::factory()->create([
        'email' => 'newuser@example.com',
        'role' => UserRole::MEMBER,
        'invited_by' => $this->inviter->id,
    ]);

    $data = AcceptTeamInvitationData::from([
        'name' => 'New User',
        'password' => 'password123',
    ]);

    $action = new AcceptTeamInvitationAction();
    $user = $action($invitation, $data);

    expect($user)->toBeInstanceOf(User::class)
        ->and($user->name)->toBe('New User')
        ->and($user->email)->toBe('newuser@example.com')
        ->and($user->role)->toBe(UserRole::MEMBER)
        ->and($user->email_verified_at)->not()->toBeNull(); // Auto-verified

    $invitation->refresh();
    expect($invitation->isAccepted())->toBeTrue()
        ->and($invitation->accepted_at)->not()->toBeNull();

    Event::assertDispatched(TeamInvitationAccepted::class);
});

test('cannot accept expired invitation', function () {
    $invitation = TeamInvitation::factory()->create([
        'email' => 'newuser@example.com',
        'expires_at' => now()->subDay(), // Expired yesterday
    ]);

    $data = AcceptTeamInvitationData::from([
        'name' => 'New User',
        'password' => 'password123',
    ]);

    $action = new AcceptTeamInvitationAction();

    expect(fn() => $action($invitation, $data))
        ->toThrow(\Exception::class, 'This invitation has expired');
});

test('cannot accept already accepted invitation', function () {
    $invitation = TeamInvitation::factory()->create([
        'email' => 'newuser@example.com',
        'accepted_at' => now(), // Already accepted
    ]);

    $data = AcceptTeamInvitationData::from([
        'name' => 'New User',
        'password' => 'password123',
    ]);

    $action = new AcceptTeamInvitationAction();

    expect(fn() => $action($invitation, $data))
        ->toThrow(\Exception::class, 'This invitation has already been accepted');
});

test('can cancel pending invitation', function () {
    $invitation = TeamInvitation::factory()->create([
        'email' => 'newuser@example.com',
        'invited_by' => $this->inviter->id,
    ]);

    $action = new CancelTeamInvitationAction();
    $action($invitation);

    expect(TeamInvitation::find($invitation->id))->toBeNull();
});

test('cannot cancel accepted invitation', function () {
    $invitation = TeamInvitation::factory()->create([
        'email' => 'newuser@example.com',
        'accepted_at' => now(),
    ]);

    $action = new CancelTeamInvitationAction();

    expect(fn() => $action($invitation))
        ->toThrow(\Exception::class, 'Cannot cancel an invitation that has already been accepted');
});

test('invitation scopes work correctly', function () {
    // Create various invitations
    $pending = TeamInvitation::factory()->create([
        'email' => 'pending@example.com',
        'expires_at' => now()->addDays(7),
    ]);

    $expired = TeamInvitation::factory()->create([
        'email' => 'expired@example.com',
        'expires_at' => now()->subDay(),
    ]);

    $accepted = TeamInvitation::factory()->create([
        'email' => 'accepted@example.com',
        'accepted_at' => now(),
    ]);

    // Test scopes
    $pendingInvitations = TeamInvitation::pending()->get();
    expect($pendingInvitations)->toHaveCount(1)
        ->and($pendingInvitations->first()->id)->toBe($pending->id);

    $expiredInvitations = TeamInvitation::expired()->get();
    expect($expiredInvitations)->toHaveCount(1)
        ->and($expiredInvitations->first()->id)->toBe($expired->id);

    $acceptedInvitations = TeamInvitation::accepted()->get();
    expect($acceptedInvitations)->toHaveCount(1)
        ->and($acceptedInvitations->first()->id)->toBe($accepted->id);
});

test('invitation with custom permissions', function () {
    $data = SendTeamInvitationData::from([
        'email' => 'custom@example.com',
        'role' => UserRole::MEMBER,
        'permissions' => ['clients.delete', 'projects.delete'],
    ]);

    $action = new SendTeamInvitationAction();
    $invitation = $action($data, $this->inviter);

    expect($invitation->permissions)->toBe(['clients.delete', 'projects.delete']);

    // Accept invitation
    $acceptData = AcceptTeamInvitationData::from([
        'name' => 'Custom User',
        'password' => 'password123',
    ]);

    $acceptAction = new AcceptTeamInvitationAction();
    $user = $acceptAction($invitation, $acceptData);

    // User should have custom permissions
    expect($user->permissions)->toBe(['clients.delete', 'projects.delete'])
        ->and($user->hasPermission('clients.delete'))->toBeTrue()
        ->and($user->hasPermission('projects.delete'))->toBeTrue();
});

test('invitation helper methods work correctly', function () {
    $pending = TeamInvitation::factory()->create([
        'email' => 'pending@example.com',
        'expires_at' => now()->addDays(7),
    ]);

    $expired = TeamInvitation::factory()->create([
        'email' => 'expired@example.com',
        'expires_at' => now()->subDay(),
    ]);

    $accepted = TeamInvitation::factory()->create([
        'email' => 'accepted@example.com',
        'accepted_at' => now(),
    ]);

    // Test isPending()
    expect($pending->isPending())->toBeTrue()
        ->and($expired->isPending())->toBeFalse()
        ->and($accepted->isPending())->toBeFalse();

    // Test isExpired()
    expect($pending->isExpired())->toBeFalse()
        ->and($expired->isExpired())->toBeTrue()
        ->and($accepted->isExpired())->toBeFalse();

    // Test isAccepted()
    expect($pending->isAccepted())->toBeFalse()
        ->and($expired->isAccepted())->toBeFalse()
        ->and($accepted->isAccepted())->toBeTrue();
});
