<?php

declare(strict_types=1);

namespace App\Livewire\Tenant\Invoice;

use App\Actions\Tenant\Invoice\CancelInvoiceAction;
use App\Actions\Tenant\Invoice\RecordPaymentAction;
use App\Actions\Tenant\Invoice\SendInvoiceAction;
use App\Data\Tenant\Invoice\RecordPaymentData;
use App\Enums\InvoiceStatus;
use App\Enums\PaymentMethod;
use App\Models\Tenant\Invoice;
use Carbon\Carbon;
use Livewire\Attributes\Validate;
use Livewire\Component;

class InvoiceView extends Component
{
    public Invoice $invoice;
    public bool $showPaymentModal = false;

    #[Validate('required|numeric|min:0.01')]
    public float $payment_amount = 0;

    #[Validate('required|date')]
    public string $payment_date = '';

    #[Validate('required')]
    public string $payment_method = '';

    #[Validate('nullable|max:255')]
    public ?string $transaction_id = null;

    #[Validate('nullable|max:1000')]
    public ?string $payment_notes = null;

    public function mount(string $invoice): void
    {
        $this->invoice = Invoice::with(['client', 'items', 'payments'])->findOrFail($invoice);
        $this->payment_date = now()->format('Y-m-d');
        $this->payment_amount = (float) $this->invoice->remaining_amount;
    }

    public function sendInvoice(): void
    {
        try {
            $action = new SendInvoiceAction();
            $action($this->invoice);

            $this->invoice->refresh();

            $this->dispatch('notification', [
                'type' => 'success',
                'message' => 'Invoice sent successfully.',
            ]);
        } catch (\Exception $e) {
            $this->dispatch('notification', [
                'type' => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function openPaymentModal(): void
    {
        $this->payment_amount = (float) $this->invoice->remaining_amount;
        $this->payment_date = now()->format('Y-m-d');
        $this->payment_method = '';
        $this->transaction_id = null;
        $this->payment_notes = null;
        $this->showPaymentModal = true;
    }

    public function closePaymentModal(): void
    {
        $this->showPaymentModal = false;
        $this->resetValidation();
    }

    public function recordPayment(): void
    {
        $this->validate();

        try {
            $data = RecordPaymentData::from([
                'amount' => $this->payment_amount,
                'payment_date' => Carbon::parse($this->payment_date),
                'payment_method' => PaymentMethod::from($this->payment_method),
                'transaction_id' => $this->transaction_id,
                'notes' => $this->payment_notes,
            ]);

            $action = new RecordPaymentAction();
            $action($this->invoice, $data);

            $this->invoice->refresh();
            $this->closePaymentModal();

            $this->dispatch('notification', [
                'type' => 'success',
                'message' => 'Payment recorded successfully.',
            ]);
        } catch (\Exception $e) {
            $this->dispatch('notification', [
                'type' => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function cancelInvoice(): void
    {
        try {
            $action = new CancelInvoiceAction();
            $action($this->invoice);

            $this->invoice->refresh();

            $this->dispatch('notification', [
                'type' => 'success',
                'message' => 'Invoice cancelled successfully.',
            ]);
        } catch (\Exception $e) {
            $this->dispatch('notification', [
                'type' => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function render()
    {
        return view('livewire.tenant.invoice.invoice-view', [
            'paymentMethods' => PaymentMethod::cases(),
        ])->layout('layouts.tenant', [
            'title' => 'Invoice #' . $this->invoice->invoice_number,
            'header' => 'Invoice #' . $this->invoice->invoice_number,
        ]);
    }
}
