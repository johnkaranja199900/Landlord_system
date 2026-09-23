<x-layout :title="'Tenant '.$tenant->full_name">
    <h1>{{ $tenant->full_name }} <span class="badge">{{ $tenant->status }}</span></h1>
    <div class="card">
        <table>
            <tr><th>Tenant ID</th><td>{{ $tenant->id }}</td><th>Account No.</th><td>{{ $tenant->account_number }}</td></tr>
            <tr><th>Phone</th><td>{{ $tenant->phone }}</td><th>Alt. Phone</th><td>{{ $tenant->alternative_phone ?? '—' }}</td></tr>
            <tr><th>Email</th><td>{{ $tenant->email ?? '—' }}</td><th>Registered</th><td>{{ $tenant->registered_date?->format('Y-m-d') }}</td></tr>
            <tr><th>National ID</th><td>{{ $tenant->national_id_reference ?? '—' }}</td><th>Next of Kin</th><td>{{ $tenant->next_of_kin ?? '—' }}</td></tr>
            <tr><th>Address</th><td colspan="3">{{ $tenant->address ?? '—' }}</td></tr>
            <tr><th>Emergency</th><td colspan="3">{{ $tenant->emergency_contact ?? '—' }}</td></tr>
        </table>
    </div>
    @can('update', $tenant)<a class="btn btn-secondary" href="{{ route('tenants.edit', $tenant) }}">Edit</a>@endcan
    @can('allocate', App\Models\Tenancy::class)<a class="btn" href="{{ route('tenancies.create') }}">Allocate to Unit</a>@endcan
    <h2>Tenancy History</h2>
    <table>
        <tr><th>Unit</th><th>Property</th><th>Start</th><th>End</th><th>Rent (KES)</th><th>Status</th></tr>
        @foreach($tenant->tenancies as $t)
            <tr>
                <td><a href="{{ route('tenancies.show',$t) }}">{{ optional($t->unit)->unit_number }}</a></td>
                <td>{{ optional(optional(optional($t->unit)->block)->property)->name }}</td>
                <td>{{ $t->start_date?->format('Y-m-d') }}</td>
                <td>{{ $t->actual_end_date?->format('Y-m-d') ?? '—' }}</td>
                <td>{{ number_format($t->monthly_rent,2) }}</td>
                <td><span class="badge">{{ $t->status }}</span></td>
            </tr>
        @endforeach
    </table>
</x-layout>
