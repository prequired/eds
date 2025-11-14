<?php

declare(strict_types=1);

namespace App\Actions\Tenant\Expense;

use App\Models\Tenant\Expense;
use App\Notifications\ExpenseRejectedNotification;

class RejectExpenseAction
{
    public function __invoke(Expense $expense, ?string $reason = null): Expense
    {
        $expense->reject();

        // Notify the expense owner
        $expense->user->notify(new ExpenseRejectedNotification($expense, $reason));

        return $expense;
    }
}
