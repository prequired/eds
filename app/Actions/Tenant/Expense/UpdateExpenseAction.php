<?php

declare(strict_types=1);

namespace App\Actions\Tenant\Expense;

use App\Data\Tenant\Expense\UpdateExpenseData;
use App\Models\Tenant\Expense;
use Illuminate\Support\Facades\DB;
use Spatie\LaravelData\Optional;

class UpdateExpenseAction
{
    public function __invoke(Expense $expense, UpdateExpenseData $data): Expense
    {
        if (!$expense->canBeEdited()) {
            throw new \Exception('This expense cannot be edited in its current status.');
        }

        return DB::transaction(function () use ($expense, $data) {
            $updates = [];

            if (!($data->category instanceof Optional)) {
                $updates['category'] = $data->category;
            }

            if (!($data->amount instanceof Optional)) {
                $updates['amount'] = $data->amount;
            }

            if (!($data->expense_date instanceof Optional)) {
                $updates['expense_date'] = $data->expense_date;
            }

            if (!($data->description instanceof Optional)) {
                $updates['description'] = $data->description;
            }

            if (!($data->project_id instanceof Optional)) {
                $updates['project_id'] = $data->project_id;
            }

            if (!($data->client_id instanceof Optional)) {
                $updates['client_id'] = $data->client_id;
            }

            if (!($data->receipt_path instanceof Optional)) {
                $updates['receipt_path'] = $data->receipt_path;
            }

            if (!($data->billable instanceof Optional)) {
                $updates['billable'] = $data->billable;
            }

            if (!($data->status instanceof Optional)) {
                $updates['status'] = $data->status;
            }

            if (!($data->notes instanceof Optional)) {
                $updates['notes'] = $data->notes;
            }

            $expense->update($updates);

            return $expense->fresh();
        });
    }
}
