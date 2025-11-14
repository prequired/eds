<?php

declare(strict_types=1);

namespace App\Livewire\Portal;

use App\Enums\InvoiceStatus;
use App\Models\Tenant\Invoice;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Layout;
use Livewire\Component;

class ClientInvoiceView extends Component
{
    public Invoice $invoice;

    public bool $showPaymentModal = false;

    public function mount(Invoice $invoice): void
    {
        $client = auth('client')->user();

        // Ensure this invoice belongs to the authenticated client
        if ($invoice->client_id !== $client->id) {
            abort(403, 'Unauthorized access to this invoice.');
        }

        $this->invoice = $invoice;
    }

    public function openPaymentModal(): void
    {
        // Check if invoice can be paid
        if (!in_array($this->invoice->status, [
            InvoiceStatus::SENT,
            InvoiceStatus::PARTIAL,
            InvoiceStatus::OVERDUE,
        ])) {
            $this->dispatch('notification', [
                'type' => 'error',
                'message' => 'This invoice cannot be paid at this time.',
            ]);
            return;
        }

        $this->showPaymentModal = true;
    }

    public function closePaymentModal(): void
    {
        $this->showPaymentModal = false;
    }

    public function downloadPdf(): void
    {
        // TODO: Implement PDF generation
        $this->dispatch('notification', [
            'type' => 'info',
            'message' => 'PDF download will be available soon.',
        ]);
    }

    public function canPay(): bool
    {
        return in_array($this->invoice->status, [
            InvoiceStatus::SENT,
            InvoiceStatus::PARTIAL,
            InvoiceStatus::OVERDUE,
        ]) && $this->invoice->balance_due > 0;
    }

    #[Layout('layouts.portal')]
    public function render(): View
    {
        return view('livewire.portal.client-invoice-view');
    }
}
