<?php

namespace App\Services;

use App\Models\Tenant;
use App\Models\TenantDeposit;
use App\Models\Tenancy;
use App\Models\Unit;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TenancyAllocationService
{
    /**
     * Allocate a tenant to a unit with full server-side validation:
     *  1. Unit exists (route model binding guarantees this; soft-deleted hidden)
     *  2. Unit belongs to the acting landlord
     *  3. Unit is currently available (vacant/reserved)
     *  4. No conflicting active tenancy (unless shared occupancy explicitly allowed)
     * Then records allocation/move-in dates, rent & deposit snapshots,
     * deposit-paid amount and generates opening financial records.
     */
    public function allocate(
        Tenant $tenant,
        Unit $unit,
        string $startDate,
        ?string $expectedMoveInDate,
        ?string $expectedEndDate,
        float $depositPaid,
        int $dueDay = 5,
    ): Tenancy {
        return DB::transaction(function () use ($tenant, $unit, $startDate, $expectedMoveInDate, $expectedEndDate, $depositPaid, $dueDay) {
            // (2) Ownership check — protects against cross-landlord allocation.
            $landlordId = $unit->block->property->landlord_id;
            if ((int) $tenant->landlord_id !== (int) $landlordId && ! Auth::user()?->is_super_admin) {
                throw ValidationException::withMessages([
                    'unit_id' => 'This unit does not belong to your portfolio.',
                ]);
            }

            // (3) Availability check.
            if (! in_array($unit->status, ['vacant', 'reserved'], true)) {
                throw ValidationException::withMessages([
                    'unit_id' => "Unit {$unit->unit_number} is not available (status: {$unit->status}).",
                ]);
            }

            // (4) Conflicting active tenancy check (shared occupancy opt-in only).
            $sharedAllowed = $unit->shared_occupancy_allowed
                && $unit->block->property->shared_occupancy_enabled;

            if (! $sharedAllowed && $unit->activeTenancies()->exists()) {
                throw ValidationException::withMessages([
                    'unit_id' => 'An active tenancy already occupies this unit.',
                ]);
            }

            // Snapshot the rent applicable on the start date (never overwritten later).
            $monthlyRent = $unit->rentOn($startDate) ?? $unit->current_rent;
            $depositRequired = $unit->deposit_requirement ?? 0;

            $tenancy = Tenancy::create([
                'tenant_id' => $tenant->id,
                'unit_id' => $unit->id,
                'allocation_date' => now()->toDateString(),
                'start_date' => $startDate,
                'expected_move_in_date' => $expectedMoveInDate,
                'expected_end_date' => $expectedEndDate,
                'monthly_rent' => $monthlyRent,
                'deposit_required' => $depositRequired,
                'deposit_paid' => $depositPaid,
                'due_day' => $dueDay,
                'status' => 'active',
                'created_by' => Auth::id(),
            ]);

            // (10) Opening records: deposit account + ledger entries.
            TenantDeposit::create([
                'tenancy_id' => $tenancy->id,
                'required_amount' => $depositRequired,
                'paid_amount' => $depositPaid,
                'status' => $depositPaid >= $depositRequired && $depositRequired > 0 ? 'paid' : 'pending',
            ]);

            if ($depositPaid > 0) {
                RentLedgerWriter::entry($tenancy, [
                    'transaction_type' => 'credit',
                    'credit' => $depositPaid,
                    'reference' => 'DEP-'.$tenancy->id,
                    'description' => 'Opening deposit received at allocation',
                    'transaction_date' => $startDate,
                ]);
            }

            // Opening rent obligation for the move-in month.
            RentLedgerWriter::entry($tenancy, [
                'transaction_type' => 'rent_charge',
                'debit' => $monthlyRent,
                'reference' => 'OPEN-T'.$tenancy->id,
                'description' => 'Opening rent obligation at allocation',
                'transaction_date' => $startDate,
            ]);

            $unit->update(['status' => 'occupied']);
            $tenant->update(['status' => 'active']);

            return $tenancy;
        });
    }

    /**
     * Move-out process: close (terminate) the tenancy, keep the tenant record,
     * and only release the unit once vacating is completed.
     */
    public function terminateAndVacate(Tenancy $tenancy, string $actualEndDate, string $finalStatus = 'terminated'): Tenancy
    {
        return DB::transaction(function () use ($tenancy, $actualEndDate, $finalStatus) {
            $tenancy->update([
                'status' => $finalStatus,
                'actual_end_date' => $actualEndDate,
            ]);

            // Unit becomes vacant only after the vacating process completes.
            $unit = $tenancy->unit;
            $stillOccupied = $unit->activeTenancies()->where('id', '!=', $tenancy->id)->exists();

            if (! $stillOccupied) {
                $unit->update(['status' => 'vacant']);
            }

            return $tenancy;
        });
    }
}
