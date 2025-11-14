<?php

declare(strict_types=1);

namespace App\Enums;

enum TimeEntryStatus: string
{
    case DRAFT = 'draft';
    case SUBMITTED = 'submitted';
    case APPROVED = 'approved';
    case INVOICED = 'invoiced';

    public function label(): string
    {
        return match($this) {
            self::DRAFT => 'Draft',
            self::SUBMITTED => 'Submitted',
            self::APPROVED => 'Approved',
            self::INVOICED => 'Invoiced',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::DRAFT => 'gray',
            self::SUBMITTED => 'yellow',
            self::APPROVED => 'green',
            self::INVOICED => 'blue',
        };
    }

    public function canEdit(): bool
    {
        return in_array($this, [self::DRAFT, self::SUBMITTED]);
    }

    public function canDelete(): bool
    {
        return $this !== self::INVOICED;
    }
}
