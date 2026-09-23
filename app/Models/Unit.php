<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Unit extends Model
{
    use SoftDeletes; // Units with financial history are never hard deleted.

    public const STATUSES = [
        'vacant', 'reserved', 'occupied', 'under_maintenance', 'blocked', 'closed',
    ];

    protected $fillable = [
        'block_id', 'unit_number', 'floor', 'unit_type', 'status',
        'current_rent', 'deposit_requirement', 'description',
        'meter_info', 'utility_config', 'shared_occupancy_allowed',
    ];

    protected $casts = [
        'current_rent' => 'decimal:2',
        'deposit_requirement' => 'decimal:2',
        'meter_info' => 'array',
        'utility_config' => 'array',
        'shared_occupancy_allowed' => 'boolean',
    ];

    public function block(): BelongsTo
    {
        return $this->belongsTo(Block::class);
    }

    public function rentHistories(): HasMany
    {
        return $this->hasMany(UnitRentHistory::class)->orderBy('effective_from');
    }

    public function tenancies(): HasMany
    {
        return $this->hasMany(Tenancy::class);
    }

    public function activeTenancies(): HasMany
    {
        return $this->tenancies()->whereIn('status', ['active', 'terminated_pending_vacating']);
    }

    public function scopeAvailable(Builder $query): Builder
    {
        return $query->whereIn('status', ['vacant', 'reserved']);
    }

    /**
     * Rent that was applicable on a given date, falling back to current rent.
     */
    public function rentOn(string $date): ?string
    {
        $history = $this->rentHistories()
            ->where('effective_from', '<=', $date)
            ->where(function ($q) use ($date) {
                $q->whereNull('effective_to')->orWhere('effective_to', '>=', $date);
            })
            ->orderByDesc('effective_from')
            ->first();

        return $history?->rent_amount ?? $this->current_rent;
    }
}
