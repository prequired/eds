<?php

declare(strict_types=1);

namespace App\Enums;

enum InvoiceStatus: string
{
    case DRAFT = 'draft';
    case SENT = 'sent';
    case VIEWED = 'viewed';
    case PAID = 'paid';
    case PARTIALLY_PAID = 'partially_paid';
    case OVERDUE = 'overdue';
    case CANCELLED = 'cancelled';

    public function label(): string
    {
        return match($this) {
            self::DRAFT => 'Draft',
            self::SENT => 'Sent',
            self::VIEWED => 'Viewed',
            self::PAID => 'Paid',
            self::PARTIALLY_PAID => 'Partially Paid',
            self::OVERDUE => 'Overdue',
            self::CANCELLED => 'Cancelled',
        };
    }

    public function color(): string
    {
        return match($this) {
            self::DRAFT => 'gray',
            self::SENT => 'blue',
            self::VIEWED => 'indigo',
            self::PAID => 'green',
            self::PARTIALLY_PAID => 'yellow',
            self::OVERDUE => 'red',
            self::CANCELLED => 'gray',
        };
    }

    public function isPaid(): bool
    {
        return $this === self::PAID;
    }

    public function isOverdue(): bool
    {
        return $this === self::OVERDUE;
    }

    public function canEdit(): bool
    {
        return in_array($this, [self::DRAFT, self::SENT]);
    }

    public function canSend(): bool
    {
        return $this === self::DRAFT;
    }
}
