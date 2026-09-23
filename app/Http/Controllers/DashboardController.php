<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Property;
use App\Models\Tenant;
use App\Models\Tenancy;
use App\Models\Unit;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = request()->user();

        $scope = fn ($model) => $user->is_super_admin
            ? $model::query()
            : match (true) {
                $model === Property::class => Property::where('landlord_id', $user->landlord_id),
                $model === Tenant::class => Tenant::where('landlord_id', $user->landlord_id),
                default => $model::query(),
            };

        return view('dashboard', [
            'properties' => $scope(Property::class)->count(),
            'units' => Unit::when(! $user->is_super_admin, fn ($q) => $q->whereHas('block.property', fn ($p) => $p->where('landlord_id', $user->landlord_id)))->count(),
            'occupiedUnits' => Unit::where('status', 'occupied')
                ->when(! $user->is_super_admin, fn ($q) => $q->whereHas('block.property', fn ($p) => $p->where('landlord_id', $user->landlord_id)))->count(),
            'tenants' => $scope(Tenant::class)->count(),
            'activeTenancies' => Tenancy::where('status', 'active')
                ->when(! $user->is_super_admin, fn ($q) => $q->whereHas('unit.block.property', fn ($p) => $p->where('landlord_id', $user->landlord_id)))
                ->count(),
            'collectedThisMonth' => Payment::where('status', 'confirmed')
                ->when(! $user->is_super_admin, fn ($q) => $q->where('landlord_id', $user->landlord_id))
                ->whereMonth('payment_date', now()->month)
                ->whereYear('payment_date', now()->year)
                ->sum('amount'),
        ]);
    }
}
