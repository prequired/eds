<?php

declare(strict_types=1);

namespace App\Actions\Tenant\Invoice;

use App\Data\Tenant\Invoice\UpdateInvoiceData;
use App\Models\Tenant\Invoice;
use Illuminate\Support\Facades\DB;
use Spatie\LaravelData\Optional;

class UpdateInvoiceAction
{
    public function __invoke(Invoice $invoice, UpdateInvoiceData $data): Invoice
    {
        if (!$invoice->canEdit()) {
            throw new \Exception('This invoice cannot be edited in its current status.');
        }

        return DB::transaction(function () use ($invoice, $data) {
            $updates = [];

            if (!$data->client_id instanceof Optional) {
                $updates['client_id'] = $data->client_id;
            }
            if (!$data->issue_date instanceof Optional) {
                $updates['issue_date'] = $data->issue_date;
            }
            if (!$data->due_date instanceof Optional) {
                $updates['due_date'] = $data->due_date;
            }
            if (!$data->tax_rate instanceof Optional) {
                $updates['tax_rate'] = $data->tax_rate;
            }
            if (!$data->notes instanceof Optional) {
                $updates['notes'] = $data->notes;
            }
            if (!$data->terms instanceof Optional) {
                $updates['terms'] = $data->terms;
            }
            if (!$data->footer instanceof Optional) {
                $updates['footer'] = $data->footer;
            }

            $invoice->update($updates);

            // Update items if provided
            if (!$data->items instanceof Optional) {
                // Delete existing items
                $invoice->items()->delete();

                // Create new items
                foreach ($data->items as $index => $itemData) {
                    $invoice->items()->create([
                        'description' => $itemData->description,
                        'quantity' => $itemData->quantity,
                        'unit_price' => $itemData->unit_price,
                        'sort_order' => $itemData->sort_order ?? $index,
                    ]);
                }
            }

            $invoice->refresh();

            return $invoice;
        });
    }
}
