<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Concerns\HasAttachments;
use Illuminate\Database\Eloquent\SoftDeletes;

class Company extends Model
{
    use SoftDeletes, HasAttachments;

    protected $fillable = [
        'name', 'trade_name', 'document', 'email', 'phone', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
