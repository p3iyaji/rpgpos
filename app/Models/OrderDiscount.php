<?php

namespace App\Models;

use App\Enums\DiscountType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDiscount extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order_id',
        'discount_id',
        'name',
        'type',
        'value',
        'amount'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'type' => DiscountType::class,
        'value' => 'decimal:2',
        'amount' => 'decimal:2',
    ];

    /**
     * Get the order that owns the discount.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the discount that was applied.
     */
    public function discount()
    {
        return $this->belongsTo(Discount::class);
    }

    /**
     * Calculate the discount amount for a given price.
     */
    public function calculateDiscount(float $price): float
    {
        return match ($this->type) {
            DiscountType::PERCENTAGE => $price * ($this->value / 100),
            DiscountType::FIXED => min($this->value, $price),
        };
    }

    /**
     * Scope for percentage discounts.
     */
    public function scopePercentage($query)
    {
        return $query->where('type', DiscountType::PERCENTAGE);
    }

    /**
     * Scope for fixed amount discounts.
     */
    public function scopeFixed($query)
    {
        return $query->where('type', DiscountType::FIXED);
    }
}
