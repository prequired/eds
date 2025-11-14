<?php

declare(strict_types=1);

namespace App\Events\Tenant;

use App\Models\Tenant\Invoice;
use App\Models\Tenant\Payment;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InvoicePaymentRecorded
{
    use Dispatchable, SerializesModels;

    public function __construct(
        public Invoice $invoice,
        public Payment $payment,
    ) {}
}
