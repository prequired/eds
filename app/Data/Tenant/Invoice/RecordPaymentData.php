<?php

declare(strict_types=1);

namespace App\Data\Tenant\Invoice;

use App\Enums\PaymentMethod;
use Carbon\Carbon;
use Spatie\LaravelData\Data;

class RecordPaymentData extends Data
{
    public function __construct(
        public float $amount,
        public Carbon $payment_date,
        public PaymentMethod $payment_method,
        public ?string $transaction_id = null,
        public ?string $notes = null,
    ) {}
}
