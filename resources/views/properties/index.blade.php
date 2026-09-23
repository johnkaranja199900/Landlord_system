<x-layout title="Properties">
    <div style="display:flex;justify-content:space-between;align-items:center;">
        <h1>Properties</h1>
        @can('create', App\Models\Property::class)<a class="btn" href="{{ route('properties.create') }}">+ New Property</a>@endcan
    </div>
    <table>
        <tr><th>Name</th><th>Code</th><th>Blocks</th><th>Units</th><th>Status</th><th></th></tr>
        @forelse($properties as $property)
            <tr>
                <td>{{ $property->name }}</td>
                <td>{{ $property->property_code }}</td>
                <td>{{ $property->blocks->count() }}</td>
                <td>{{ $property->units_count }}</td>
                <td><span class="badge">{{ $property->status }}</span></td>
                <td>
                    <a class="btn" href="{{ route('properties.show', $property) }}">Open</a>
                    <a class="btn btn-secondary" href="{{ route('blocks.index', $property) }}">Blocks</a>
                </td>
            </tr>
        @empty
            <tr><td colspan="6">No properties yet.</td></tr>
        @endforelse
    </table>
    {{ $properties->links() }}
</x-layout>
