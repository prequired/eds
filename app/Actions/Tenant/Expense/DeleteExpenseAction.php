<?php

declare(strict_types=1);

namespace App\Actions\Tenant\Expense;

use App\Models\Tenant\Expense;
use Illuminate\Support\Facades\Storage;

class DeleteExpenseAction
{
    public function __invoke(Expense $expense): void
    {
        if (!$expense->canBeDeleted()) {
            throw new \Exception('This expense cannot be deleted in its current status.');
        }

        // Delete receipt file if exists
        if ($expense->hasReceipt()) {
            Storage::disk('public')->delete($expense->receipt_path);
        }

        $expense->delete();
    }
}
