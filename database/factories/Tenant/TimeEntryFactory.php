<?php

declare(strict_types=1);

namespace Database\Factories\Tenant;

use App\Enums\TimeEntryStatus;
use App\Models\Tenant\Client;
use App\Models\Tenant\Project;
use App\Models\Tenant\TimeEntry;
use App\Models\Tenant\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TimeEntry>
 */
class TimeEntryFactory extends Factory
{
    protected $model = TimeEntry::class;

    public function definition(): array
    {
        $startTime = $this->faker->dateTimeBetween('-1 month', 'now');
        $endTime = (clone $startTime)->modify('+' . $this->faker->numberBetween(30, 480) . ' minutes');
        $duration = $endTime->getTimestamp() - $startTime->getTimestamp();

        $billable = $this->faker->boolean(70); // 70% chance of being billable
        $hourlyRate = $billable ? $this->faker->randomFloat(2, 25, 200) : null;

        return [
            'user_id' => User::factory(),
            'project_id' => $this->faker->boolean(60) ? Project::factory() : null,
            'client_id' => $this->faker->boolean(60) ? Client::factory() : null,
            'description' => $this->faker->sentence(),
            'start_time' => $startTime,
            'end_time' => $endTime,
            'duration' => $duration,
            'billable' => $billable,
            'hourly_rate' => $hourlyRate,
            'status' => $this->faker->randomElement(TimeEntryStatus::cases()),
            'notes' => $this->faker->boolean(30) ? $this->faker->paragraph() : null,
        ];
    }

    public function running(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'end_time' => null,
                'duration' => 0,
            ];
        });
    }

    public function completed(): self
    {
        return $this->state(function (array $attributes) {
            $startTime = $attributes['start_time'];
            $endTime = (clone $startTime)->modify('+' . $this->faker->numberBetween(30, 480) . ' minutes');

            return [
                'end_time' => $endTime,
                'duration' => $endTime->getTimestamp() - $startTime->getTimestamp(),
            ];
        });
    }

    public function billable(float $hourlyRate = null): self
    {
        return $this->state(function (array $attributes) use ($hourlyRate) {
            return [
                'billable' => true,
                'hourly_rate' => $hourlyRate ?? $this->faker->randomFloat(2, 50, 150),
            ];
        });
    }

    public function nonBillable(): self
    {
        return $this->state(function (array $attributes) {
            return [
                'billable' => false,
                'hourly_rate' => null,
            ];
        });
    }

    public function draft(): self
    {
        return $this->state(fn (array $attributes) => [
            'status' => TimeEntryStatus::DRAFT,
        ]);
    }

    public function submitted(): self
    {
        return $this->state(fn (array $attributes) => [
            'status' => TimeEntryStatus::SUBMITTED,
        ]);
    }

    public function approved(): self
    {
        return $this->state(fn (array $attributes) => [
            'status' => TimeEntryStatus::APPROVED,
        ]);
    }

    public function invoiced(): self
    {
        return $this->state(fn (array $attributes) => [
            'status' => TimeEntryStatus::INVOICED,
        ]);
    }

    public function forUser(User $user): self
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user->id,
        ]);
    }

    public function forProject(Project $project): self
    {
        return $this->state(fn (array $attributes) => [
            'project_id' => $project->id,
        ]);
    }

    public function forClient(Client $client): self
    {
        return $this->state(fn (array $attributes) => [
            'client_id' => $client->id,
        ]);
    }

    public function withDuration(int $minutes): self
    {
        return $this->state(function (array $attributes) use ($minutes) {
            $startTime = $attributes['start_time'];
            $endTime = (clone $startTime)->modify("+{$minutes} minutes");

            return [
                'end_time' => $endTime,
                'duration' => $minutes * 60,
            ];
        });
    }
}
