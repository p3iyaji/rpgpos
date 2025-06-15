<?php

namespace App\Models;

use App\Enums\ReturnStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Returns extends Model
{
    use HasFactory;

    protected $table = 'returns'; // Important since 'return' is a reserved word

    protected $fillable = [
        'order_id',
        'user_id',
        'status',
        'reason',
        'notes',
        'refund_amount'
    ];

    protected $casts = [
        'status' => ReturnStatus::class,
        'refund_amount' => 'decimal:2',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function approve(float $refundAmount = null): self
    {
        $this->status = ReturnStatus::APPROVED;
        if ($refundAmount !== null) {
            $this->refund_amount = $refundAmount;
        }
        return $this;
    }

    public function reject(): self
    {
        $this->status = ReturnStatus::REJECTED;
        return $this;
    }

    public function complete(): self
    {
        $this->status = ReturnStatus::COMPLETED;
        return $this;
    }

    public function scopePending($query)
    {
        return $query->where('status', ReturnStatus::PENDING->value);
    }

    public function scopeApproved($query)
    {
        return $query->where('status', ReturnStatus::APPROVED->value);
    }

    public function scopeProcessed($query)
    {
        return $query->whereNot('status', ReturnStatus::PENDING->value);
    }
}
