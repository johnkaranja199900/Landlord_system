<x-layout title="Blocks">
    <h1>{{ $property->name }} — Blocks</h1>
    @can('create', App\Models\Block::class)
    <form method="POST" action="{{ route('blocks.store', $property) }}" class="card">@csrf
        <div style="display:flex;gap:.5rem;">
            <input name="name" placeholder="Block name" required>
            <input name="block_code" placeholder="Code" required>
            <select name="status"><option>active</option><option>inactive</option><option>archived</option></select>
            <button class="btn">Add</button>
        </div>
    </form>
    @endcan
    <table>
        <tr><th>Block</th><th>Code</th><th>Units</th><th>Status</th><th></th></tr>
        @foreach($blocks as $block)
            <tr>
                <td>{{ $block->name }}</td><td>{{ $block->block_code }}</td><td>{{ $block->units_count }}</td>
                <td><span class="badge">{{ $block->status }}</span></td>
                <td><a class="btn" href="{{ route('units.index', $block) }}">Manage Units</a></td>
            </tr>
        @endforeach
    </table>
</x-layout>
