<?php

namespace App\Models;

use App\Enums\PaymentMethod;
use App\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'amount',
        'method',
        'reference',
        'status',
        'notes'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'method' => PaymentMethod::class,
        'status' => PaymentStatus::class,
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function markAsCompleted(): self
    {
        $this->status = PaymentStatus::COMPLETED;
        return $this;
    }

    public function markAsFailed(): self
    {
        $this->status = PaymentStatus::FAILED;
        return $this;
    }

    public function isSuccessful(): bool
    {
        return $this->status === PaymentStatus::COMPLETED;
    }

    public function scopeCardPayments($query)
    {
        return $query->whereIn('method', [
            PaymentMethod::CREDIT_CARD->value,
            PaymentMethod::DEBIT_CARD->value
        ]);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', PaymentStatus::COMPLETED->value);
    }
}
