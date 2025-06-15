<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;


class DiscountProduct extends Pivot
{
    public $incrementing = false;

    protected $fillable = [
        'product_id',
        'discount_id',
    ];


    public function discount()
    {
        return $this->belongsTo(Discount::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
