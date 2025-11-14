<?php

namespace App\Livewire\Tenant\Websites;

use App\Actions\Tenant\Websites\DeleteWebsiteAction;
use App\Enums\UptimeStatus;
use App\Enums\WebsiteStatus;
use App\Models\Tenant\Website;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class WebsiteList extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url]
    public string $status = 'active';

    #[Url]
    public string $environment = 'all';

    #[Url]
    public string $uptimeStatus = 'all';

    #[Url]
    public string $sortBy = 'created_at';

    #[Url]
    public string $sortDirection = 'desc';

    public bool $showFilters = false;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function updatingEnvironment(): void
    {
        $this->resetPage();
    }

    public function updatingUptimeStatus(): void
    {
        $this->resetPage();
    }

    public function archive(string $websiteId): void
    {
        $website = Website::findOrFail($websiteId);

        $action = new DeleteWebsiteAction();
        $action($website);

        session()->flash('success', 'Website archived successfully.');
    }

    public function render()
    {
        $websites = Website::query()
            ->with(['client', 'project'])
            ->when($this->search, fn($q) =>
                $q->where('name', 'ilike', "%{$this->search}%")
                    ->orWhere('url', 'ilike', "%{$this->search}%")
                    ->orWhereHas('client', function ($query) {
                        $query->where('name', 'ilike', "%{$this->search}%");
                    })
            )
            ->when($this->status === 'archived', fn($q) =>
                $q->whereNotNull('archived_at')
            )
            ->when($this->status !== 'archived' && $this->status !== 'all', fn($q) =>
                $q->whereNull('archived_at')
                    ->where('status', $this->status)
            )
            ->when($this->status === 'active', fn($q) =>
                $q->whereNull('archived_at')
                    ->where('status', WebsiteStatus::ACTIVE->value)
            )
            ->when($this->environment !== 'all', fn($q) =>
                $q->where('environment', $this->environment)
            )
            ->when($this->uptimeStatus !== 'all', fn($q) =>
                $q->where('uptime_status', $this->uptimeStatus)
            )
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(24);

        return view('livewire.tenant.websites.website-list', [
            'websites' => $websites,
        ])->layout('layouts.tenant', ['header' => 'Websites']);
    }
}
