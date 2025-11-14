<?php

namespace App\Livewire\Tenant\Clients;

use App\Actions\Tenant\Clients\DeleteClientAction;
use App\Models\Tenant\Client;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class ClientList extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url]
    public string $status = 'active';

    #[Url]
    public string $sortBy = 'name';

    #[Url]
    public string $sortDirection = 'asc';

    public bool $showFilters = false;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function archive(string $clientId): void
    {
        $client = Client::findOrFail($clientId);

        $action = new DeleteClientAction();
        $action($client);

        $this->dispatch('client-archived', clientId: $clientId);

        session()->flash('success', 'Client archived successfully.');
    }

    public function render()
    {
        $clients = Client::query()
            ->when($this->search, fn($q) =>
                $q->where('name', 'ilike', "%{$this->search}%")
                    ->orWhere('email', 'ilike', "%{$this->search}%")
                    ->orWhere('company', 'ilike', "%{$this->search}%")
            )
            ->when($this->status === 'archived', fn($q) =>
                $q->whereNotNull('archived_at')
            )
            ->when($this->status !== 'archived' && $this->status !== 'all', fn($q) =>
                $q->where('status', $this->status)->whereNull('archived_at')
            )
            ->when($this->status === 'all', fn($q) =>
                $q->whereNull('archived_at')
            )
            ->withCount(['projects', 'websites'])
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(24);

        return view('livewire.tenant.clients.client-list', [
            'clients' => $clients,
        ])->layout('layouts.tenant', ['header' => 'Clients']);
    }
}
