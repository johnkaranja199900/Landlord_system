<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenantDeposit extends Model
{
    protected $fillable = ['tenancy_id', 'required_amount', 'paid_amount', 'status'];

    protected $casts = [
        'required_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
    ];

    public function tenancy(): BelongsTo
    {
        return $this->belongsTo(Tenancy::class);
    }
}
