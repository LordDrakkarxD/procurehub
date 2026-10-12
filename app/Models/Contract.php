<?php

namespace App\Models;

use App\Models\Concerns\HasAttachments;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Contract extends Model
{
    use SoftDeletes, HasAttachments;

    protected $fillable = [
        'number',
        'bidding_id',
        'company_id',
        'total_amount',
        'balance',
        'starts_on',
        'ends_on',
        'status',
        'notes',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'balance' => 'decimal:2',
        'starts_on' => 'date',
        'ends_on' => 'date',
    ];

    public function bidding(): BelongsTo
    {
        return $this->belongsTo(Bidding::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function items()
    {
        return $this->hasMany(ContractItem::class)->orderBy('item_number');
    }

    public function supplyOrders()
    {
        return $this->hasMany(SupplyOrder::class);
    }

    protected static function booted(): void
    {
        static::creating(function (Contract $contract) {
            if ($contract->balance === null) {
                $contract->balance = $contract->total_amount;
            }
        });
    }
}
