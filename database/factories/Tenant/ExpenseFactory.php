<?php

declare(strict_types=1);

namespace Database\Factories\Tenant;

use App\Enums\ExpenseCategory;
use App\Enums\ExpenseStatus;
use App\Models\Tenant\Client;
use App\Models\Tenant\Expense;
use App\Models\Tenant\Project;
use App\Models\Tenant\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Expense>
 */
class ExpenseFactory extends Factory
{
    protected $model = Expense::class;

    public function definition(): array
    {
        $billable = $this->faker->boolean(60); // 60% chance of being billable

        return [
            'user_id' => User::factory(),
            'project_id' => $this->faker->boolean(50) ? Project::factory() : null,
            'client_id' => $this->faker->boolean(50) ? Client::factory() : null,
            'category' => $this->faker->randomElement(ExpenseCategory::cases()),
            'amount' => $this->faker->randomFloat(2, 10, 500),
            'expense_date' => $this->faker->dateTimeBetween('-3 months', 'now'),
            'description' => $this->faker->sentence(),
            'receipt_path' => $this->faker->boolean(70) ? 'receipts/' . $this->faker->uuid() . '.pdf' : null,
            'billable' => $billable,
            'status' => $this->faker->randomElement(ExpenseStatus::cases()),
            'notes' => $this->faker->boolean(30) ? $this->faker->paragraph() : null,
        ];
    }

    public function draft(): self
    {
        return $this->state(fn (array $attributes) => [
            'status' => ExpenseStatus::DRAFT,
            'approved_by' => null,
            'approved_at' => null,
            'reimbursed_at' => null,
        ]);
    }

    public function submitted(): self
    {
        return $this->state(fn (array $attributes) => [
            'status' => ExpenseStatus::SUBMITTED,
            'approved_by' => null,
            'approved_at' => null,
            'reimbursed_at' => null,
        ]);
    }

    public function approved(): self
    {
        return $this->state(fn (array $attributes) => [
            'status' => ExpenseStatus::APPROVED,
            'approved_by' => User::factory(),
            'approved_at' => now(),
            'reimbursed_at' => null,
        ]);
    }

    public function rejected(): self
    {
        return $this->state(fn (array $attributes) => [
            'status' => ExpenseStatus::REJECTED,
            'approved_by' => null,
            'approved_at' => null,
            'reimbursed_at' => null,
        ]);
    }

    public function reimbursed(): self
    {
        return $this->state(fn (array $attributes) => [
            'status' => ExpenseStatus::REIMBURSED,
            'approved_by' => User::factory(),
            'approved_at' => now(),
            'reimbursed_at' => now(),
        ]);
    }

    public function invoiced(): self
    {
        return $this->state(fn (array $attributes) => [
            'status' => ExpenseStatus::INVOICED,
            'billable' => true,
            'approved_by' => User::factory(),
            'approved_at' => now(),
        ]);
    }

    public function billable(): self
    {
        return $this->state(fn (array $attributes) => [
            'billable' => true,
        ]);
    }

    public function nonBillable(): self
    {
        return $this->state(fn (array $attributes) => [
            'billable' => false,
        ]);
    }

    public function withReceipt(): self
    {
        return $this->state(fn (array $attributes) => [
            'receipt_path' => 'receipts/' . $this->faker->uuid() . '.pdf',
        ]);
    }

    public function withoutReceipt(): self
    {
        return $this->state(fn (array $attributes) => [
            'receipt_path' => null,
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

    public function category(ExpenseCategory $category): self
    {
        return $this->state(fn (array $attributes) => [
            'category' => $category,
        ]);
    }

    public function amount(float $amount): self
    {
        return $this->state(fn (array $attributes) => [
            'amount' => $amount,
        ]);
    }
}
