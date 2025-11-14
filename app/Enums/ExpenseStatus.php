<?php

declare(strict_types=1);

namespace App\Enums;

enum ExpenseStatus: string
{
    case DRAFT = 'draft';
    case SUBMITTED = 'submitted';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case REIMBURSED = 'reimbursed';
    case INVOICED = 'invoiced';

    public function label(): string
    {
        return match ($this) {
            self::DRAFT => 'Draft',
            self::SUBMITTED => 'Submitted',
            self::APPROVED => 'Approved',
            self::REJECTED => 'Rejected',
            self::REIMBURSED => 'Reimbursed',
            self::INVOICED => 'Invoiced',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::DRAFT => 'gray',
            self::SUBMITTED => 'blue',
            self::APPROVED => 'green',
            self::REJECTED => 'red',
            self::REIMBURSED => 'purple',
            self::INVOICED => 'indigo',
        };
    }

    public function canEdit(): bool
    {
        return in_array($this, [self::DRAFT, self::REJECTED]);
    }

    public function canDelete(): bool
    {
        return in_array($this, [self::DRAFT, self::REJECTED]);
    }

    public function canSubmit(): bool
    {
        return in_array($this, [self::DRAFT, self::REJECTED]);
    }

    public function canApprove(): bool
    {
        return $this === self::SUBMITTED;
    }

    public function canReject(): bool
    {
        return $this === self::SUBMITTED;
    }

    public function canReimburse(): bool
    {
        return $this === self::APPROVED;
    }
}
