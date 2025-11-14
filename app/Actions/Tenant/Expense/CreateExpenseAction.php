<?php

declare(strict_types=1);

namespace App\Actions\Tenant\Expense;

use App\Data\Tenant\Expense\CreateExpenseData;
use App\Models\Tenant\Expense;
use App\Models\Tenant\User;
use Illuminate\Support\Facades\DB;

class CreateExpenseAction
{
    public function __invoke(CreateExpenseData $data, User $user): Expense
    {
        return DB::transaction(function () use ($data, $user) {
            return Expense::create([
                'user_id' => $user->id,
                'project_id' => $data->project_id instanceof \Spatie\LaravelData\Optional ? null : $data->project_id,
                'client_id' => $data->client_id instanceof \Spatie\LaravelData\Optional ? null : $data->client_id,
                'category' => $data->category,
                'amount' => $data->amount,
                'expense_date' => $data->expense_date,
                'description' => $data->description,
                'receipt_path' => $data->receipt_path instanceof \Spatie\LaravelData\Optional ? null : $data->receipt_path,
                'billable' => $data->billable,
                'status' => $data->status,
                'notes' => $data->notes instanceof \Spatie\LaravelData\Optional ? null : $data->notes,
            ]);
        });
    }
}
