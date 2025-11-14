<?php

declare(strict_types=1);

namespace App\Actions\Tenant\Invoice;

use App\Data\Tenant\Invoice\CreateInvoiceData;
use App\Models\Tenant\Invoice;
use Illuminate\Support\Facades\DB;

class CreateInvoiceAction
{
    public function __invoke(CreateInvoiceData $data): Invoice
    {
        return DB::transaction(function () use ($data) {
            $invoice = Invoice::create([
                'client_id' => $data->client_id,
                'status' => $data->status,
                'issue_date' => $data->issue_date,
                'due_date' => $data->due_date,
                'tax_rate' => $data->tax_rate,
                'notes' => $data->notes,
                'terms' => $data->terms,
                'footer' => $data->footer,
            ]);

            // Create invoice items
            foreach ($data->items as $index => $itemData) {
                $invoice->items()->create([
                    'description' => $itemData->description,
                    'quantity' => $itemData->quantity,
                    'unit_price' => $itemData->unit_price,
                    'sort_order' => $itemData->sort_order ?? $index,
                ]);
            }

            // Totals are calculated automatically via InvoiceItem model events
            $invoice->refresh();

            return $invoice;
        });
    }
}
