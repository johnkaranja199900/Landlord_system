<x-layout :title="'Tenancy #'.$tenancy->id">
    <h1>Tenancy — {{ $tenancy->tenant->full_name }} → Unit {{ optional($tenancy->unit)->unit_number }}</h1>
    <div class="card">
        <table>
            <tr><th>Allocated</th><td>{{ $tenancy->allocation_date?->format('Y-m-d') }}</td><th>Move-in expected</th><td>{{ $tenancy->expected_move_in_date?->format('Y-m-d') ?? '—' }}</td></tr>
            <tr><th>Start</th><td>{{ $tenancy->start_date->format('Y-m-d') }}</td><th>Status</th><td><span class="badge">{{ $tenancy->status }}</span></td></tr>
            <tr><th>Monthly Rent (snapshot)</th><td>KES {{ number_format($tenancy->monthly_rent,2) }}</td><th>Deposit req/paid</th><td>KES {{ number_format($tenancy->deposit_required,2) }} / {{ number_format($tenancy->deposit_paid,2) }}</td></tr>
        </table>
    </div>
    @can('terminate', $tenancy)
    <div class="card">
        <h3>Move-out / Vacating Process</h3>
        <p class="muted">Closes the tenancy but keeps the tenant record. The unit becomes vacant only after vacating completes.</p>
        <form method="POST" action="{{ route('tenancies.terminate', $tenancy) }}">
            @csrf
            <div style="display:flex;gap:.5rem;">
                <input type="date" name="actual_end_date" value="{{ now()->toDateString() }}" required>
                <select name="final_status"><option value="terminated">Terminated</option><option value="evicted">Evicted</option><option value="expired">Expired</option></select>
                <button class="btn btn-danger">Complete Vacating</button>
            </div>
        </form>
    </div>
    @endcan
    <h2>Rent Ledger</h2>
    <table>
        <tr><th>Date</th><th>Type</th><th>Debit</th><th>Credit</th><th>Reference</th><th>Description</th></tr>
        @foreach($tenancy->ledgerEntries as $e)
            <tr><td>{{ $e->transaction_date->format('Y-m-d') }}</td><td>{{ $e->transaction_type }}</td><td>{{ number_format($e->debit,2) }}</td><td>{{ number_format($e->credit,2) }}</td><td>{{ $e->reference }}</td><td>{{ $e->description }}</td></tr>
        @endforeach
    </table>
    <a class="btn btn-secondary" href="{{ route('ledger.tenant', $tenancy) }}">Open full ledger</a>
</x-layout>
