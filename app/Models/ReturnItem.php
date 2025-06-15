<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'return_id',
        'order_item_id',
        'quantity',
        'unit_price',
        'total_price',
        'reason'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'unit_price' => 'decimal:2',
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
     * Get the return that owns this item.
     */
    public function return()
    {
        return $this->belongsTo(Returns::class);
    }

    /**
     * Get the original order item being returned.
     */
    public function orderItem()
    {
        return $this->belongsTo(OrderItem::class);
    }

    /**
     * Calculate total price for the returned item.
     */
    public function calculateTotal(): self
    {
        $this->total_price = $this->unit_price * $this->quantity;
        return $this;
    }

    /**
     * Scope for items with specific return reason.
     */
    public function scopeWithReason($query, string $reason)
    {
        return $query->where('reason', 'like', "%{$reason}%");
    }

    /**
     * Check if this is a full return (all quantities).
     */
    public function isFullReturn(): bool
    {
        return $this->quantity >= $this->orderItem->quantity;
    }
}
