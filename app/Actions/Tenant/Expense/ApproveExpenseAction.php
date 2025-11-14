<?php

declare(strict_types=1);

namespace App\Actions\Tenant\Expense;

use App\Models\Tenant\Expense;
use App\Models\Tenant\User;

class ApproveExpenseAction
{
    public function __invoke(Expense $expense, User $approver): Expense
    {
        $expense->approve($approver);

        return $expense;
    }
}
