<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupplyOrder extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'number',
        'contract_id',
        'amount',
        'status',
        'ordered_at',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'ordered_at' => 'date',
    ];

    public function contract(): BelongsTo
    {
        return $this->belongsTo(Contract::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(SupplyOrderItem::class);
    }
}
