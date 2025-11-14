<?php

namespace App\Actions\Tenant\Clients;

use App\Models\Tenant\Client;
use Illuminate\Support\Facades\DB;

class DeleteClientAction
{
    public function __invoke(Client $client): bool
    {
        return DB::transaction(function () use ($client) {
            // Soft delete (archive)
            $client->update(['archived_at' => now()]);

            return true;
        });
    }
}
