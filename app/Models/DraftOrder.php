<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DraftOrder extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'customer_id',
        'notes',
        'cart_items',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'total'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'cart_items' => AsArrayObject::class,
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'discount_amount' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    /**
     * Get the staff user who created the draft.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the customer associated with the draft.
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Calculate totals from cart items.
     */
    public function calculateTotals(): self
    {
        $this->subtotal = collect($this->cart_items)
            ->sum(fn($item) => $item['price'] * $item['quantity']);

        $this->total = $this->subtotal
            + $this->tax_amount
            - $this->discount_amount;

        return $this;
    }

    /**
     * Add an item to the cart.
     */
    public function addCartItem(array $item): self
    {
        $items = $this->cart_items ?? [];
        $items[] = [
            'product_id' => $item['product_id'],
            'name' => $item['name'],
            'price' => $item['price'],
            'quantity' => $item['quantity'],
            'options' => $item['options'] ?? [],
        ];

        $this->cart_items = $items;
        return $this->calculateTotals();
    }

    /**
     * Convert draft to a full order.
     */
    public function convertToOrder(): Order
    {
        return Order::create([
            'user_id' => $this->user_id,
            'customer_id' => $this->customer_id,
            'subtotal' => $this->subtotal,
            'tax_amount' => $this->tax_amount,
            'discount_amount' => $this->discount_amount,
            'total' => $this->total,
            'notes' => $this->notes,
        ]);
    }

    /**
     * Scope for drafts belonging to a specific user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
