<?php

declare(strict_types=1);

namespace App\Enums;

enum PaymentMethod: string
{
    case CREDIT_CARD = 'credit_card';
    case BANK_TRANSFER = 'bank_transfer';
    case CHECK = 'check';
    case CASH = 'cash';
    case STRIPE = 'stripe';
    case PAYPAL = 'paypal';
    case OTHER = 'other';

    public function label(): string
    {
        return match($this) {
            self::CREDIT_CARD => 'Credit Card',
            self::BANK_TRANSFER => 'Bank Transfer',
            self::CHECK => 'Check',
            self::CASH => 'Cash',
            self::STRIPE => 'Stripe',
            self::PAYPAL => 'PayPal',
            self::OTHER => 'Other',
        };
    }
}
