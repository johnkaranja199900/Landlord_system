<x-layout title="Allocate Tenant">
    <h1>Allocate Tenant to Unit</h1>
    <div class="card" style="max-width:640px;">
        <p class="muted">Server-side checks before allocation: unit exists · belongs to your landlord record · currently vacant/reserved · no conflicting active tenancy (unless shared occupancy is explicitly enabled).</p>
        <form method="POST" action="{{ route('tenancies.store') }}">
            @csrf
            <label>Tenant</label>
            <select name="tenant_id" required>@foreach($tenants as $t)<option value="{{ $t->id }}">{{ $t->full_name }} ({{ $t->account_number }})</option>@endforeach</select>
            <label>Unit (available only)</label>
            <select name="unit_id" required>@foreach($units as $u)<option value="{{ $u->id }}">{{ $u->block->property->name }} / {{ $u->block->name }} / {{ $u->unit_number }} — KES {{ number_format($u->current_rent,2) }}</option>@endforeach</select>
            <div class="grid">
                <div><label>Allocation / Start Date</label><input type="date" name="start_date" value="{{ now()->toDateString() }}" required></div>
                <div><label>Expected Move-in</label><input type="date" name="expected_move_in_date"></div>
                <div><label>Expected End</label><input type="date" name="expected_end_date"></div>
                <div><label>Deposit Paid (KES)</label><input type="number" step="0.01" min="0" name="deposit_paid" value="0" required></div>
                <div><label>Rent Due Day (1–28)</label><input type="number" min="1" max="28" name="due_day" value="5" required></div>
            </div>
            <button class="btn">Allocate & Generate Opening Records</button>
        </form>
    </div>
</x-layout>
