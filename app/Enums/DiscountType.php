<?php

namespace App\Enums;

enum DiscountType
{
    case PERCENTAGE = 'percentage';
    case FIXED = 'fixed';
    case BUY_X_GET_Y = 'buy_x_get_y';

    public function label(): string
    {
        return match ($this) {
            self::PERCENTAGE => 'Percentage',
            self::FIXED => 'Fixed Amount',
            self::BUY_X_GET_Y => 'Buy X Get Y',
        };
    }

    public function isPercentageBased(): bool
    {
        return $this === self::PERCENTAGE;
    }

    public function isFixedAmount(): bool
    {
        return $this === self::FIXED;
    }

    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
