<?php

declare(strict_types=1);

namespace App\Actions\Tenant\Expense;

use App\Models\Tenant\Expense;
use App\Notifications\ExpenseReimbursedNotification;

class ReimburseExpenseAction
{
    public function __invoke(Expense $expense): Expense
    {
        $expense->markAsReimbursed();

        // Notify the expense owner
        $expense->user->notify(new ExpenseReimbursedNotification($expense));

        return $expense;
    }
}
