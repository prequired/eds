<?php

declare(strict_types=1);

namespace App\Livewire\Tenant\Invoice;

use App\Enums\InvoiceStatus;
use App\Models\Tenant\Invoice;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class InvoiceList extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'status')]
    public string $statusFilter = 'all';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function deleteInvoice(string $invoiceId)
    {
        if (!auth()->user()->can('invoices.delete')) {
            $this->dispatch('notification', [
                'type' => 'error',
                'message' => 'You do not have permission to delete invoices.',
            ]);
            return;
        }

        $invoice = Invoice::findOrFail($invoiceId);

        if ($invoice->payments()->count() > 0) {
            $this->dispatch('notification', [
                'type' => 'error',
                'message' => 'Cannot delete invoice with payments.',
            ]);
            return;
        }

        $invoice->delete();

        $this->dispatch('notification', [
            'type' => 'success',
            'message' => 'Invoice deleted successfully.',
        ]);
    }

    public function render()
    {
        $query = Invoice::with(['client'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('invoice_number', 'ilike', "%{$this->search}%")
                        ->orWhereHas('client', function ($clientQuery) {
                            $clientQuery->where('name', 'ilike', "%{$this->search}%");
                        });
                });
            })
            ->when($this->statusFilter !== 'all', function ($query) {
                if ($this->statusFilter === 'overdue') {
                    $query->overdue();
                } elseif ($this->statusFilter === 'unpaid') {
                    $query->unpaid();
                } else {
                    $query->where('status', $this->statusFilter);
                }
            })
            ->orderBy('issue_date', 'desc');

        $invoices = $query->paginate(15);

        // Calculate summary stats
        $stats = [
            'total' => Invoice::count(),
            'draft' => Invoice::draft()->count(),
            'sent' => Invoice::sent()->count(),
            'overdue' => Invoice::overdue()->count(),
            'unpaid' => Invoice::unpaid()->count(),
        ];

        return view('livewire.tenant.invoice.invoice-list', [
            'invoices' => $invoices,
            'stats' => $stats,
            'statuses' => InvoiceStatus::cases(),
        ])->layout('layouts.tenant', [
            'title' => 'Invoices',
            'header' => 'Invoices',
        ]);
    }
}
