<?php

namespace App\Services;

use App\Models\RentLedger;
use App\Models\Tenancy;
use Illuminate\Support\Facades\Auth;

/**
 * Small helper for writing rent-ledger entries from anywhere in the domain.
 */
class RentLedgerWriter
{
    public static function entry(Tenancy $tenancy, array $attributes): RentLedger
    {
        $unit = $tenancy->unit;

        return RentLedger::create(array_merge([
            'landlord_id' => $unit->block->property->landlord_id,
            'tenant_id' => $tenancy->tenant_id,
            'tenancy_id' => $tenancy->id,
            'unit_id' => $tenancy->unit_id,
            'debit' => 0,
            'credit' => 0,
            'created_by' => Auth::id(),
        ], $attributes));
    }
}
