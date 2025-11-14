<?php

declare(strict_types=1);

namespace App\Data\Tenant\Invoice;

use App\Enums\InvoiceStatus;
use Carbon\Carbon;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\DataCollection;

class CreateInvoiceData extends Data
{
    public function __construct(
        public string $client_id,
        public Carbon $issue_date,
        public Carbon $due_date,
        public float $tax_rate,
        #[DataCollectionOf(InvoiceItemData::class)]
        public DataCollection $items,
        public ?string $notes = null,
        public ?string $terms = null,
        public ?string $footer = null,
        public InvoiceStatus $status = InvoiceStatus::DRAFT,
    ) {}
}
