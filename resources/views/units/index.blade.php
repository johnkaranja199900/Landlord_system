<x-layout :title="'Units – '.$block->name">
    <h1>Units in {{ $block->name }} ({{ $block->property->name }})</h1>
    @can('create', App\Models\Unit::class)
    <details class="card"><summary><strong>+ Add Unit</strong> (each unit has its own rent)</summary>
        <form method="POST" action="{{ route('units.store', $block) }}">
            @csrf
            <div class="grid">
                <div><label>Unit Number</label><input name="unit_number" placeholder="A01" required></div>
                <div><label>Floor</label><input name="floor"></div>
                <div><label>Type</label><input name="unit_type" placeholder="1BR/Studio"></div>
                <div><label>Status</label><select name="status">@foreach(['vacant','reserved','occupied','under_maintenance','blocked','closed'] as $s)<option value="{{ $s }}">{{ str_replace('_',' ',$s) }}</option>@endforeach</select></div>
                <div><label>Monthly Rent (KES)</label><input type="number" step="0.01" min="0" name="current_rent" required></div>
                <div><label>Deposit Requirement (KES)</label><input type="number" step="0.01" min="0" name="deposit_requirement"></div>
            </div>
            <label>Description</label><input name="description">
            <label><input type="checkbox" name="shared_occupancy_allowed" value="1" style="width:auto"> Shared occupancy allowed for this unit</label>
            <br><button class="btn">Create Unit</button>
        </form>
    </details>
    @endcan
    <table>
        <tr><th>Unit ID</th><th>Number</th><th>Floor</th><th>Type</th><th>Rent (KES)</th><th>Deposit</th><th>Status</th><th></th></tr>
        @foreach($units as $unit)
            <tr>
                <td>{{ $unit->id }}</td>
                <td><a href="{{ route('units.show', $unit) }}">{{ $unit->unit_number }}</a></td>
                <td>{{ $unit->floor }}</td><td>{{ $unit->unit_type }}</td>
                <td>{{ number_format($unit->current_rent, 2) }}</td>
                <td>{{ number_format($unit->deposit_requirement, 2) }}</td>
                <td><span class="badge">{{ str_replace('_',' ',$unit->status) }}</span></td>
                <td><a class="btn btn-secondary" href="{{ route('units.rent-history', $unit) }}">Rent History</a></td>
            </tr>
        @endforeach
    </table>
    {{ $units->links() }}
</x-layout>
