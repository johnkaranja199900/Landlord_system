<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RentLedger extends Model
{
    protected $table = 'rent_ledger';

    protected $fillable = [
        'landlord_id', 'tenant_id', 'tenancy_id', 'unit_id', 'invoice_id', 'payment_id',
        'transaction_type', 'debit', 'credit', 'reference', 'description',
        'transaction_date', 'created_by',
    ];

    protected $casts = [
        'debit' => 'decimal:2',
        'credit' => 'decimal:2',
        'transaction_date' => 'date',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class)->withTrashed();
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(RentInvoice::class, 'invoice_id');
    }
}
