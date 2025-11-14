<?php

namespace Database\Factories\Tenant;

use App\Enums\ClientStatus;
use App\Models\Tenant\Client;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ClientFactory extends Factory
{
    protected $model = Client::class;

    protected $connection = 'tenant';

    public function definition(): array
    {
        return [
            'id' => Str::uuid(),
            'name' => fake()->name(),
            'company' => fake()->company(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => fake()->phoneNumber(),
            'website' => fake()->url(),
            'address_line1' => fake()->streetAddress(),
            'address_line2' => fake()->optional()->secondaryAddress(),
            'city' => fake()->city(),
            'state' => fake()->state(),
            'postal_code' => fake()->postcode(),
            'country' => 'US',
            'billing_email' => fake()->optional()->safeEmail(),
            'monthly_retainer_cents' => fake()->numberBetween(0, 1000000),
            'currency' => 'usd',
            'payment_terms' => fake()->randomElement([15, 30, 60]),
            'status' => ClientStatus::ACTIVE,
            'settings' => [
                'portal_access' => true,
                'send_automated_reports' => true,
                'report_frequency' => 'weekly',
            ],
            'notes' => fake()->optional()->paragraph(),
            'tags' => fake()->optional()->randomElements(['vip', 'ecommerce', 'saas', 'local'], fake()->numberBetween(0, 3)),
            'archived_at' => null,
        ];
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ClientStatus::INACTIVE,
        ]);
    }

    public function archived(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => ClientStatus::ARCHIVED,
            'archived_at' => now(),
        ]);
    }

    public function withRetainer(int $cents = 500000): static
    {
        return $this->state(fn (array $attributes) => [
            'monthly_retainer_cents' => $cents,
        ]);
    }
}
