<?php

declare(strict_types=1);

namespace App\Actions\Tenant\Invoice;

use App\Enums\InvoiceStatus;
use App\Models\Tenant\Invoice;

class CancelInvoiceAction
{
    public function __invoke(Invoice $invoice): Invoice
    {
        if ($invoice->status === InvoiceStatus::PAID) {
            throw new \Exception('Cannot cancel a paid invoice.');
        }

        if ($invoice->isPartiallyPaid()) {
            throw new \Exception('Cannot cancel an invoice with payments. Refund payments first.');
        }

        $invoice->update([
            'status' => InvoiceStatus::CANCELLED,
        ]);

        return $invoice;
    }
}
