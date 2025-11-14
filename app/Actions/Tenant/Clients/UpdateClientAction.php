<?php

namespace App\Actions\Tenant\Clients;

use App\Data\Tenant\Clients\UpdateClientData;
use App\Models\Tenant\Client;
use Illuminate\Support\Facades\DB;

class UpdateClientAction
{
    public function __invoke(Client $client, UpdateClientData $data): Client
    {
        return DB::transaction(function () use ($client, $data) {
            $client->update([
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
                'status' => $data->status,
                'notes' => $data->notes,
                'tags' => $data->tags,
            ]);

            $client->refresh();

            return $client;
        });
    }
}
