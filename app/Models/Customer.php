<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'loyalty_points'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'loyalty_points' => 'decimal:2',
    ];

    /**
     * Add loyalty points to customer.
     *
     * @param float $points
     * @return $this
     */
    public function addLoyaltyPoints(float $points)
    {
        $this->increment('loyalty_points', $points);
        return $this;
    }

    /**
     * Deduct loyalty points from customer.
     *
     * @param float $points
     * @return $this
     * @throws \Exception if insufficient points
     */
    public function deductLoyaltyPoints(float $points)
    {
        if ($this->loyalty_points < $points) {
            throw new \Exception('Insufficient loyalty points');
        }

        $this->decrement('loyalty_points', $points);
        return $this;
    }

    /**
     * Reset loyalty points to zero.
     *
     * @return $this
     */
    public function resetLoyaltyPoints()
    {
        $this->update(['loyalty_points' => 0]);
        return $this;
    }

    /**
     * Scope a query to only include active customers (not deleted).
     */
    public function scopeActive($query)
    {
        return $query->whereNull('deleted_at');
    }

    /**
     * Scope a query to only include customers with email.
     */
    public function scopeWithEmail($query)
    {
        return $query->whereNotNull('email');
    }

    /**
     * Scope a query to only include customers with minimum loyalty points.
     */
    public function scopeWithMinLoyaltyPoints($query, $points)
    {
        return $query->where('loyalty_points', '>=', $points);
    }
}
