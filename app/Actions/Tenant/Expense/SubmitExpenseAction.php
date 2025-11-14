<?php

declare(strict_types=1);

namespace App\Actions\Tenant\Expense;

use App\Models\Tenant\Expense;

class SubmitExpenseAction
{
    public function __invoke(Expense $expense): Expense
    {
        $expense->submit();

        return $expense;
    }
}
