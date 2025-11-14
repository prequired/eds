<?php

declare(strict_types=1);

namespace App\Actions\Tenant\Invoice;

use App\Enums\InvoiceStatus;
use App\Events\Tenant\InvoiceSent;
use App\Models\Tenant\Invoice;

class SendInvoiceAction
{
    public function __invoke(Invoice $invoice): Invoice
    {
        if (!$invoice->canSend()) {
            throw new \Exception('This invoice cannot be sent in its current status.');
        }

        if ($invoice->items()->count() === 0) {
            throw new \Exception('Cannot send an invoice with no items.');
        }

        $invoice->update([
            'status' => InvoiceStatus::SENT,
        ]);

        InvoiceSent::dispatch($invoice);

        return $invoice;
    }
}
