<?php

namespace App\Http\Controllers\Property;

use Illuminate\Support\Facades\Gate;
use App\Http\Controllers\Controller;
use App\Models\Landlord;
use App\Models\Property;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PropertyController extends Controller
{
    public function __construct()
    {
        // Route-level authorization middleware in addition to policies below.
        $this->middleware('permission:properties.view')->only(['index', 'show']);
        $this->middleware('permission:properties.create')->only(['create', 'store']);
        $this->middleware('permission:properties.update')->only(['edit', 'update']);
        $this->middleware('permission:properties.delete')->only('destroy');
    }

    private function scopedQuery()
    {
        $user = request()->user();

        return $user->is_super_admin
            ? Property::query()
            : Property::where('landlord_id', $user->landlord_id);
    }

    public function index(): View
    {
        return view('properties.index', [
            'properties' => $this->scopedQuery()->with('blocks')->withCount('units')->latest()->paginate(15),
        ]);
    }

    public function create(): View
    {
        $user = request()->user();

        return view('properties.form', [
            'property' => new Property(),
            'landlords' => $user->is_super_admin ? Landlord::all() : collect([$user->landlord]),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'landlord_id' => ['required', 'exists:landlords,id'],
            'name' => ['required', 'string', 'max:150'],
            'property_code' => ['required', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:active,inactive,archived'],
            'shared_occupancy_enabled' => ['boolean'],
        ]);

        $user = $request->user();
        if (! $user->is_super_admin) {
            $data['landlord_id'] = $user->landlord_id; // never trust client input
        }

        abort_unless(
            $user->can('create', Property::class),
            403
        );

        Property::create($data);

        return redirect()->route('properties.index')->with('success', 'Property created.');
    }

    public function show(Property $property): View
    {
        Gate::authorize('view', $property);

        return view('properties.show', [
            'property' => $property->load('blocks.units.rentHistories'),
        ]);
    }

    public function edit(Property $property): View
    {
        Gate::authorize('update', $property);

        return view('properties.form', ['property' => $property]);
    }

    public function update(Request $request, Property $property): RedirectResponse
    {
        Gate::authorize('update', $property);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'property_code' => ['required', 'string', 'max:30'],
            'address' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:active,inactive,archived'],
            'shared_occupancy_enabled' => ['boolean'],
        ]);

        $property->update($data);

        return back()->with('success', 'Property updated.');
    }

    public function destroy(Property $property): RedirectResponse
    {
        Gate::authorize('delete', $property);

        $property->delete();

        return redirect()->route('properties.index')->with('success', 'Property deleted.');
    }
}
