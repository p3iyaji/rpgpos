<?php

namespace App\Enums;

enum PaymentMethod
{
    case CASH = 'cash';
    case CREDIT_CARD = 'credit_card';
    case DEBIT_CARD = 'debit_card';
    case BANK_TRANSFER = 'bank_transfer';
    case MOBILE_PAYMENT = 'mobile_payment';
    case OTHER = 'other';

    public function label(): string
    {
        return match ($this) {
            self::CASH => 'Cash',
            self::CREDIT_CARD => 'Credit Card',
            self::DEBIT_CARD => 'Debit Card',
            self::BANK_TRANSFER => 'Bank Transfer',
            self::MOBILE_PAYMENT => 'Mobile Payment',
            self::OTHER => 'Other',
        };
    }

    public function isCard(): bool
    {
        return in_array($this, [self::CREDIT_CARD, self::DEBIT_CARD]);
    }
}
