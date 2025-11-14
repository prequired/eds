<?php

declare(strict_types=1);

namespace App\Data\Tenant\Expense;

use App\Enums\ExpenseCategory;
use App\Enums\ExpenseStatus;
use Carbon\Carbon;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Optional;

class UpdateExpenseData extends Data
{
    public function __construct(
        public ExpenseCategory|Optional $category = new Optional(),
        public float|Optional $amount = new Optional(),
        public Carbon|Optional $expense_date = new Optional(),
        public string|Optional $description = new Optional(),
        public string|null|Optional $project_id = new Optional(),
        public string|null|Optional $client_id = new Optional(),
        public string|null|Optional $receipt_path = new Optional(),
        public bool|Optional $billable = new Optional(),
        public ExpenseStatus|Optional $status = new Optional(),
        public string|null|Optional $notes = new Optional(),
    ) {}
}
