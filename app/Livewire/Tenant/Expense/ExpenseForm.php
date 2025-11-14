<?php

declare(strict_types=1);

namespace App\Livewire\Tenant\Expense;

use App\Actions\Tenant\Expense\CreateExpenseAction;
use App\Actions\Tenant\Expense\UpdateExpenseAction;
use App\Data\Tenant\Expense\CreateExpenseData;
use App\Data\Tenant\Expense\UpdateExpenseData;
use App\Enums\ExpenseCategory;
use App\Enums\ExpenseStatus;
use App\Models\Tenant\Client;
use App\Models\Tenant\Expense;
use App\Models\Tenant\Project;
use Carbon\Carbon;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class ExpenseForm extends Component
{
    use WithFileUploads;

    public ?Expense $expense = null;
    public bool $isEdit = false;

    #[Validate('required')]
    public string $category = '';

    #[Validate('required|numeric|min:0.01')]
    public string $amount = '';

    #[Validate('required|date')]
    public string $expense_date = '';

    #[Validate('required|min:3|max:500')]
    public string $description = '';

    public ?string $project_id = null;
    public ?string $client_id = null;

    #[Validate('nullable|file|mimes:jpg,jpeg,png,pdf|max:5120')] // 5MB max
    public $receipt;

    public ?string $existing_receipt_path = null;
    public bool $remove_receipt = false;

    public bool $billable = false;

    #[Validate('nullable|max:2000')]
    public ?string $notes = null;

    public function mount(?Expense $expense = null): void
    {
        if ($expense && $expense->exists) {
            $this->isEdit = true;
            $this->expense = $expense;

            // Check permissions
            if (!auth()->user()->can('expenses.update') ||
                ($expense->user_id !== auth()->id() && !auth()->user()->isOwner() && !auth()->user()->isAdmin())) {
                abort(403, 'You do not have permission to edit this expense.');
            }

            if (!$expense->canBeEdited()) {
                session()->flash('error', 'This expense cannot be edited in its current status.');
                $this->redirect(route('expenses.index'), navigate: true);
                return;
            }

            $this->category = $expense->category->value;
            $this->amount = (string) $expense->amount;
            $this->expense_date = $expense->expense_date->format('Y-m-d');
            $this->description = $expense->description;
            $this->project_id = $expense->project_id;
            $this->client_id = $expense->client_id;
            $this->existing_receipt_path = $expense->receipt_path;
            $this->billable = $expense->billable;
            $this->notes = $expense->notes;
        } else {
            // Set defaults for new expense
            $this->expense_date = now()->format('Y-m-d');
        }
    }

    public function removeReceipt(): void
    {
        $this->remove_receipt = true;
        $this->existing_receipt_path = null;
    }

    public function save(): void
    {
        $this->validate();

        try {
            $receiptPath = $this->existing_receipt_path;

            // Handle receipt upload
            if ($this->receipt) {
                // Delete old receipt if exists
                if ($this->existing_receipt_path) {
                    \Storage::disk('public')->delete($this->existing_receipt_path);
                }

                $receiptPath = $this->receipt->store('receipts', 'public');
            } elseif ($this->remove_receipt) {
                // Delete receipt if removal requested
                if ($this->existing_receipt_path) {
                    \Storage::disk('public')->delete($this->existing_receipt_path);
                }
                $receiptPath = null;
            }

            if ($this->isEdit) {
                $data = UpdateExpenseData::from([
                    'category' => ExpenseCategory::from($this->category),
                    'amount' => (float) $this->amount,
                    'expense_date' => Carbon::parse($this->expense_date),
                    'description' => $this->description,
                    'project_id' => $this->project_id,
                    'client_id' => $this->client_id,
                    'receipt_path' => $receiptPath,
                    'billable' => $this->billable,
                    'notes' => $this->notes,
                ]);

                $action = new UpdateExpenseAction();
                $action($this->expense, $data);

                session()->flash('success', 'Expense updated successfully.');
            } else {
                $data = CreateExpenseData::from([
                    'category' => ExpenseCategory::from($this->category),
                    'amount' => (float) $this->amount,
                    'expense_date' => Carbon::parse($this->expense_date),
                    'description' => $this->description,
                    'project_id' => $this->project_id,
                    'client_id' => $this->client_id,
                    'receipt_path' => $receiptPath,
                    'billable' => $this->billable,
                    'status' => ExpenseStatus::DRAFT,
                    'notes' => $this->notes,
                ]);

                $action = new CreateExpenseAction();
                $action($data, auth()->user());

                session()->flash('success', 'Expense created successfully.');
            }

            $this->redirect(route('expenses.index'), navigate: true);
        } catch (\Exception $e) {
            $this->dispatch('notification', [
                'type' => 'error',
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function render()
    {
        $categories = ExpenseCategory::cases();
        $projects = Project::orderBy('name')->get();
        $clients = Client::orderBy('name')->get();

        return view('livewire.tenant.expense.expense-form', [
            'categories' => $categories,
            'projects' => $projects,
            'clients' => $clients,
        ])->layout('layouts.tenant', [
            'title' => $this->isEdit ? 'Edit Expense' : 'New Expense',
            'header' => $this->isEdit ? 'Edit Expense' : 'New Expense',
        ]);
    }
}
