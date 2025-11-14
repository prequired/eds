<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Models\User;

uses()->group('auth');

beforeEach(function () {
    // Initialize tenancy for testing
    $this->tenant = createTenant();
    tenancy()->initialize($this->tenant);
});

afterEach(function () {
    tenancy()->end();
});

test('owner has all permissions', function () {
    $owner = User::factory()->create(['role' => UserRole::OWNER]);

    expect($owner->isOwner())->toBeTrue()
        ->and($owner->hasPermission('clients.create'))->toBeTrue()
        ->and($owner->hasPermission('clients.delete'))->toBeTrue()
        ->and($owner->hasPermission('settings.update'))->toBeTrue()
        ->and($owner->hasPermission('team.delete'))->toBeTrue()
        ->and($owner->hasPermission('any.random.permission'))->toBeTrue();
});

test('admin has default admin permissions', function () {
    $admin = User::factory()->create(['role' => UserRole::ADMIN]);

    expect($admin->isAdmin())->toBeTrue()
        ->and($admin->hasPermission('clients.create'))->toBeTrue()
        ->and($admin->hasPermission('clients.view'))->toBeTrue()
        ->and($admin->hasPermission('projects.create'))->toBeTrue()
        ->and($admin->hasPermission('websites.update'))->toBeTrue()
        ->and($admin->hasPermission('team.view'))->toBeTrue();
});

test('admin does not have owner-only permissions', function () {
    $admin = User::factory()->create(['role' => UserRole::ADMIN]);

    expect($admin->hasPermission('team.delete'))->toBeFalse()
        ->and($admin->hasPermission('settings.delete'))->toBeFalse();
});

test('member has limited permissions', function () {
    $member = User::factory()->create(['role' => UserRole::MEMBER]);

    expect($member->isMember())->toBeTrue()
        ->and($member->hasPermission('clients.view'))->toBeTrue()
        ->and($member->hasPermission('projects.view'))->toBeTrue()
        ->and($member->hasPermission('tasks.create'))->toBeTrue()
        ->and($member->hasPermission('time_entries.create'))->toBeTrue();
});

test('member does not have admin permissions', function () {
    $member = User::factory()->create(['role' => UserRole::MEMBER]);

    expect($member->hasPermission('clients.delete'))->toBeFalse()
        ->and($member->hasPermission('websites.create'))->toBeFalse()
        ->and($member->hasPermission('settings.view'))->toBeFalse();
});

test('wildcard permissions work correctly', function () {
    $admin = User::factory()->create(['role' => UserRole::ADMIN]);

    // Admin has 'clients.*' permission
    expect($admin->hasPermission('clients.create'))->toBeTrue()
        ->and($admin->hasPermission('clients.view'))->toBeTrue()
        ->and($admin->hasPermission('clients.update'))->toBeTrue()
        ->and($admin->hasPermission('clients.delete'))->toBeTrue();
});

test('custom permissions override default role permissions', function () {
    // Create member with custom permission
    $member = User::factory()->create([
        'role' => UserRole::MEMBER,
        'permissions' => ['clients.delete'], // Custom permission
    ]);

    expect($member->hasPermission('clients.delete'))->toBeTrue() // Custom permission
        ->and($member->hasPermission('clients.view'))->toBeTrue() // Default member permission
        ->and($member->hasPermission('settings.update'))->toBeFalse(); // Still no admin permissions
});

test('user can method integrates with permission system', function () {
    $owner = User::factory()->create(['role' => UserRole::OWNER]);
    $member = User::factory()->create(['role' => UserRole::MEMBER]);

    expect($owner->can('clients.delete'))->toBeTrue()
        ->and($member->can('clients.delete'))->toBeFalse();
});

test('role labels are correct', function () {
    expect(UserRole::OWNER->label())->toBe('Owner')
        ->and(UserRole::ADMIN->label())->toBe('Admin')
        ->and(UserRole::MEMBER->label())->toBe('Member');
});
