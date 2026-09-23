<x-layout title="Dashboard">
    <h1>Dashboard</h1>
    <div class="grid">
        <div class="card"><div class="muted">Properties</div><strong>{{ $properties }}</strong></div>
        <div class="card"><div class="muted">Units</div><strong>{{ $units }}</strong></div>
        <div class="card"><div class="muted">Occupied Units</div><strong>{{ $occupiedUnits }}</strong></div>
        <div class="card"><div class="muted">Tenants</div><strong>{{ $tenants }}</strong></div>
        <div class="card"><div class="muted">Active Tenancies</div><strong>{{ $activeTenancies }}</strong></div>
        <div class="card"><div class="muted">Collected This Month (KES)</div><strong>{{ number_format($collectedThisMonth, 2) }}</strong></div>
    </div>
</x-layout>
