<?php

namespace App\Http\Controllers\Finance;

use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\RentLedger;
use App\Models\Tenancy;
use App\Services\RentManagementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    public function __construct(private RentManagementService $rents)
    {
        $this->middleware('permission:payments.view')->only(['index', 'show', 'receipt']);
        $this->middleware('permission:payments.record')->only(['create', 'store']);
        $this->middleware('permission:payments.reverse')->only('reverse');
    }

    public function index(Request $request): View
    {
        $user = $request->user();

        $payments = Payment::query()
            ->when(! $user->is_super_admin, fn ($q) => $q->where('landlord_id', $user->landlord_id))
            ->with(['tenant', 'tenancy.unit'])
            ->latest('payment_date')
            ->paginate(20);

        return view('finance.payments.index', compact('payments'));
    }

    public function show(Payment $payment): View
    {
        Gate::authorize('view', $payment);

        return view('finance.payments.show', ['payment' => $payment->load('tenant', 'tenancy.unit')]);
    }

    public function create(): View
    {
        $user = request()->user();

        $tenancies = Tenancy::where('status', 'active')
            ->when(! $user->is_super_admin, fn ($q) => $q->whereHas('unit.block.property', fn ($p) => $p->where('landlord_id', $user->landlord_id)))
            ->with(['tenant', 'unit'])
            ->get();

        return view('finance.payments.create', compact('tenancies'));
    }

    /**
     * Record a payment and generate the ledger credit + receipt reference.
     */
    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('record', Payment::class);

        $data = $request->validate([
            'tenancy_id' => ['required', 'exists:tenancies,id'],
            'amount' => ['required', 'numeric', 'min:0.01'],
            'payment_method' => ['required', 'in:mpesa,cash,bank_transfer,cheque,other'],
            'payment_date' => ['required', 'date'],
            'reference' => ['nullable', 'string', 'max:100'],
            'notes' => ['nullable', 'string'],
        ]);

        $tenancy = Tenancy::with('unit.block.property')->findOrFail($data['tenancy_id']);

        if (! $request->user()->can('view', $tenancy)) {
            abort(403);
        }

        $payment = $this->rents->recordPayment(
            $tenancy,
            (float) $data['amount'],
            $data['payment_method'],
            $data['payment_date'],
            $data['reference'] ?? null,
            $data['notes'] ?? null,
        );

        return redirect()->route('payments.show', $payment)->with('success', 'Payment recorded.');
    }

    public function receipt(Payment $payment): View
    {
        Gate::authorize('view', $payment);

        return view('finance.payments.receipt', [
            'payment' => $payment->load('tenant', 'tenancy.unit.block.property'),
        ]);
    }

    public function reverse(Request $request, Payment $payment): RedirectResponse
    {
        Gate::authorize('reverse', $payment);

        $data = $request->validate(['reason' => ['required', 'string', 'max:255']]);

        $payment->update(['status' => 'reversed']);

        RentLedger::create([
            'landlord_id' => $payment->landlord_id,
            'tenant_id' => $payment->tenant_id,
            'tenancy_id' => $payment->tenancy_id,
            'unit_id' => optional($payment->tenancy)->unit_id,
            'payment_id' => $payment->id,
            'transaction_type' => 'reversal',
            'debit' => $payment->amount,
            'credit' => 0,
            'reference' => 'REV-'.$payment->id,
            'description' => 'Reversal: '.$data['reason'],
            'transaction_date' => now()->toDateString(),
            'created_by' => $request->user()->id,
        ]);

        return back()->with('success', 'Payment reversed with audit entry.');
    }
}
