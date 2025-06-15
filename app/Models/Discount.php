<?php

namespace App\Models;

use App\Enums\DiscountType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class Discount extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'type',
        'value',
        'start_date',
        'end_date',
        'min_quantity',
        'min_amount',
        'usage_limit',
        'usage_count',
        'is_active'
    ];

    protected $casts = [
        'type' => DiscountType::class,
        'value' => 'decimal:2',
        'min_amount' => 'decimal:2',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Check if discount is currently active
     */
    public function isActive(): bool
    {
        $now = Carbon::now();
        return $this->is_active
            && $now->between($this->start_date, $this->end_date)
            && ($this->usage_limit === null || $this->usage_count < $this->usage_limit);
    }

    /**
     * Check if discount can be applied to given cart/order
     */
    public function isApplicable(float $amount = null, int $quantity = null): bool
    {
        if (!$this->isActive()) {
            return false;
        }

        if ($this->min_amount && $amount < $this->min_amount) {
            return false;
        }

        if ($this->min_quantity && $quantity < $this->min_quantity) {
            return false;
        }

        return true;
    }

    /**
     * Calculate discount amount for a given price
     */
    public function calculateDiscount(float $originalPrice): float
    {
        return match ($this->type) {
            DiscountType::PERCENTAGE => $originalPrice * ($this->value / 100),
            DiscountType::FIXED => min($this->value, $originalPrice),
            DiscountType::BUY_X_GET_Y => 0, // Special case - implement separately
        };
    }

    /**
     * Record usage of this discount
     */
    public function recordUsage(): self
    {
        $this->increment('usage_count');
        return $this;
    }

    /**
     * Scope for active discounts
     */
    public function scopeActive($query)
    {
        $now = Carbon::now();
        return $query->where('is_active', true)
            ->where('start_date', '<=', $now)
            ->where('end_date', '>=', $now)
            ->where(function ($q) {
                $q->whereNull('usage_limit')
                    ->orWhereColumn('usage_count', '<', 'usage_limit');
            });
    }

    /**
     * Scope for percentage discounts
     */
    public function scopePercentage($query)
    {
        return $query->where('type', DiscountType::PERCENTAGE);
    }

    /**
     * Scope for fixed amount discounts
     */
    public function scopeFixed($query)
    {
        return $query->where('type', DiscountType::FIXED);
    }
}
