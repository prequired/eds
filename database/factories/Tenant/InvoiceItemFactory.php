<?php

declare(strict_types=1);

namespace Database\Factories\Tenant;

use App\Models\Tenant\Invoice;
use App\Models\Tenant\InvoiceItem;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvoiceItemFactory extends Factory
{
    protected $model = InvoiceItem::class;

    public function definition(): array
    {
        $quantity = fake()->numberBetween(1, 10);
        $unitPrice = fake()->randomFloat(2, 10, 500);

        return [
            'invoice_id' => Invoice::factory(),
            'description' => fake()->sentence(),
            'quantity' => $quantity,
            'unit_price' => $unitPrice,
            'total' => $quantity * $unitPrice,
            'sort_order' => 0,
        ];
    }
}
