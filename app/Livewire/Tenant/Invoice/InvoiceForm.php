<?php

declare(strict_types=1);

namespace App\Livewire\Tenant\Invoice;

use App\Actions\Tenant\Invoice\CreateInvoiceAction;
use App\Actions\Tenant\Invoice\SendInvoiceAction;
use App\Actions\Tenant\Invoice\UpdateInvoiceAction;
use App\Data\Tenant\Invoice\CreateInvoiceData;
use App\Data\Tenant\Invoice\InvoiceItemData;
use App\Data\Tenant\Invoice\UpdateInvoiceData;
use App\Enums\InvoiceStatus;
use App\Models\Tenant\Client;
use App\Models\Tenant\Invoice;
use Carbon\Carbon;
use Livewire\Attributes\Validate;
use Livewire\Component;

class InvoiceForm extends Component
{
    public ?string $invoiceId = null;
    public ?Invoice $invoice = null;

    #[Validate('required|exists:App\Models\Tenant\Client,id')]
    public string $client_id = '';

    #[Validate('required|date')]
    public string $issue_date = '';

    #[Validate('required|date|after_or_equal:issue_date')]
    public string $due_date = '';

    #[Validate('required|numeric|min:0|max:100')]
    public float $tax_rate = 0;

    #[Validate('nullable|max:5000')]
    public ?string $notes = null;

    #[Validate('nullable|max:5000')]
    public ?string $terms = null;

    #[Validate('nullable|max:5000')]
    public ?string $footer = null;

    #[Validate('required|array|min:1')]
    public array $items = [];

    public function mount(?string $invoice = null): void
    {
        // Set default dates
        $this->issue_date = now()->format('Y-m-d');
        $this->due_date = now()->addDays(30)->format('Y-m-d');

        if ($invoice) {
            $this->invoiceId = $invoice;
            $this->invoice = Invoice::with('items')->findOrFail($invoice);

            if (!$this->invoice->canEdit()) {
                session()->flash('error', 'This invoice cannot be edited.');
                $this->redirect(route('invoices.index'), navigate: true);
                return;
            }

            $this->client_id = $this->invoice->client_id;
            $this->issue_date = $this->invoice->issue_date->format('Y-m-d');
            $this->due_date = $this->invoice->due_date->format('Y-m-d');
            $this->tax_rate = (float) $this->invoice->tax_rate;
            $this->notes = $this->invoice->notes;
            $this->terms = $this->invoice->terms;
            $this->footer = $this->invoice->footer;

            // Load existing items
            $this->items = $this->invoice->items->map(fn($item) => [
                'description' => $item->description,
                'quantity' => (float) $item->quantity,
                'unit_price' => (float) $item->unit_price,
            ])->toArray();
        } else {
            // Add one empty item for new invoices
            $this->addItem();
        }
    }

    public function addItem(): void
    {
        $this->items[] = [
            'description' => '',
            'quantity' => 1,
            'unit_price' => 0,
        ];
    }

    public function removeItem(int $index): void
    {
        if (count($this->items) > 1) {
            unset($this->items[$index]);
            $this->items = array_values($this->items); // Re-index array
        }
    }

    public function calculateSubtotal(): float
    {
        return array_reduce($this->items, function ($carry, $item) {
            return $carry + ($item['quantity'] * $item['unit_price']);
        }, 0);
    }

    public function calculateTax(): float
    {
        return $this->calculateSubtotal() * ($this->tax_rate / 100);
    }

    public function calculateTotal(): float
    {
        return $this->calculateSubtotal() + $this->calculateTax();
    }

    public function saveDraft()
    {
        return $this->save(InvoiceStatus::DRAFT);
    }

    public function saveAndSend()
    {
        $invoice = $this->save(InvoiceStatus::DRAFT);

        if ($invoice) {
            try {
                $action = new SendInvoiceAction();
                $action($invoice);

                session()->flash('success', 'Invoice sent successfully.');
                $this->redirect(route('invoices.index'), navigate: true);
            } catch (\Exception $e) {
                session()->flash('error', $e->getMessage());
            }
        }
    }

    protected function save(InvoiceStatus $status = InvoiceStatus::DRAFT): ?Invoice
    {
        $this->validate();

        // Validate items
        foreach ($this->items as $index => $item) {
            if (empty($item['description'])) {
                $this->addError("items.{$index}.description", 'Description is required.');
                return null;
            }
            if ($item['quantity'] <= 0) {
                $this->addError("items.{$index}.quantity", 'Quantity must be greater than 0.');
                return null;
            }
            if ($item['unit_price'] < 0) {
                $this->addError("items.{$index}.unit_price", 'Unit price cannot be negative.');
                return null;
            }
        }

        try {
            $itemsData = collect($this->items)->map(fn($item, $index) =>
                InvoiceItemData::from([
                    'description' => $item['description'],
                    'quantity' => (float) $item['quantity'],
                    'unit_price' => (float) $item['unit_price'],
                    'sort_order' => $index,
                ])
            );

            if ($this->invoiceId) {
                // Update existing invoice
                $data = UpdateInvoiceData::from([
                    'client_id' => $this->client_id,
                    'issue_date' => Carbon::parse($this->issue_date),
                    'due_date' => Carbon::parse($this->due_date),
                    'tax_rate' => $this->tax_rate,
                    'items' => $itemsData,
                    'notes' => $this->notes,
                    'terms' => $this->terms,
                    'footer' => $this->footer,
                ]);

                $action = new UpdateInvoiceAction();
                $invoice = $action($this->invoice, $data);

                session()->flash('success', 'Invoice updated successfully.');
            } else {
                // Create new invoice
                $data = CreateInvoiceData::from([
                    'client_id' => $this->client_id,
                    'issue_date' => Carbon::parse($this->issue_date),
                    'due_date' => Carbon::parse($this->due_date),
                    'tax_rate' => $this->tax_rate,
                    'items' => $itemsData,
                    'notes' => $this->notes,
                    'terms' => $this->terms,
                    'footer' => $this->footer,
                    'status' => $status,
                ]);

                $action = new CreateInvoiceAction();
                $invoice = $action($data);

                session()->flash('success', 'Invoice created successfully.');
            }

            if ($status === InvoiceStatus::DRAFT) {
                $this->redirect(route('invoices.index'), navigate: true);
            }

            return $invoice;
        } catch (\Exception $e) {
            session()->flash('error', $e->getMessage());
            return null;
        }
    }

    public function render()
    {
        $clients = Client::orderBy('name')->get();

        return view('livewire.tenant.invoice.invoice-form', [
            'clients' => $clients,
            'subtotal' => $this->calculateSubtotal(),
            'tax' => $this->calculateTax(),
            'total' => $this->calculateTotal(),
        ])->layout('layouts.tenant', [
            'title' => $this->invoiceId ? 'Edit Invoice' : 'Create Invoice',
            'header' => $this->invoiceId ? 'Edit Invoice' : 'Create Invoice',
        ]);
    }
}
