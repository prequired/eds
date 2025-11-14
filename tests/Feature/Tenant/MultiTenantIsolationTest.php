<?php

use App\Models\Central\Tenant;
use App\Models\Central\User;
use App\Models\Tenant\Client;
use function Pest\Laravel\actingAs;

beforeEach(function () {
    // Create first tenant
    $this->tenant1 = Tenant::create([
        'company_name' => 'Agency One',
        'subdomain' => 'agency-one',
        'owner_email' => 'owner@agency-one.test',
        'plan' => 'professional',
        'status' => 'active',
    ]);

    // Create second tenant
    $this->tenant2 = Tenant::create([
        'company_name' => 'Agency Two',
        'subdomain' => 'agency-two',
        'owner_email' => 'owner@agency-two.test',
        'plan' => 'professional',
        'status' => 'active',
    ]);

    // Create user for tenant1
    $this->user1 = User::factory()
        ->for($this->tenant1)
        ->create(['role' => 'owner']);

    // Create user for tenant2
    $this->user2 = User::factory()
        ->for($this->tenant2)
        ->create(['role' => 'owner']);
});

test('tenants have separate databases', function () {
    // Initialize tenant1
    tenancy()->initialize($this->tenant1);

    // Create client in tenant1
    $client1 = Client::factory()->create(['name' => 'Tenant 1 Client']);
    expect(Client::count())->toBe(1);

    // Switch to tenant2
    tenancy()->end();
    tenancy()->initialize($this->tenant2);

    // Tenant2 should see no clients
    expect(Client::count())->toBe(0);

    // Create client in tenant2
    $client2 = Client::factory()->create(['name' => 'Tenant 2 Client']);
    expect(Client::count())->toBe(1);

    // Switch back to tenant1
    tenancy()->end();
    tenancy()->initialize($this->tenant1);

    // Tenant1 should still see only their client
    expect(Client::count())->toBe(1);
    expect(Client::first()->name)->toBe('Tenant 1 Client');
});

test('tenant cannot access another tenants client data', function () {
    // Create client in tenant1
    tenancy()->initialize($this->tenant1);
    $client1 = Client::factory()->create(['name' => 'Tenant 1 Client']);
    $clientId = $client1->id;

    // Switch to tenant2
    tenancy()->end();
    tenancy()->initialize($this->tenant2);

    // Attempting to find tenant1's client should return null
    expect(Client::find($clientId))->toBeNull();
});

test('tenant limits are enforced', function () {
    // Set client limit for tenant
    $this->tenant1->update(['client_limit' => 2]);

    tenancy()->initialize($this->tenant1);

    // Create 2 clients (at limit)
    Client::factory()->count(2)->create();

    // Verify we can't add more
    expect($this->tenant1->canAddClient())->toBeFalse();

    // Verify unlimited tenant can add
    $this->tenant1->update(['client_limit' => null]);
    expect($this->tenant1->canAddClient())->toBeTrue();
});

test('session is isolated per tenant', function () {
    actingAs($this->user1);

    tenancy()->initialize($this->tenant1);
    expect(auth()->user()->tenant_id)->toBe($this->tenant1->id);

    tenancy()->end();
    tenancy()->initialize($this->tenant2);

    // User from tenant1 shouldn't be authenticated in tenant2
    expect(auth()->check())->toBeFalse();
});

test('queries are scoped to tenant database', function () {
    tenancy()->initialize($this->tenant1);
    Client::factory()->count(5)->create();

    tenancy()->end();
    tenancy()->initialize($this->tenant2);
    Client::factory()->count(3)->create();

    // Each tenant sees only their data
    expect(Client::count())->toBe(3);

    tenancy()->end();
    tenancy()->initialize($this->tenant1);
    expect(Client::count())->toBe(5);
});

test('tenant subdomain is enforced', function () {
    expect($this->tenant1->subdomain)->toBe('agency-one');
    expect($this->tenant2->subdomain)->toBe('agency-two');

    // Subdomains must be unique
    expect(function () {
        Tenant::create([
            'company_name' => 'Duplicate',
            'subdomain' => 'agency-one', // Duplicate!
            'owner_email' => 'dupe@test.com',
        ]);
    })->toThrow(\Exception::class);
});
