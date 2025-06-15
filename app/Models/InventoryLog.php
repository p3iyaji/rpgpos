<?php

namespace App\Models;

use App\Enums\InventoryAction;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'quantity',
        'action',
        'notes',
        'user_id'
    ];

    protected $casts = [
        'action' => InventoryAction::class,
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeAdditions($query)
    {
        return $query->where('action', InventoryAction::ADD->value);
    }

    public function scopeSubtractions($query)
    {
        return $query->where('action', InventoryAction::SUBTRACT->value);
    }

    public function scopeSales($query)
    {
        return $query->where('action', InventoryAction::SALE->value);
    }

    public function getEffectiveQuantityAttribute(): int
    {
        return $this->action->isAddition()
            ? $this->quantity
            : -$this->quantity;
    }

    public static function log(
        int $productId,
        int $quantity,
        InventoryAction $action,
        ?string $notes = null,
        ?int $userId = null
    ): self {
        return self::create([
            'product_id' => $productId,
            'quantity' => abs($quantity),
            'action' => $action,
            'notes' => $notes,
            'user_id' => $userId ?? auth()->id(),
        ]);
    }

}
