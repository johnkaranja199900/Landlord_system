<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Gate;
use App\Models\Tenancy;
use App\Models\Tenant;
use App\Models\Unit;
use App\Services\TenancyAllocationService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TenancyController extends Controller
{
    public function __construct(private TenancyAllocationService $allocator)
    {
        $this->middleware('permission:tenancies.view')->only(['index', 'show']);
        $this->middleware('permission:tenancies.allocate')->only(['create', 'store']);
        $this->middleware('permission:tenancies.terminate')->only('terminate');
        $this->middleware('permission:tenancies.update-status')->only('updateStatus');
    }

    public function index(): View
    {
        $user = request()->user();

        $tenancies = Tenancy::query()
            ->when(! $user->is_super_admin, fn ($q) => $q->whereHas(
                'unit.block.property',
                fn ($p) => $p->where('landlord_id', $user->landlord_id)
            ))
            ->with(['tenant', 'unit.block.property'])
            ->latest()
            ->paginate(20);

        return view('tenancies.index', compact('tenancies'));
    }

    public function show(Tenancy $tenancy): View
    {
        Gate::authorize('view', $tenancy);

        return view('tenancies.show', [
            'tenancy' => $tenancy->load('tenant', 'unit.block.property', 'deposit', 'ledgerEntries'),
        ]);
    }

    public function create(): View
    {
        $user = request()->user();

        $units = Unit::query()
            ->when(! $user->is_super_admin, fn ($q) => $q->whereHas('block.property', fn ($p) => $p->where('landlord_id', $user->landlord_id)))
            ->available()
            ->with('block.property')
            ->get();

        $tenants = Tenant::query()
            ->when(! $user->is_super_admin, fn ($q) => $q->where('landlord_id', $user->landlord_id))
            ->where('status', '!=', 'blacklisted')
            ->orderBy('full_name')
            ->get();

        return view('tenancies.allocate', compact('units', 'tenants'));
    }

    /**
     * Allocate a tenant to a vacant unit. All pre-checks (ownership,
     * availability, conflicting tenancy) run inside the service.
     */
    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('allocate', Tenancy::class);

        $data = $request->validate([
            'tenant_id' => ['required', 'exists:tenants,id'],
            'unit_id' => ['required', 'exists:units,id'],
            'start_date' => ['required', 'date'],
            'expected_move_in_date' => ['nullable', 'date'],
            'expected_end_date' => ['nullable', 'date', 'after:start_date'],
            'deposit_paid' => ['required', 'numeric', 'min:0'],
            'due_day' => ['required', 'integer', 'between:1,28'],
        ]);

        $tenant = Tenant::findOrFail($data['tenant_id']);
        $unit = Unit::findOrFail($data['unit_id']);

        // Cross-landlord protection before touching the service layer.
        if (! $request->user()->can('view', $tenant)) {
            abort(403, 'This tenant does not belong to your portfolio.');
        }

        $tenancy = $this->allocator->allocate(
            $tenant,
            $unit,
            $data['start_date'],
            $data['expected_move_in_date'] ?? null,
            $data['expected_end_date'] ?? null,
            (float) $data['deposit_paid'],
            (int) $data['due_day'],
        );

        return redirect()->route('tenancies.show', $tenancy)
            ->with('success', 'Tenant allocated. Opening records generated.');
    }

    /**
     * Move-out: closes the tenancy (history preserved, tenant record kept).
     */
    public function terminate(Request $request, Tenancy $tenancy): RedirectResponse
    {
        Gate::authorize('terminate', $tenancy);

        $data = $request->validate([
            'actual_end_date' => ['required', 'date'],
            'final_status' => ['required', 'in:terminated,evicted,expired'],
        ]);

        $this->allocator->terminateAndVacate($tenancy, $data['actual_end_date'], $data['final_status']);

        return back()->with('success', 'Tenancy closed and vacating process completed.');
    }

    public function updateStatus(Request $request, Tenancy $tenancy): RedirectResponse
    {
        Gate::authorize('updateStatus', $tenancy);

        $data = $request->validate(['status' => ['required', 'in:active,terminated_pending_vacating']]);

        $tenancy->update($data);

        return back()->with('success', 'Tenancy status updated.');
    }
}
