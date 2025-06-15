<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{

    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'unit_price',
        'discount_amount',
        'tax_amount',
        'total_price'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'unit_price' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_price' => 'decimal:2',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($item) {
            $item->calculateTotal();
        });
    }

    /**
     * Get the order that owns the item.
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the product associated with the item.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Calculate total price for the item.
     */
    public function calculateTotal(): self
    {
        $this->total_price = max(
            0,
            ($this->unit_price * $this->quantity)
            + $this->tax_amount
            - $this->discount_amount
        );
        return $this;
    }

    /**
     * Get the base price (before tax/discount).
     */
    public function getBasePriceAttribute(): float
    {
        return $this->unit_price * $this->quantity;
    }

    /**
     * Scope for items with discount.
     */
    public function scopeDiscounted($query)
    {
        return $query->where('discount_amount', '>', 0);
    }

    /**
     * Validate quantity is positive.
     */
    public function setQuantityAttribute($value)
    {
        if ($value < 1) {
            throw new \InvalidArgumentException('Quantity must be at least 1');
        }
        $this->attributes['quantity'] = $value;
    }

}
