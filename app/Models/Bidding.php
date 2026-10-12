<?php

namespace App\Models;

use App\Models\Concerns\HasAttachments;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bidding extends Model
{
    use SoftDeletes, HasAttachments;

    protected $fillable = [
        'number',
        'title',
        'description',
        'company_id',
        'total_amount',
        'status',
        'opened_at',
        'awarded_at',
    ];

    protected $casts = [
        'total_amount' => 'decimal:2',
        'opened_at' => 'date',
        'awarded_at' => 'date',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function contracts(): HasMany
    {
        return $this->hasMany(Contract::class);
    }

    public function items()
    {
        return $this->hasMany(BiddingItem::class)->orderBy('item_number');
    }
}
