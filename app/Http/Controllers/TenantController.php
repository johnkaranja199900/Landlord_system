<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Gate;
use App\Models\Tenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class TenantController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:tenants.view')->only(['index', 'show']);
        $this->middleware('permission:tenants.create')->only(['create', 'store']);
        $this->middleware('permission:tenants.update')->only(['edit', 'update']);
        $this->middleware('permission:tenants.delete')->only('destroy');
    }

    private function scopedQuery()
    {
        $user = request()->user();

        return $user->is_super_admin
            ? Tenant::query()
            : Tenant::where('landlord_id', $user->landlord_id); // never expose other landlords' tenants
    }

    public function index(Request $request): View
    {
        $tenants = $this->scopedQuery()
            ->when($request->filled('search'), function ($q) use ($request) {
                $s = $request->string('search');
                $q->where(fn ($w) => $w->where('full_name', 'like', "%{$s}%")
                    ->orWhere('phone', 'like', "%{$s}%")
                    ->orWhere('account_number', 'like', "%{$s}%"));
            })
            ->with('tenancies.unit')
            ->latest()
            ->paginate(20);

        return view('tenants.index', compact('tenants'));
    }

    public function create(): View
    {
        return view('tenants.form', ['tenant' => new Tenant()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validated($request);
        $data['landlord_id'] = $request->user()->landlord_id; // server-side scoping
        $data['registered_date'] = now()->toDateString();

        Tenant::create($data);

        return redirect()->route('tenants.index')->with('success', 'Tenant registered.');
    }

    public function show(Tenant $tenant): View
    {
        Gate::authorize('view', $tenant);

        return view('tenants.show', [
            'tenant' => $tenant->load('tenancies.unit.block.property'),
        ]);
    }

    public function edit(Tenant $tenant): View
    {
        Gate::authorize('update', $tenant);

        return view('tenants.form', compact('tenant'));
    }

    public function update(Request $request, Tenant $tenant): RedirectResponse
    {
        Gate::authorize('update', $tenant);

        $tenant->update($this->validated($request, $tenant));

        return back()->with('success', 'Tenant updated.');
    }

    /**
     * Tenants with history are NEVER deleted — the policy blocks it.
     */
    public function destroy(Tenant $tenant): RedirectResponse
    {
        Gate::authorize('delete', $tenant);

        $tenant->delete();

        return redirect()->route('tenants.index')->with('success', 'Tenant removed.');
    }

    private function validated(Request $request, ?Tenant $tenant = null): array
    {
        return $request->validate([
            'full_name' => ['required', 'string', 'max:150'],
            'phone' => ['required', 'string', 'max:30'],
            'alternative_phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:150'],
            'national_id_reference' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:255'],
            'emergency_contact' => ['nullable', 'string', 'max:255'],
            'next_of_kin' => ['nullable', 'string', 'max:150'],
            'account_number' => [
                'required', 'string', 'max:50',
                Rule::unique('tenants', 'account_number')->ignore($tenant?->id),
            ],
            'status' => ['required', 'in:active,inactive,blacklisted'],
            'notes' => ['nullable', 'string'],
        ]);
    }
}
