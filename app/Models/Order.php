<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Enums\OrderStatus;

class Order extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'order_number',
        'customer_id',
        'user_id',
        'status',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'total',
        'notes'
    ];

    protected $casts = [
        'status' => OrderStatus::class,
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            $order->order_number = $order->order_number ?? static::generateOrderNumber();
        });
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', OrderStatus::COMPLETED->value);
    }

    public function scopePending($query)
    {
        return $query->where('status', OrderStatus::PENDING->value);
    }

    public function isPaid(): bool
    {
        return $this->payments()->where('status', 'completed')->sum('amount') >= $this->total;
    }

    public function calculateTotals()
    {
        $this->subtotal = $this->items->sum(function ($item) {
            return $item->price * $item->quantity;
        });
        $this->total = $this->subtotal + $this->tax_amount - $this->discount_amount;
        return $this;
    }

    protected static function generateOrderNumber(): string
    {
        $prefix = 'ORD-' . now()->format('Ymd');
        $lastOrder = static::where('order_number', 'like', $prefix . '%')->latest()->first();
        $sequence = $lastOrder ? (int) str_replace($prefix, '', $lastOrder->order_number) + 1 : 1;

        return $prefix . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }
}
