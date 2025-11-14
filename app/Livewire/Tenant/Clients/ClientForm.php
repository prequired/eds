<?php

namespace App\Livewire\Tenant\Clients;

use App\Actions\Tenant\Clients\CreateClientAction;
use App\Actions\Tenant\Clients\UpdateClientAction;
use App\Data\Tenant\Clients\CreateClientData;
use App\Data\Tenant\Clients\UpdateClientData;
use App\Models\Tenant\Client;
use Livewire\Attributes\Validate;
use Livewire\Component;

class ClientForm extends Component
{
    public ?string $clientId = null;

    #[Validate('required|min:2|max:255')]
    public string $name = '';

    #[Validate('nullable|email|max:255')]
    public ?string $email = null;

    #[Validate('nullable|max:20')]
    public ?string $phone = null;

    #[Validate('nullable|max:255')]
    public ?string $company = null;

    #[Validate('nullable|max:255')]
    public ?string $website = null;

    #[Validate('nullable|max:1000')]
    public ?string $address = null;

    #[Validate('nullable|max:100')]
    public ?string $city = null;

    #[Validate('nullable|max:100')]
    public ?string $state = null;

    #[Validate('nullable|max:20')]
    public ?string $zip = null;

    #[Validate('nullable|max:100')]
    public ?string $country = null;

    #[Validate('nullable|integer|min:0')]
    public ?int $hourly_rate = null;

    #[Validate('nullable|max:3')]
    public ?string $currency = 'USD';

    #[Validate('nullable|max:50')]
    public ?string $billing_email = null;

    #[Validate('nullable|max:50')]
    public ?string $billing_name = null;

    #[Validate('nullable|max:1000')]
    public ?string $billing_address = null;

    #[Validate('nullable|max:5000')]
    public ?string $notes = null;

    public function mount(?string $clientId = null): void
    {
        $this->clientId = $clientId;

        if ($clientId) {
            $client = Client::findOrFail($clientId);

            $this->name = $client->name;
            $this->email = $client->email;
            $this->phone = $client->phone;
            $this->company = $client->company;
            $this->website = $client->website;
            $this->address = $client->address;
            $this->city = $client->city;
            $this->state = $client->state;
            $this->zip = $client->zip;
            $this->country = $client->country;
            $this->hourly_rate = $client->hourly_rate;
            $this->currency = $client->currency ?? 'USD';
            $this->billing_email = $client->billing_email;
            $this->billing_name = $client->billing_name;
            $this->billing_address = $client->billing_address;
            $this->notes = $client->notes;
        }
    }

    public function save(): void
    {
        $this->validate();

        try {
            if ($this->clientId) {
                // Update existing client
                $client = Client::findOrFail($this->clientId);
                $data = UpdateClientData::from([
                    'name' => $this->name,
                    'email' => $this->email,
                    'phone' => $this->phone,
                    'company' => $this->company,
                    'website' => $this->website,
                    'address' => $this->address,
                    'city' => $this->city,
                    'state' => $this->state,
                    'zip' => $this->zip,
                    'country' => $this->country,
                    'hourly_rate' => $this->hourly_rate,
                    'currency' => $this->currency,
                    'billing_email' => $this->billing_email,
                    'billing_name' => $this->billing_name,
                    'billing_address' => $this->billing_address,
                    'notes' => $this->notes,
                ]);

                $action = new UpdateClientAction();
                $action($client, $data);

                session()->flash('success', 'Client updated successfully.');
            } else {
                // Create new client
                $data = CreateClientData::from([
                    'name' => $this->name,
                    'email' => $this->email,
                    'phone' => $this->phone,
                    'company' => $this->company,
                    'website' => $this->website,
                    'address' => $this->address,
                    'city' => $this->city,
                    'state' => $this->state,
                    'zip' => $this->zip,
                    'country' => $this->country,
                    'hourly_rate' => $this->hourly_rate,
                    'currency' => $this->currency,
                    'billing_email' => $this->billing_email,
                    'billing_name' => $this->billing_name,
                    'billing_address' => $this->billing_address,
                    'notes' => $this->notes,
                ]);

                $action = new CreateClientAction();
                $action($data);

                session()->flash('success', 'Client created successfully.');
            }

            $this->redirect(route('clients.index'), navigate: true);
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.tenant.clients.client-form')
            ->layout('layouts.tenant', [
                'header' => $this->clientId ? 'Edit Client' : 'Create Client'
            ]);
    }
}
