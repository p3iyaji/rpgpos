<?php

namespace App\Enums;

enum ReturnStatus
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case COMPLETED = 'completed';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::APPROVED => 'Approved',
            self::REJECTED => 'Rejected',
            self::COMPLETED => 'Completed',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::PENDING => 'bg-yellow-100 text-yellow-800',
            self::APPROVED => 'bg-blue-100 text-blue-800',
            self::REJECTED => 'bg-red-100 text-red-800',
            self::COMPLETED => 'bg-green-100 text-green-800',
        };
    }

    public function isApproved(): bool
    {
        return $this === self::APPROVED || $this === self::COMPLETED;
    }

    public function isProcessed(): bool
    {
        return $this !== self::PENDING;
    }
}
