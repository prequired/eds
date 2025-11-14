<?php

declare(strict_types=1);

namespace App\Data\Tenant\Invoice;

use Carbon\Carbon;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;
use Spatie\LaravelData\Optional;

class UpdateInvoiceData extends Data
{
    public function __construct(
        public string|Optional $client_id = new Optional(),
        public Carbon|Optional $issue_date = new Optional(),
        public Carbon|Optional $due_date = new Optional(),
        public float|Optional $tax_rate = new Optional(),
        #[DataCollectionOf(InvoiceItemData::class)]
        public DataCollection|Optional $items = new Optional(),
        public string|null|Optional $notes = new Optional(),
        public string|null|Optional $terms = new Optional(),
        public string|null|Optional $footer = new Optional(),
    ) {}
}
