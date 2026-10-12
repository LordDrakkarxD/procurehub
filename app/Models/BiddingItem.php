<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BiddingItem extends Model
{
    protected $fillable = [
        'bidding_id', 'product_id', 'quantity', 'unit_price', 'total', 'item_number',
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'unit_price' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function bidding()
    {
        return $this->belongsTo(Bidding::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    protected static function booted(): void
    {
        static::saving(function (BiddingItem $item) {
            $item->total = round((float) $item->quantity * (float) $item->unit_price, 2);
        });
    }
}
