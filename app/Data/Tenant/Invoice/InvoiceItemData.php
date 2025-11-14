<?php

declare(strict_types=1);

namespace App\Data\Tenant\Invoice;

use Spatie\LaravelData\Data;

class InvoiceItemData extends Data
{
    public function __construct(
        public string $description,
        public float $quantity,
        public float $unit_price,
        public ?int $sort_order = null,
    ) {}
}
