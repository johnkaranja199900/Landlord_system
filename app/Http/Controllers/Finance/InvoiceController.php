<?php

namespace App\Http\Controllers\Finance;

use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Models\RentInvoice;
use App\Models\Tenancy;
use App\Services\RentManagementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    public function __construct(private RentManagementService $rents)
    {
        $this->middleware('permission:invoices.view')->only(['index', 'show']);
        $this->middleware('permission:invoices.generate')->only(['create', 'store', 'cancel']);
    }

    public function index(Request $request): View
    {
        $user = $request->user();

        $invoices = RentInvoice::query()
            ->when(! $user->is_super_admin, fn ($q) => $q->where('landlord_id', $user->landlord_id))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->with(['tenant', 'unit.block.property'])
            ->latest('billing_year')->latest('billing_month')
            ->paginate(20);

        return view('finance.invoices.index', compact('invoices'));
    }

    public function show(RentInvoice $invoice): View
    {
        Gate::authorize('view', $invoice);

        return view('finance.invoices.show', ['invoice' => $invoice->load('tenant', 'unit.block.property')]);
    }

    public function create(): View
    {
        $user = request()->user();

        $tenancies = Tenancy::where('status', 'active')
            ->when(! $user->is_super_admin, fn ($q) => $q->whereHas('unit.block.property', fn ($p) => $p->where('landlord_id', $user->landlord_id)))
            ->with(['tenant', 'unit.block.property'])
            ->get();

        return view('finance.invoices.create', compact('tenancies'));
    }

    /**
     * Generate a monthly invoice. The rent amount is taken from the unit's
     * rent history for the billing month — historical values are preserved.
     */
    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('generate', RentInvoice::class);

        $data = $request->validate([
            'tenancy_id' => ['required', 'exists:tenancies,id'],
            'billing_year' => ['required', 'integer', 'min:2000', 'max:2100'],
            'billing_month' => ['required', 'integer', 'min:1', 'max:12'],
        ]);

        $tenancy = Tenancy::with('unit.block.property')->findOrFail($data['tenancy_id']);

        if (! $request->user()->can('view', $tenancy)) {
            abort(403);
        }

        $invoice = $this->rents->generateInvoice($tenancy, (int) $data['billing_year'], (int) $data['billing_month']);

        return redirect()->route('invoices.show', $invoice)->with('success', 'Invoice generated.');
    }

    public function cancel(RentInvoice $invoice): RedirectResponse
    {
        Gate::authorize('cancel', $invoice);

        $invoice->update(['status' => 'cancelled']);

        return back()->with('success', 'Invoice cancelled.');
    }
}
