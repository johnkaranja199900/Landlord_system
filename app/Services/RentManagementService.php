<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\RentInvoice;
use App\Models\RentLedger;
use App\Models\Tenancy;
use App\Models\Unit;
use App\Models\UnitRentHistory;
use DateTime;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RentManagementService
{
    /**
     * Change the current rent of a unit WITHOUT destroying history.
     * Closes the open rent-history segment and opens a new one.
     */
    public function updateUnitRent(Unit $unit, float $newRent, string $effectiveFrom, ?string $reason = null): UnitRentHistory
    {
        return DB::transaction(function () use ($unit, $newRent, $effectiveFrom, $reason) {
            // Close the currently-open historical segment (if any).
            $unit->rentHistories()
                ->whereNull('effective_to')
                ->update(['effective_to' => date('Y-m-d', strtotime($effectiveFrom.' -1 day'))]);

            $history = UnitRentHistory::create([
                'unit_id' => $unit->id,
                'rent_amount' => $newRent,
                'effective_from' => $effectiveFrom,
                'effective_to' => null,
                'reason' => $reason ?? 'rent adjustment',
                'created_by' => Auth::id(),
            ]);

            $unit->update(['current_rent' => $newRent]);

            return $history;
        });
    }

    /**
     * Generate a monthly rent invoice for an active tenancy. The rent amount
     * is resolved from RENT HISTORY for the billing month, so changing the
     * current rent never rewrites past obligations.
     */
    public function generateInvoice(Tenancy $tenancy, int $year, int $month): RentInvoice
    {
        $firstOfMonth = sprintf('%04d-%02d-01', $year, $month);
        $unit = $tenancy->unit;

        // Rent applicable during that period (historical snapshot).
        $rentAmount = $unit->rentOn($firstOfMonth) ?? $tenancy->monthly_rent;

        $previous = RentInvoice::where('tenancy_id', $tenancy->id)
            ->orderByDesc('billing_year')->orderByDesc('billing_month')
            ->first();

        $previousBalance = $previous
            ? max(0, (float) $previous->total_due - $this->paidOnInvoice($previous))
            : 0;

        $dueDate = sprintf('%04d-%02d-%02d', $year, $month, min(28, (int) $tenancy->due_day));
        $landlordId = $unit->block->property->landlord_id;

        return DB::transaction(function () use ($tenancy, $unit, $landlordId, $year, $month, $rentAmount, $previousBalance, $dueDate, $firstOfMonth) {
            $invoice = RentInvoice::create([
                'landlord_id' => $landlordId,
                'tenant_id' => $tenancy->tenant_id,
                'tenancy_id' => $tenancy->id,
                'unit_id' => $unit->id,
                'invoice_number' => $this->nextInvoiceNumber($year, $month),
                'billing_year' => $year,
                'billing_month' => $month,
                'rent_amount' => $rentAmount,
                'previous_balance' => $previousBalance,
                'credit_amount' => 0,
                'other_charges' => 0,
                'total_due' => $rentAmount + $previousBalance,
                'due_date' => $dueDate,
                'status' => 'issued',
                'issued_at' => now(),
            ]);

            // Opening ledger entry: obligation as it stood at generation time.
            RentLedger::create([
                'landlord_id' => $landlordId,
                'tenant_id' => $invoice->tenant_id,
                'tenancy_id' => $invoice->tenancy_id,
                'unit_id' => $invoice->unit_id,
                'invoice_id' => $invoice->id,
                'transaction_type' => 'rent_charge',
                'debit' => $invoice->total_due,
                'credit' => 0,
                'reference' => $invoice->invoice_number,
                'description' => 'Rent '.DateTime::createFromFormat('!m', $month)->format('F')." {$year} (amount applicable at generation)",
                'transaction_date' => $firstOfMonth,
                'created_by' => Auth::id(),
            ]);

            return $invoice;
        });
    }

    private function paidOnInvoice(RentInvoice $invoice): float
    {
        return (float) RentLedger::where('invoice_id', $invoice->id)
            ->where('transaction_type', 'payment')->sum('credit');
    }

    private function nextInvoiceNumber(int $year, int $month): string
    {
        $seq = RentInvoice::where('billing_year', $year)->where('billing_month', $month)->count() + 1;

        return sprintf('INV-%04d%02d-%05d', $year, $month, $seq);
    }

    /**
     * Record a payment against a tenancy: creates payment + ledger credit +
     * receipt reference. Allocation to invoices follows oldest-first order.
     */
    public function recordPayment(Tenancy $tenancy, float $amount, string $method, string $paymentDate, ?string $reference = null, ?string $notes = null): Payment
    {
        return DB::transaction(function () use ($tenancy, $amount, $method, $paymentDate, $reference, $notes) {
            $landlordId = $tenancy->unit->block->property->landlord_id;

            $payment = Payment::create([
                'landlord_id' => $landlordId,
                'tenant_id' => $tenancy->tenant_id,
                'tenancy_id' => $tenancy->id,
                'payment_method' => $method,
                'amount' => $amount,
                'payment_date' => $paymentDate,
                'reference' => $reference,
                'status' => 'confirmed',
                'notes' => $notes,
            ]);

            $receipt = 'RCP-'.str_pad((string) $payment->id, 6, '0', STR_PAD_LEFT);

            RentLedger::create([
                'landlord_id' => $landlordId,
                'tenant_id' => $tenancy->tenant_id,
                'tenancy_id' => $tenancy->id,
                'unit_id' => $tenancy->unit_id,
                'payment_id' => $payment->id,
                'transaction_type' => 'payment',
                'debit' => 0,
                'credit' => $amount,
                'reference' => $receipt,
                'description' => 'Rent payment received',
                'transaction_date' => $paymentDate,
                'created_by' => Auth::id(),
            ]);

            $this->applyOldestFirst($tenancy, $amount);

            return $payment->fresh();
        });
    }

    private function applyOldestFirst(Tenancy $tenancy, float $amount): void
    {
        $remaining = $amount;

        $openInvoices = RentInvoice::where('tenancy_id', $tenancy->id)
            ->whereIn('status', ['issued', 'partially_paid', 'overdue'])
            ->orderBy('due_date')
            ->get();

        foreach ($openInvoices as $invoice) {
            if ($remaining <= 0) {
                break;
            }

            $paidSoFar = $this->paidOnInvoice($invoice);
            $outstanding = (float) $invoice->total_due - $paidSoFar;
            $applied = min($outstanding, $remaining);
            $remaining -= $applied;

            $invoice->update([
                'status' => ($paidSoFar + $applied) >= (float) $invoice->total_due ? 'paid' : 'partially_paid',
            ]);
        }
    }
}
