<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Landlord;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Global landlord account management — Super Administrators only.
 */
class LandlordController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:landlords.manage');
    }

    public function index(): View
    {
        return view('admin.landlords.index', [
            'landlords' => Landlord::withCount(['properties', 'tenants'])->latest()->paginate(20),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'business_name' => ['nullable', 'string', 'max:150'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:150'],
            'status' => ['required', 'in:active,suspended,inactive'],
        ]);

        Landlord::create($data);

        return back()->with('success', 'Landlord created.');
    }

    public function update(Request $request, Landlord $landlord): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'business_name' => ['nullable', 'string', 'max:150'],
            'phone' => ['nullable', 'string', 'max:30'],
            'email' => ['nullable', 'email', 'max:150'],
            'status' => ['required', 'in:active,suspended,inactive'],
        ]);

        $landlord->update($data);

        return back()->with('success', 'Landlord updated.');
    }
}
