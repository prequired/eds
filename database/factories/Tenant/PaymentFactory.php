<?php

declare(strict_types=1);

namespace Database\Factories\Tenant;

use App\Enums\PaymentMethod;
use App\Models\Tenant\Invoice;
use App\Models\Tenant\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;

class PaymentFactory extends Factory
{
    protected $model = Payment::class;

    public function definition(): array
    {
        return [
            'invoice_id' => Invoice::factory(),
            'amount' => fake()->randomFloat(2, 50, 1000),
            'payment_date' => fake()->dateTimeBetween('-30 days', 'now'),
            'payment_method' => fake()->randomElement(PaymentMethod::cases()),
            'transaction_id' => fake()->optional()->uuid(),
            'notes' => fake()->optional()->sentence(),
        ];
    }
}
