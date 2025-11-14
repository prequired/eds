<?php

declare(strict_types=1);

namespace App\Data\Tenant\Expense;

use App\Enums\ExpenseCategory;
use App\Enums\ExpenseStatus;
use Carbon\Carbon;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class CreateExpenseData extends Data
{
    public function __construct(
        public ExpenseCategory $category,
        public float $amount,
        public Carbon $expense_date,
        public string $description,
        public string|Optional $project_id = new Optional(),
        public string|Optional $client_id = new Optional(),
        public string|null|Optional $receipt_path = new Optional(),
        public bool $billable = false,
        public ExpenseStatus $status = ExpenseStatus::DRAFT,
        public string|null|Optional $notes = new Optional(),
    ) {}
}
