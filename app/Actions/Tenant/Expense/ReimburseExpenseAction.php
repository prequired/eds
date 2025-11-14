<?php

declare(strict_types=1);

namespace App\Actions\Tenant\Expense;

use App\Models\Tenant\Expense;

class ReimburseExpenseAction
{
    public function __invoke(Expense $expense): Expense
    {
        $expense->markAsReimbursed();

        return $expense;
    }
}
