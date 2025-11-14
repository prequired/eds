<?php

declare(strict_types=1);

namespace Database\Factories\Tenant;

use App\Enums\DeploymentStatus;
use App\Enums\UptimeStatus;
use App\Enums\WebsiteEnvironment;
use App\Enums\WebsiteStatus;
use App\Models\Tenant\Client;
use App\Models\Tenant\Project;
use App\Models\Tenant\Website;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Tenant\Website>
 */
class WebsiteFactory extends Factory
{
    protected $model = Website::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'client_id' => Client::factory(),
            'project_id' => null,
            'name' => fake()->company() . ' Website',
            'url' => fake()->url(),
            'environment' => fake()->randomElement(WebsiteEnvironment::cases()),
            'status' => fake()->randomElement(WebsiteStatus::cases()),
            'server_provider' => fake()->randomElement(['digitalocean', 'aws', 'vultr', 'linode']),
            'server_id' => 'server-' . fake()->uuid(),
            'server_ip' => fake()->ipv4(),
            'repository_provider' => fake()->randomElement(['github', 'gitlab', 'bitbucket']),
            'repository_url' => 'https://github.com/' . fake()->userName() . '/' . fake()->slug(),
            'repository_branch' => 'main',
            'deployment_method' => fake()->randomElement(['forge', 'github_actions', 'manual']),
            'deployment_status' => DeploymentStatus::IDLE,
            'uptime_status' => UptimeStatus::UNKNOWN,
            'notes' => fake()->optional()->sentence(),
        ];
    }

    /**
     * Indicate that the website is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => WebsiteStatus::ACTIVE,
        ]);
    }

    /**
     * Indicate that the website is in production.
     */
    public function production(): static
    {
        return $this->state(fn (array $attributes) => [
            'environment' => WebsiteEnvironment::PRODUCTION,
        ]);
    }

    /**
     * Indicate that the website is online.
     */
    public function online(): static
    {
        return $this->state(fn (array $attributes) => [
            'uptime_status' => UptimeStatus::UP,
            'response_time_ms' => fake()->numberBetween(50, 500),
            'last_checked_at' => now(),
        ]);
    }

    /**
     * Indicate that the website is offline.
     */
    public function offline(): static
    {
        return $this->state(fn (array $attributes) => [
            'uptime_status' => UptimeStatus::DOWN,
            'last_checked_at' => now(),
        ]);
    }

    /**
     * Indicate that the website has a project.
     */
    public function withProject(): static
    {
        return $this->state(fn (array $attributes) => [
            'project_id' => Project::factory()->create(['client_id' => $attributes['client_id']]),
        ]);
    }
}
