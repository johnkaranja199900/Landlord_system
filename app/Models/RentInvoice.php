<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RentInvoice extends Model
{
    protected $fillable = [
        'landlord_id', 'tenant_id', 'tenancy_id', 'unit_id', 'invoice_number',
        'billing_year', 'billing_month', 'rent_amount', 'previous_balance',
        'credit_amount', 'other_charges', 'total_due', 'due_date', 'status', 'issued_at',
    ];

    protected $casts = [
        'rent_amount' => 'decimal:2',
        'previous_balance' => 'decimal:2',
        'credit_amount' => 'decimal:2',
        'other_charges' => 'decimal:2',
        'total_due' => 'decimal:2',
        'due_date' => 'date',
        'issued_at' => 'datetime',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class)->withTrashed();
    }

    public function tenancy(): BelongsTo
    {
        return $this->belongsTo(Tenancy::class);
    }
}
