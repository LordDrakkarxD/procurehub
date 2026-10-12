<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupplyOrderItem extends Model
{
    protected $fillable = [
        'supply_order_id',
        'contract_item_id',
        'quantity',
        'unit_price',
        'total',
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'unit_price' => 'decimal:2',
        'total' => 'decimal:2',
    ];

    public function supplyOrder(): BelongsTo
    {
        return $this->belongsTo(SupplyOrder::class);
    }

    public function contractItem(): BelongsTo
    {
        return $this->belongsTo(ContractItem::class);
    }

    protected static function booted(): void
    {
        static::saving(function (SupplyOrderItem $item) {
            $item->total = round((float) $item->quantity * (float) $item->unit_price, 2);
        });
    }
}
