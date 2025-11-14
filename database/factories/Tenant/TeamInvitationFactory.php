<?php

declare(strict_types=1);

namespace Database\Factories\Tenant;

use App\Enums\UserRole;
use App\Models\Tenant\TeamInvitation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<TeamInvitation>
 */
class TeamInvitationFactory extends Factory
{
    protected $model = TeamInvitation::class;

    public function definition(): array
    {
        return [
            'email' => fake()->unique()->safeEmail(),
            'role' => UserRole::MEMBER,
            'permissions' => null,
            'invited_by' => User::factory(),
            'token' => Str::random(64),
            'accepted_at' => null,
            'expires_at' => now()->addDays(7),
        ];
    }

    /**
     * Mark the invitation as accepted.
     */
    public function accepted(): static
    {
        return $this->state(fn (array $attributes) => [
            'accepted_at' => now(),
        ]);
    }

    /**
     * Mark the invitation as expired.
     */
    public function expired(): static
    {
        return $this->state(fn (array $attributes) => [
            'expires_at' => now()->subDay(),
        ]);
    }

    /**
     * Set the invitation as pending (default state, for clarity).
     */
    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'accepted_at' => null,
            'expires_at' => now()->addDays(7),
        ]);
    }

    /**
     * Create an admin invitation.
     */
    public function admin(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => UserRole::ADMIN,
        ]);
    }

    /**
     * Create an owner invitation.
     */
    public function owner(): static
    {
        return $this->state(fn (array $attributes) => [
            'role' => UserRole::OWNER,
        ]);
    }
}
