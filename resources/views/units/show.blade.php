<x-layout :title="'Unit '.$unit->unit_number">
    <h1>Unit {{ $unit->unit_number }} <span class="muted">— {{ $unit->block->name }}, {{ $unit->block->property->name }}</span></h1>
    <div class="card">
        <table>
            <tr><th>Floor</th><td>{{ $unit->floor }}</td><th>Type</th><td>{{ $unit->unit_type }}</td></tr>
            <tr><th>Status</th><td><span class="badge">{{ str_replace('_',' ',$unit->status) }}</span></td><th>Occupancy</th><td>{{ $unit->tenancies->whereIn('status',['active','terminated_pending_vacating'])->count() ? 'Occupied' : 'Vacant' }}</td></tr>
            <tr><th>Current Rent</th><td>KES {{ number_format($unit->current_rent,2) }}</td><th>Deposit Required</th><td>KES {{ number_format($unit->deposit_requirement,2) }}</td></tr>
            <tr><th>Meter Info</th><td colspan="3">{{ $unit->meter_info ? json_encode($unit->meter_info) : '—' }}</td></tr>
            <tr><th>Utilities</th><td colspan="3">{{ $unit->utility_config ? json_encode($unit->utility_config) : '—' }}</td></tr>
        </table>
    </div>

    @can('updateRent', $unit)
    <div class="card">
        <h3>Configure Monthly Rent (history preserved)</h3>
        <form method="POST" action="{{ route('units.rent', $unit) }}">
            @csrf @method('PATCH')
            <div style="display:flex;gap:.5rem;flex-wrap:wrap;">
                <input type="number" step="0.01" min="0" name="rent_amount" placeholder="New rent KES" required style="flex:1">
                <input type="date" name="effective_from" value="{{ now()->toDateString() }}" required style="flex:1">
                <input name="reason" placeholder="Reason (e.g. annual review)" style="flex:2">
                <button class="btn">Apply</button>
            </div>
            <p class="muted">Example: A01 Jan–Jun = 10,000 · Jul onward = 12,000. Past invoices keep the amount applicable when generated.</p>
        </form>
    </div>
    @endcan

    <h2>Tenancy History</h2>
    <table>
        <tr><th>Tenant</th><th>Start</th><th>End</th><th>Rent at allocation</th><th>Status</th></tr>
        @foreach($unit->tenancies as $t)
            <tr><td>{{ $t->tenant->full_name }}</td><td>{{ $t->start_date?->format('Y-m-d') }}</td><td>{{ $t->actual_end_date?->format('Y-m-d') ?? '—' }}</td><td>{{ number_format($t->monthly_rent,2) }}</td><td><span class="badge">{{ $t->status }}</span></td></tr>
        @endforeach
    </table>
    <a class="btn btn-secondary" href="{{ route('units.rent-history', $unit) }}">Full Rent History</a>
</x-layout>
