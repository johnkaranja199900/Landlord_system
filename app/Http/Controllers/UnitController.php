<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Gate;
use App\Models\Block;
use App\Models\Unit;
use App\Services\RentManagementService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UnitController extends Controller
{
    public function __construct(private RentManagementService $rents)
    {
        $this->middleware('permission:units.view')->only(['index', 'show', 'rentHistory']);
        $this->middleware('permission:units.create')->only(['create', 'store']);
        $this->middleware('permission:units.update')->only(['edit', 'update']);
        $this->middleware('permission:units.update-rent')->only('updateRent');
        $this->middleware('permission:units.delete')->only('destroy');
    }

    public function index(Block $block): View
    {
        Gate::authorize('view', $block);

        return view('units.index', [
            'block' => $block,
            'units' => $block->units()->paginate(25),
        ]);
    }

    public function show(Unit $unit): View
    {
        Gate::authorize('view', $unit);

        return view('units.show', ['unit' => $unit->load('rentHistories', 'tenancies.tenant')]);
    }

    public function store(Request $request, Block $block): RedirectResponse
    {
        Gate::authorize('create', Unit::class);
        Gate::authorize('update', $block); // must own parent block

        $data = $request->validate([
            'unit_number' => ['required', 'string', 'max:20', function ($attr, $value, $fail) use ($block) {
                if ($block->units()->withTrashed()->where('unit_number', $value)->exists()) {
                    $fail('This unit number already exists in the block (including archived units).');
                }
            }],
            'floor' => ['nullable', 'string', 'max:20'],
            'unit_type' => ['nullable', 'string', 'max:50'],
            'status' => ['required', 'in:'.implode(',', Unit::STATUSES)],
            'current_rent' => ['nullable', 'numeric', 'min:0'],
            'deposit_requirement' => ['nullable', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'meter_info' => ['nullable', 'array'],
            'utility_config' => ['nullable', 'array'],
            'shared_occupancy_allowed' => ['boolean'],
        ]);

        $unit = $block->units()->create($data);

        // Seed initial rent history so every unit's rent is traceable from day one.
        if (! empty($data['current_rent'])) {
            $this->rents->updateUnitRent($unit, (float) $data['current_rent'], now()->toDateString(), 'initial rent');
        }

        return back()->with('success', 'Unit created.');
    }

    public function update(Request $request, Unit $unit): RedirectResponse
    {
        Gate::authorize('update', $unit);

        $data = $request->validate([
            'floor' => ['nullable', 'string', 'max:20'],
            'unit_type' => ['nullable', 'string', 'max:50'],
            'status' => ['required', 'in:'.implode(',', Unit::STATUSES)],
            'deposit_requirement' => ['nullable', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'meter_info' => ['nullable', 'array'],
            'utility_config' => ['nullable', 'array'],
            'shared_occupancy_allowed' => ['boolean'],
        ]);

        // NOTE: current_rent intentionally NOT editable here — use updateRent
        // so historical rent segments are preserved.
        $unit->update($data);

        return back()->with('success', 'Unit updated.');
    }

    /**
     * Configure the monthly rent for this specific unit.
     * Never overwrites history: closes the old segment and opens a new one.
     */
    public function updateRent(Request $request, Unit $unit): RedirectResponse
    {
        Gate::authorize('updateRent', $unit);

        $data = $request->validate([
            'rent_amount' => ['required', 'numeric', 'min:0'],
            'effective_from' => ['required', 'date'],
            'reason' => ['nullable', 'string', 'max:190'],
        ]);

        $this->rents->updateUnitRent(
            $unit,
            (float) $data['rent_amount'],
            $data['effective_from'],
            $data['reason'] ?? null,
        );

        return back()->with('success', 'Rent updated with history preserved.');
    }

    public function rentHistory(Unit $unit): View
    {
        Gate::authorize('view', $unit);

        return view('units.rent-history', [
            'unit' => $unit,
            'histories' => $unit->rentHistories()->with('createdBy')->get(),
        ]);
    }

    /**
     * Soft delete only — units with financial history remain traceable.
     */
    public function destroy(Unit $unit): RedirectResponse
    {
        Gate::authorize('delete', $unit);

        $unit->delete();

        return redirect()->back()->with('success', 'Unit archived (soft deleted). Historical records remain intact.');
    }
}
