<?php

declare(strict_types=1);

namespace App\Actions\Tenant\Expense;

use App\Enums\TeamRole;
use App\Models\Tenant\Expense;
use App\Models\User;
use App\Notifications\ExpenseSubmittedNotification;

class SubmitExpenseAction
{
    public function __invoke(Expense $expense): Expense
    {
        $expense->submit();

        // Notify all admins and owners
        $approvers = User::whereIn('role', [TeamRole::ADMIN, TeamRole::OWNER])->get();
        foreach ($approvers as $approver) {
            $approver->notify(new ExpenseSubmittedNotification($expense));
        }

        return $expense;
    }
}
