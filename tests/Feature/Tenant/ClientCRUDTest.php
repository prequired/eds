<?php

use App\Actions\Tenant\Clients\CreateClientAction;
use App\Actions\Tenant\Clients\UpdateClientAction;
use App\Actions\Tenant\Clients\DeleteClientAction;
use App\Data\Tenant\Clients\CreateClientData;
use App\Data\Tenant\Clients\UpdateClientData;
use App\Enums\ClientStatus;
use App\Events\Tenant\ClientCreated;
use App\Models\Central\Tenant;
use App\Models\Tenant\Client;
use Illuminate\Support\Facades\Event;

beforeEach(function () {
    $this->tenant = Tenant::create([
        'company_name' => 'Test Agency',
        'subdomain' => 'test-agency',
        'owner_email' => 'owner@test.com',
        'plan' => 'professional',
        'status' => 'active',
        'client_limit' => 20,
    ]);

    tenancy()->initialize($this->tenant);
});

test('can create client with valid data', function () {
    Event::fake();

    $data = CreateClientData::from([
        'name' => 'Acme Corporation',
        'company' => 'Acme Corp',
        'email' => 'contact@acme.com',
        'phone' => '(406) 555-1234',
        'website' => 'https://acme.com',
        'addressLine1' => '123 Main St',
        'addressLine2' => 'Suite 100',
        'city' => 'Missoula',
        'state' => 'Montana',
        'postalCode' => '59801',
        'country' => 'US',
        'billingEmail' => 'billing@acme.com',
        'monthlyRetainerCents' => 500000, // $5,000
        'currency' => 'usd',
        'paymentTerms' => 30,
        'notes' => 'Important client',
        'tags' => ['vip', 'ecommerce'],
    ]);

    $action = new CreateClientAction();
    $client = $action($data);

    expect($client)->toBeInstanceOf(Client::class);
    expect($client->name)->toBe('Acme Corporation');
    expect($client->email)->toBe('contact@acme.com');
    expect($client->monthly_retainer_cents)->toBe(500000);
    expect($client->status->value)->toBe('active');
    expect($client->settings)->toHaveKey('portal_access');

    Event::assertDispatched(ClientCreated::class);
});

test('cannot create client when limit is reached', function () {
    $this->tenant->update(['client_limit' => 1]);

    // Create one client (at limit)
    Client::factory()->create();

    $data = CreateClientData::from([
        'name' => 'Test Client',
        'company' => null,
        'email' => null,
        'phone' => null,
        'website' => null,
        'addressLine1' => null,
        'addressLine2' => null,
        'city' => null,
        'state' => null,
        'postalCode' => null,
        'country' => 'US',
        'billingEmail' => null,
        'monthlyRetainerCents' => 0,
        'currency' => 'usd',
        'paymentTerms' => 30,
        'notes' => null,
        'tags' => [],
    ]);

    $action = new CreateClientAction();

    expect(fn() => $action($data))
        ->toThrow(\Exception::class, 'Client limit reached');
});

test('can update client', function () {
    $client = Client::factory()->create([
        'name' => 'Old Name',
        'status' => 'active',
    ]);

    $data = UpdateClientData::from([
        'name' => 'New Name',
        'company' => $client->company,
        'email' => $client->email,
        'phone' => $client->phone,
        'website' => $client->website,
        'addressLine1' => $client->address_line1,
        'addressLine2' => $client->address_line2,
        'city' => $client->city,
        'state' => $client->state,
        'postalCode' => $client->postal_code,
        'country' => $client->country,
        'billingEmail' => $client->billing_email,
        'monthlyRetainerCents' => $client->monthly_retainer_cents,
        'currency' => $client->currency,
        'paymentTerms' => $client->payment_terms,
        'status' => ClientStatus::INACTIVE,
        'notes' => 'Updated notes',
        'tags' => ['updated'],
    ]);

    $action = new UpdateClientAction();
    $updatedClient = $action($client, $data);

    expect($updatedClient->name)->toBe('New Name');
    expect($updatedClient->status)->toBe(ClientStatus::INACTIVE);
    expect($updatedClient->notes)->toBe('Updated notes');
});

test('can delete (archive) client', function () {
    $client = Client::factory()->create();

    $action = new DeleteClientAction();
    $result = $action($client);

    expect($result)->toBeTrue();

    $client->refresh();
    expect($client->archived_at)->not->toBeNull();
});

test('archived clients are excluded from queries', function () {
    Client::factory()->count(5)->create();
    Client::factory()->count(3)->create(['archived_at' => now()]);

    expect(Client::whereNull('archived_at')->count())->toBe(5);
    expect(Client::count())->toBe(8); // All including archived
});

test('client name is required', function () {
    expect(function () {
        CreateClientData::from([
            'name' => '', // Empty name
            'company' => null,
            'email' => null,
            'phone' => null,
            'website' => null,
            'addressLine1' => null,
            'addressLine2' => null,
            'city' => null,
            'state' => null,
            'postalCode' => null,
            'country' => 'US',
            'billingEmail' => null,
            'monthlyRetainerCents' => 0,
            'currency' => 'usd',
            'paymentTerms' => 30,
            'notes' => null,
            'tags' => [],
        ]);
    })->toThrow(\Exception::class);
});

test('client email must be valid', function () {
    expect(function () {
        CreateClientData::from([
            'name' => 'Test Client',
            'company' => null,
            'email' => 'invalid-email', // Invalid
            'phone' => null,
            'website' => null,
            'addressLine1' => null,
            'addressLine2' => null,
            'city' => null,
            'state' => null,
            'postalCode' => null,
            'country' => 'US',
            'billingEmail' => null,
            'monthlyRetainerCents' => 0,
            'currency' => 'usd',
            'paymentTerms' => 30,
            'notes' => null,
            'tags' => [],
        ]);
    })->toThrow(\Exception::class);
});

test('can list active clients', function () {
    Client::factory()->count(5)->create(['status' => 'active']);
    Client::factory()->count(2)->create(['status' => 'inactive']);

    $activeClients = Client::active()->get();

    expect($activeClients)->toHaveCount(5);
});

test('client monthly retainer is formatted correctly', function () {
    $client = Client::factory()->create([
        'monthly_retainer_cents' => 150000, // $1,500.00
    ]);

    expect($client->monthly_retainer)->toBe('$1,500.00');
});
