<?php

namespace App\Actions\Tenant\Clients;

use App\Data\Tenant\Clients\CreateClientData;
use App\Events\Tenant\ClientCreated;
use App\Models\Tenant\Client;
use Illuminate\Support\Facades\DB;

class CreateClientAction
{
    public function __invoke(CreateClientData $data): Client
    {
        // Check tenant limits
        if (!tenancy()->tenant->canAddClient()) {
            throw new \Exception('Client limit reached for your plan. Please upgrade to add more clients.');
        }

        return DB::transaction(function () use ($data) {
            $client = Client::create([
                'name' => $data->name,
                'company' => $data->company,
                'email' => $data->email,
                'phone' => $data->phone,
                'website' => $data->website,
                'address_line1' => $data->addressLine1,
                'address_line2' => $data->addressLine2,
                'city' => $data->city,
                'state' => $data->state,
                'postal_code' => $data->postalCode,
                'country' => $data->country,
                'billing_email' => $data->billingEmail,
                'monthly_retainer_cents' => $data->monthlyRetainerCents,
                'currency' => $data->currency,
                'payment_terms' => $data->paymentTerms,
                'status' => 'active',
                'notes' => $data->notes,
                'tags' => $data->tags,
                'settings' => [
                    'portal_access' => true,
                    'send_automated_reports' => true,
                    'report_frequency' => 'weekly',
                ],
            ]);

            // Dispatch event
            ClientCreated::dispatch($client);

            return $client;
        });
    }
}
