<?php

namespace App\Enums;

enum InventoryAction
{
    case ADD = 'add';
    case SUBTRACT = 'subtract';
    case ADJUST = 'adjust';
    case SALE = 'sale';
    case RETURN = 'return';

    public function label(): string
    {
        return match ($this) {
            self::ADD => 'Add Stock',
            self::SUBTRACT => 'Subtract Stock',
            self::ADJUST => 'Adjustment',
            self::SALE => 'Sale',
            self::RETURN => 'Return',
        };
    }

    public function isAddition(): bool
    {
        return in_array($this, [self::ADD, self::RETURN]);
    }

    public function isSubtraction(): bool
    {
        return in_array($this, [self::SUBTRACT, self::SALE]);
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
