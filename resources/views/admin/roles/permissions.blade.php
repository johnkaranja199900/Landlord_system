<x-layout title="Permission Catalog">
    <h1>Permission Catalog</h1>
    @foreach($permissions as $module => $perms)
        <div class="card">
            <h3>{{ ucfirst($module) }}</h3>
            <table><tr><th>Slug</th><th>Name</th></tr>
            @foreach($perms as $p)<tr><td>{{ $p->slug }}</td><td>{{ $p->name }}</td></tr>@endforeach
            </table>
        </div>
    @endforeach
</x-layout>
