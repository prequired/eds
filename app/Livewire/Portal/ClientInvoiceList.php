<?php

declare(strict_types=1);

namespace App\Livewire\Portal;

use App\Enums\InvoiceStatus;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class ClientInvoiceList extends Component
{
    use WithPagination;

    #[Url]
    public string $status = 'all';

    #[Url]
    public string $sortBy = 'invoice_date';

    #[Url]
    public string $sortDirection = 'desc';

    public function updatingStatus(): void
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

    public function getInvoicesProperty()
    {
        $client = auth('client')->user();
        $query = $client->invoices();

        // Filter by status
        if ($this->status !== 'all') {
            if ($this->status === 'unpaid') {
                $query->whereIn('status', [
                    InvoiceStatus::SENT,
                    InvoiceStatus::PARTIAL,
                    InvoiceStatus::OVERDUE,
                ]);
            } elseif ($this->status === 'paid') {
                $query->where('status', InvoiceStatus::PAID);
            } elseif ($this->status === 'overdue') {
                $query->where('status', InvoiceStatus::OVERDUE);
            }
        }

        // Sort
        $query->orderBy($this->sortBy, $this->sortDirection);

        return $query->paginate(15);
    }

    public function getTotalStatsProperty(): array
    {
        $client = auth('client')->user();

        return [
            'total_invoices' => $client->invoices()->count(),
            'unpaid_invoices' => $client->invoices()
                ->whereIn('status', [
                    InvoiceStatus::SENT,
                    InvoiceStatus::PARTIAL,
                    InvoiceStatus::OVERDUE,
                ])
                ->count(),
            'total_outstanding' => $client->invoices()
                ->whereIn('status', [
                    InvoiceStatus::SENT,
                    InvoiceStatus::PARTIAL,
                    InvoiceStatus::OVERDUE,
                ])
                ->sum('balance_due'),
        ];
    }

    #[Layout('layouts.portal')]
    public function render(): View
    {
        return view('livewire.portal.client-invoice-list');
    }
}
