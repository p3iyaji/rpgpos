<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class DiscountCategory extends Pivot
{
    public $incrementing = false;

    protected $fillable = [
        'discount_id',
        'category_id',
    ];

    public function discount()
    {
        return $this->belongsTo(Discount::class);
    }

    public function category()
    {
        return $this->belongs(Category::class);
    }
}
