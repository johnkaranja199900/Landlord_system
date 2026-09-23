<?php

namespace App\Http\Controllers\Finance;

use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Models\RentLedger;
use App\Models\Tenancy;
use App\Services\RentLedgerWriter;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LedgerController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:ledger.view')->only(['index', 'tenantLedger']);
        $this->middleware('permission:ledger.adjust')->only('adjust');
    }

    public function index(Request $request): View
    {
        $user = $request->user();

        $entries = RentLedger::query()
            ->when(! $user->is_super_admin, fn ($q) => $q->where('landlord_id', $user->landlord_id))
            ->when($request->filled('transaction_type'), fn ($q) => $q->where('transaction_type', $request->transaction_type))
            ->with(['tenant', 'unit'])
            ->latest('transaction_date')
            ->paginate(30);

        return view('finance.ledger.index', compact('entries'));
    }

    public function tenantLedger(Request $request, Tenancy $tenancy): View
    {
        Gate::authorize('view', $tenancy);

        $entries = RentLedger::where('tenancy_id', $tenancy->id)
            ->orderBy('transaction_date')->orderBy('id')->get();

        $balance = $entries->sum('debit') - $entries->sum('credit');

        return view('finance.ledger.tenant', compact('tenancy', 'entries', 'balance'));
    }

    /**
     * Manual ledger adjustment (accountant/super-admin only). Creates an
     * immutable journal entry — never edits existing entries.
     */
    public function adjust(Request $request, Tenancy $tenancy): RedirectResponse
    {
        Gate::authorize('adjust', RentLedger::class);
        Gate::authorize('view', $tenancy);

        $data = $request->validate([
            'transaction_type' => ['required', 'in:adjustment,credit,waiver'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'direction' => ['required', 'in:debit,credit'],
            'description' => ['required', 'string', 'max:255'],
            'transaction_date' => ['required', 'date'],
        ]);

        RentLedgerWriter::entry($tenancy, [
            'transaction_type' => $data['transaction_type'],
            'debit' => $data['direction'] === 'debit' ? $data['amount'] : 0,
            'credit' => $data['direction'] === 'credit' ? $data['amount'] : 0,
            'reference' => 'ADJ-'.now()->format('YmdHis'),
            'description' => $data['description'],
            'transaction_date' => $data['transaction_date'],
        ]);

        return back()->with('success', 'Ledger adjustment recorded.');
    }
}
