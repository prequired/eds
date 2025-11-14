<?php

declare(strict_types=1);

namespace Database\Factories\Tenant;

use App\Enums\InvoiceStatus;
use App\Models\Tenant\Client;
use App\Models\Tenant\Invoice;
use Illuminate\Database\Eloquent\Factories\Factory;

class InvoiceFactory extends Factory
{
    protected $model = Invoice::class;

    public function definition(): array
    {
        $issueDate = fake()->dateTimeBetween('-30 days', 'now');
        $dueDate = fake()->dateTimeBetween($issueDate, '+30 days');

        return [
            'client_id' => Client::factory(),
            'status' => InvoiceStatus::DRAFT,
            'issue_date' => $issueDate,
            'due_date' => $dueDate,
            'paid_date' => null,
            'subtotal' => 0,
            'tax_rate' => 0,
            'tax_amount' => 0,
            'total' => 0,
            'amount_paid' => 0,
            'notes' => fake()->optional()->sentence(),
            'terms' => fake()->optional()->sentence(),
            'footer' => fake()->optional()->sentence(),
        ];
    }

    public function draft(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => InvoiceStatus::DRAFT,
        ]);
    }

    public function sent(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => InvoiceStatus::SENT,
        ]);
    }

    public function paid(): static
    {
        return $this->state(function (array $attributes) {
            $total = $attributes['total'] ?? 1000;

            return [
                'status' => InvoiceStatus::PAID,
                'amount_paid' => $total,
                'paid_date' => now(),
            ];
        });
    }

    public function partiallyPaid(): static
    {
        return $this->state(function (array $attributes) {
            $total = $attributes['total'] ?? 1000;

            return [
                'status' => InvoiceStatus::PARTIALLY_PAID,
                'amount_paid' => $total / 2,
            ];
        });
    }

    public function overdue(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => InvoiceStatus::SENT,
            'due_date' => now()->subDays(7),
            'amount_paid' => 0,
        ]);
    }

    public function cancelled(): static
    {
        return $this->state(fn(array $attributes) => [
            'status' => InvoiceStatus::CANCELLED,
        ]);
    }
}
