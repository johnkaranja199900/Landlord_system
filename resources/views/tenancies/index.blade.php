<x-layout title="Tenancies">
    <div style="display:flex;justify-content:space-between;">
        <h1>Tenancies</h1>
        @can('allocate', App\Models\Tenancy::class)<a class="btn" href="{{ route('tenancies.create') }}">+ Allocate Tenant</a>@endcan
    </div>
    <table>
        <tr><th>Tenant</th><th>Unit</th><th>Property</th><th>Rent (KES)</th><th>Start</th><th>Status</th></tr>
        @foreach($tenancies as $t)
            <tr>
                <td><a href="{{ route('tenancies.show',$t) }}">{{ $t->tenant->full_name }}</a></td>
                <td>{{ optional($t->unit)->unit_number }}</td>
                <td>{{ optional(optional(optional($t->unit)->block)->property)->name }}</td>
                <td>{{ number_format($t->monthly_rent,2) }}</td>
                <td>{{ $t->start_date?->format('Y-m-d') }}</td>
                <td><span class="badge">{{ $t->status }}</span></td>
            </tr>
        @endforeach
    </table>
    {{ $tenancies->links() }}
</x-layout>
