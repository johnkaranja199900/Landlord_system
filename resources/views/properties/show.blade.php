<x-layout :title="'Property '.$property->name">
    <h1>{{ $property->name }} <span class="muted">({{ $property->property_code }})</span></h1>
    <p class="muted">{{ $property->address }} — status: {{ $property->status }}@if($property->shared_occupancy_enabled) · shared occupancy enabled @endif</p>
    @can('update', $property)<a class="btn btn-secondary" href="{{ route('properties.edit', $property) }}">Edit</a>@endcan
    <h2 style="margin-top:1.5rem;">Blocks</h2>
    @can('create', App\Models\Block::class)
    <form method="POST" action="{{ route('blocks.store', $property) }}" class="card">
        @csrf
        <div style="display:flex;gap:.5rem;flex-wrap:wrap;">
            <input placeholder="Block name (e.g. Block A)" name="name" required style="flex:2">
            <input placeholder="Code (A)" name="block_code" required style="flex:1">
            <select name="status" style="flex:1"><option>active</option><option>inactive</option><option>archived</option></select>
            <button class="btn">Add Block</button>
        </div>
    </form>
    @endcan
    @foreach($property->blocks as $block)
        <div class="card">
            <strong>{{ $block->name }}</strong> ({{ $block->block_code }}) <span class="badge">{{ $block->status }}</span>
            <a class="btn" href="{{ route('units.index', $block) }}">Units ({{ $block->units->count() }})</a>
            <table>
                <tr><th>Unit</th><th>Floor</th><th>Type</th><th>Rent (KES)</th><th>Status</th></tr>
                @foreach($block->units as $unit)
                    <tr>
                        <td><a href="{{ route('units.show', $unit) }}">{{ $unit->unit_number }}</a></td>
                        <td>{{ $unit->floor }}</td><td>{{ $unit->unit_type }}</td>
                        <td>{{ number_format($unit->current_rent, 2) }}</td>
                        <td><span class="badge">{{ str_replace('_',' ', $unit->status) }}</span></td>
                    </tr>
                @endforeach
            </table>
        </div>
    @endforeach
</x-layout>
