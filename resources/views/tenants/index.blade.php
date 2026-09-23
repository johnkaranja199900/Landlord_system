<x-layout title="Tenants">
    <div style="display:flex;justify-content:space-between;">
        <h1>Tenants</h1>
        @can('create', App\Models\Tenant::class)<a class="btn" href="{{ route('tenants.create') }}">+ Register Tenant</a>@endcan
    </div>
    <form method="GET" class="card"><input name="search" placeholder="Search name / phone / account number" value="{{ request('search') }}"></form>
    <table>
        <tr><th>ID</th><th>Name</th><th>Phone</th><th>Account No.</th><th>Status</th><th>Unit</th><th></th></tr>
        @foreach($tenants as $tenant)
            <tr>
                <td>{{ $tenant->id }}</td><td>{{ $tenant->full_name }}</td><td>{{ $tenant->phone }}</td>
                <td>{{ $tenant->account_number }}</td><td><span class="badge">{{ $tenant->status }}</span></td>
                <td>{{ optional(optional($tenant->tenancies->firstWhere('status','active'))->unit)->unit_number ?? '—' }}</td>
                <td><a class="btn" href="{{ route('tenants.show', $tenant) }}">View</a></td>
            </tr>
        @endforeach
    </table>
    {{ $tenants->links() }}
</x-layout>
