<?php

declare(strict_types=1);

namespace App\Actions\Tenant\Expense;

use App\Models\Tenant\Expense;

class RejectExpenseAction
{
    public function __invoke(Expense $expense): Expense
    {
        $expense->reject();

        return $expense;
    }
}
