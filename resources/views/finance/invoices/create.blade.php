<x-layout title="Generate Invoice">
    <h1>Generate Monthly Rent Invoice</h1>
    <div class="card" style="max-width:560px;">
        <p class="muted">The invoice amount is resolved from the unit's rent history for the selected billing month — historical values are never overwritten.</p>
        <form method="POST" action="{{ route('invoices.store') }}">
            @csrf
            <label>Active Tenancy</label>
            <select name="tenancy_id" required>@foreach($tenancies as $t)<option value="{{ $t->id }}">{{ $t->tenant->full_name }} — {{ optional($t->unit)->unit_number }} (KES {{ number_format($t->monthly_rent,2) }})</option>@endforeach</select>
            <div style="display:flex;gap:.5rem;">
                <div style="flex:1"><label>Billing Year</label><input type="number" name="billing_year" value="{{ now()->year }}" required></div>
                <div style="flex:1"><label>Billing Month</label><input type="number" name="billing_month" min="1" max="12" value="{{ now()->month }}" required></div>
            </div>
            <button class="btn">Generate</button>
        </form>
    </div>
</x-layout>
