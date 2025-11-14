<?php

namespace Database\Factories\Central;

use App\Enums\PlanType;
use App\Enums\TenantStatus;
use App\Models\Central\Tenant;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class TenantFactory extends Factory
{
    protected $model = Tenant::class;

    protected $connection = 'pgsql';

    public function definition(): array
    {
        $subdomain = fake()->unique()->slug(2);

        return [
            'id' => Str::uuid(),
            'company_name' => fake()->company(),
            'subdomain' => $subdomain,
            'owner_email' => fake()->unique()->safeEmail(),
            'plan' => fake()->randomElement([
                PlanType::STARTER,
                PlanType::PROFESSIONAL,
                PlanType::AGENCY,
            ]),
            'status' => TenantStatus::ACTIVE,
            'trial_ends_at' => null,
            'client_limit' => null,
            'website_limit' => null,
            'storage_limit_gb' => 100,
            'bandwidth_limit_gb' => 2000,
            'settings' => [
                'branding' => [
                    'logo_url' => null,
                    'primary_color' => '#3B82F6',
                    'secondary_color' => '#10B981',
                ],
                'features' => [],
            ],
            'data' => [],
        ];
    }

    public function trial(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => TenantStatus::TRIAL,
            'trial_ends_at' => now()->addDays(14),
        ]);
    }

    public function suspended(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => TenantStatus::SUSPENDED,
        ]);
    }

    public function withLimits(int $clients = 5, int $websites = 10): static
    {
        return $this->state(fn (array $attributes) => [
            'client_limit' => $clients,
            'website_limit' => $websites,
        ]);
    }
}
