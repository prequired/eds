<?php

declare(strict_types=1);

namespace App\Livewire\Tenant\Expense;

use App\Actions\Tenant\Expense\ApproveExpenseAction;
use App\Actions\Tenant\Expense\DeleteExpenseAction;
use App\Actions\Tenant\Expense\RejectExpenseAction;
use App\Actions\Tenant\Expense\ReimburseExpenseAction;
use App\Actions\Tenant\Expense\SubmitExpenseAction;
use App\Enums\ExpenseCategory;
use App\Enums\ExpenseStatus;
use App\Models\Tenant\Client;
use App\Models\Tenant\Expense;
use App\Models\Tenant\Project;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class ExpenseList extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url(as: 'category')]
    public string $categoryFilter = 'all';

    #[Url(as: 'status')]
    public string $statusFilter = 'all';

    #[Url(as: 'project')]
    public string $projectFilter = 'all';

    #[Url(as: 'client')]
    public string $clientFilter = 'all';

    #[Url(as: 'billable')]
    public string $billableFilter = 'all';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function submitExpense(string $expenseId): void
    {
        $expense = Expense::findOrFail($expenseId);

        // Users can only submit their own expenses
        if ($expense->user_id !== auth()->id()) {
            $this->dispatch('notification', [
                'type' => 'error',
                'message' => 'You can only submit your own expenses.',
            ]);
            return;
        }

        try {
            $action = new SubmitExpenseAction();
            $action($expense);

            $this->dispatch('notification', [
                'type' => 'success',
                'message' => 'Expense submitted for approval.',
            ]);
        } catch (\Exception $e) {
            $this->dispatch('notification', [
                'type' => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function approveExpense(string $expenseId): void
    {
        if (!auth()->user()->isOwner() && !auth()->user()->isAdmin()) {
            $this->dispatch('notification', [
                'type' => 'error',
                'message' => 'You do not have permission to approve expenses.',
            ]);
            return;
        }

        $expense = Expense::findOrFail($expenseId);

        try {
            $action = new ApproveExpenseAction();
            $action($expense, auth()->user());

            $this->dispatch('notification', [
                'type' => 'success',
                'message' => 'Expense approved successfully.',
            ]);
        } catch (\Exception $e) {
            $this->dispatch('notification', [
                'type' => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function rejectExpense(string $expenseId): void
    {
        if (!auth()->user()->isOwner() && !auth()->user()->isAdmin()) {
            $this->dispatch('notification', [
                'type' => 'error',
                'message' => 'You do not have permission to reject expenses.',
            ]);
            return;
        }

        $expense = Expense::findOrFail($expenseId);

        try {
            $action = new RejectExpenseAction();
            $action($expense);

            $this->dispatch('notification', [
                'type' => 'success',
                'message' => 'Expense rejected.',
            ]);
        } catch (\Exception $e) {
            $this->dispatch('notification', [
                'type' => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function reimburseExpense(string $expenseId): void
    {
        if (!auth()->user()->isOwner() && !auth()->user()->isAdmin()) {
            $this->dispatch('notification', [
                'type' => 'error',
                'message' => 'You do not have permission to reimburse expenses.',
            ]);
            return;
        }

        $expense = Expense::findOrFail($expenseId);

        try {
            $action = new ReimburseExpenseAction();
            $action($expense);

            $this->dispatch('notification', [
                'type' => 'success',
                'message' => 'Expense marked as reimbursed.',
            ]);
        } catch (\Exception $e) {
            $this->dispatch('notification', [
                'type' => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function deleteExpense(string $expenseId): void
    {
        if (!auth()->user()->can('expenses.delete')) {
            $this->dispatch('notification', [
                'type' => 'error',
                'message' => 'You do not have permission to delete expenses.',
            ]);
            return;
        }

        $expense = Expense::findOrFail($expenseId);

        // Users can only delete their own expenses
        if ($expense->user_id !== auth()->id() && !auth()->user()->isOwner() && !auth()->user()->isAdmin()) {
            $this->dispatch('notification', [
                'type' => 'error',
                'message' => 'You can only delete your own expenses.',
            ]);
            return;
        }

        try {
            $action = new DeleteExpenseAction();
            $action($expense);

            $this->dispatch('notification', [
                'type' => 'success',
                'message' => 'Expense deleted successfully.',
            ]);
        } catch (\Exception $e) {
            $this->dispatch('notification', [
                'type' => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function render()
    {
        $query = Expense::with(['user', 'project', 'client'])
            ->when($this->search, function ($q) {
                $q->where('description', 'ilike', "%{$this->search}%");
            })
            ->when($this->categoryFilter !== 'all', function ($q) {
                $q->where('category', $this->categoryFilter);
            })
            ->when($this->statusFilter !== 'all', function ($q) {
                $q->where('status', $this->statusFilter);
            })
            ->when($this->projectFilter !== 'all', function ($q) {
                $q->where('project_id', $this->projectFilter);
            })
            ->when($this->clientFilter !== 'all', function ($q) {
                $q->where('client_id', $this->clientFilter);
            })
            ->when($this->billableFilter !== 'all', function ($q) {
                $q->where('billable', $this->billableFilter === 'yes');
            });

        // Non-admin users only see their own expenses
        if (!auth()->user()->isOwner() && !auth()->user()->isAdmin()) {
            $query->forUser(auth()->id());
        }

        $expenses = $query->orderBy('expense_date', 'desc')->paginate(20);

        // Calculate totals
        $totalAmount = $expenses->sum('amount');
        $billableAmount = $expenses->where('billable', true)->sum('amount');

        $categories = ExpenseCategory::cases();
        $statuses = ExpenseStatus::cases();
        $projects = Project::orderBy('name')->get();
        $clients = Client::orderBy('name')->get();

        return view('livewire.tenant.expense.expense-list', [
            'expenses' => $expenses,
            'totalAmount' => $totalAmount,
            'billableAmount' => $billableAmount,
            'categories' => $categories,
            'statuses' => $statuses,
            'projects' => $projects,
            'clients' => $clients,
        ])->layout('layouts.tenant', [
            'title' => 'Expenses',
            'header' => 'Expenses',
        ]);
    }
}
