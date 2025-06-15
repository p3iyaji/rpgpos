<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TaxRate extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'rate',
        'is_active'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'rate' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    /**
     * Calculate tax amount for a given price.
     *
     * @param float $amount
     * @return float
     */
    public function calculateTax(float $amount): float
    {
        return $amount * ($this->rate / 100);
    }

    /**
     * Scope for active tax rates.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get the display formatted rate (e.g., "10.00%").
     */
    public function getFormattedRateAttribute(): string
    {
        return number_format($this->rate, 2) . '%';
    }
}
