<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tenancy extends Model
{
    protected $fillable = [
        'tenant_id', 'unit_id', 'start_date', 'allocation_date', 'expected_move_in_date',
        'expected_end_date', 'actual_end_date', 'monthly_rent', 'deposit_required',
        'deposit_paid', 'due_day', 'status', 'created_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'allocation_date' => 'date',
        'expected_move_in_date' => 'date',
        'expected_end_date' => 'date',
        'actual_end_date' => 'date',
        'monthly_rent' => 'decimal:2',
        'deposit_required' => 'decimal:2',
        'deposit_paid' => 'decimal:2',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function deposit(): HasMany
    {
        return $this->hasMany(TenantDeposit::class, 'tenancy_id');
    }

    public function ledgerEntries(): HasMany
    {
        return $this->hasMany(RentLedger::class);
    }

    public function isActive(): bool
    {
        return in_array($this->status, ['active', 'terminated_pending_vacating'], true);
    }
}
