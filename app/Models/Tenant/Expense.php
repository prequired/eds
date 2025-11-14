<?php

declare(strict_types=1);

namespace App\Models\Tenant;

use App\Enums\ExpenseCategory;
use App\Enums\ExpenseStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
    use HasFactory;
    use HasUuids;
    use SoftDeletes;

    protected $connection = 'tenant';

    protected $fillable = [
        'user_id',
        'project_id',
        'client_id',
        'invoice_id',
        'category',
        'amount',
        'expense_date',
        'description',
        'receipt_path',
        'billable',
        'status',
        'notes',
        'approved_by',
        'approved_at',
        'reimbursed_at',
    ];

    protected $casts = [
        'category' => ExpenseCategory::class,
        'status' => ExpenseStatus::class,
        'amount' => 'decimal:2',
        'expense_date' => 'date',
        'billable' => 'boolean',
        'approved_at' => 'datetime',
        'reimbursed_at' => 'datetime',
    ];

    // Relationships

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Helper Methods

    public function hasReceipt(): bool
    {
        return !empty($this->receipt_path);
    }

    public function getReceiptUrl(): ?string
    {
        if (!$this->hasReceipt()) {
            return null;
        }

        return asset('storage/' . $this->receipt_path);
    }

    public function isPending(): bool
    {
        return $this->status === ExpenseStatus::SUBMITTED;
    }

    public function isApproved(): bool
    {
        return in_array($this->status, [
            ExpenseStatus::APPROVED,
            ExpenseStatus::REIMBURSED,
            ExpenseStatus::INVOICED,
        ]);
    }

    public function isReimbursed(): bool
    {
        return $this->status === ExpenseStatus::REIMBURSED;
    }

    public function isInvoiced(): bool
    {
        return $this->status === ExpenseStatus::INVOICED;
    }

    public function canBeEdited(): bool
    {
        return $this->status->canEdit();
    }

    public function canBeDeleted(): bool
    {
        return $this->status->canDelete();
    }

    public function submit(): void
    {
        if (!$this->status->canSubmit()) {
            throw new \Exception('This expense cannot be submitted in its current status.');
        }

        $this->status = ExpenseStatus::SUBMITTED;
        $this->save();
    }

    public function approve(User $approver): void
    {
        if (!$this->status->canApprove()) {
            throw new \Exception('This expense cannot be approved in its current status.');
        }

        $this->status = ExpenseStatus::APPROVED;
        $this->approved_by = $approver->id;
        $this->approved_at = now();
        $this->save();
    }

    public function reject(): void
    {
        if (!$this->status->canReject()) {
            throw new \Exception('This expense cannot be rejected in its current status.');
        }

        $this->status = ExpenseStatus::REJECTED;
        $this->save();
    }

    public function markAsReimbursed(): void
    {
        if (!$this->status->canReimburse()) {
            throw new \Exception('This expense cannot be reimbursed in its current status.');
        }

        $this->status = ExpenseStatus::REIMBURSED;
        $this->reimbursed_at = now();
        $this->save();
    }

    // Query Scopes

    public function scopeForUser(Builder $query, string $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    public function scopeForProject(Builder $query, string $projectId): Builder
    {
        return $query->where('project_id', $projectId);
    }

    public function scopeForClient(Builder $query, string $clientId): Builder
    {
        return $query->where('client_id', $clientId);
    }

    public function scopeByCategory(Builder $query, ExpenseCategory $category): Builder
    {
        return $query->where('category', $category);
    }

    public function scopeByStatus(Builder $query, ExpenseStatus $status): Builder
    {
        return $query->where('status', $status);
    }

    public function scopeDraft(Builder $query): Builder
    {
        return $query->where('status', ExpenseStatus::DRAFT);
    }

    public function scopeSubmitted(Builder $query): Builder
    {
        return $query->where('status', ExpenseStatus::SUBMITTED);
    }

    public function scopeApproved(Builder $query): Builder
    {
        return $query->where('status', ExpenseStatus::APPROVED);
    }

    public function scopeRejected(Builder $query): Builder
    {
        return $query->where('status', ExpenseStatus::REJECTED);
    }

    public function scopeReimbursed(Builder $query): Builder
    {
        return $query->where('status', ExpenseStatus::REIMBURSED);
    }

    public function scopeInvoiced(Builder $query): Builder
    {
        return $query->where('status', ExpenseStatus::INVOICED);
    }

    public function scopeBillable(Builder $query): Builder
    {
        return $query->where('billable', true);
    }

    public function scopeNonBillable(Builder $query): Builder
    {
        return $query->where('billable', false);
    }

    public function scopeNotInvoiced(Builder $query): Builder
    {
        return $query->whereNull('invoice_id')
            ->where('status', '!=', ExpenseStatus::INVOICED);
    }

    public function scopePendingApproval(Builder $query): Builder
    {
        return $query->where('status', ExpenseStatus::SUBMITTED);
    }

    public function scopeBetweenDates(Builder $query, $startDate, $endDate): Builder
    {
        return $query->whereBetween('expense_date', [$startDate, $endDate]);
    }

    public function scopeThisMonth(Builder $query): Builder
    {
        return $query->whereBetween('expense_date', [
            now()->startOfMonth(),
            now()->endOfMonth(),
        ]);
    }

    public function scopeThisYear(Builder $query): Builder
    {
        return $query->whereBetween('expense_date', [
            now()->startOfYear(),
            now()->endOfYear(),
        ]);
    }
}
