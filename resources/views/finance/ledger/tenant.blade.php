<x-layout title="Tenant Ledger">
    <h1>Ledger — {{ $tenancy->tenant->full_name }} (Unit {{ optional($tenancy->unit)->unit_number }})</h1>
    <p><strong>Balance due: KES {{ number_format($balance,2) }}</strong></p>
    <table>
        <tr><th>Date</th><th>Type</th><th>Debit</th><th>Credit</th><th>Reference</th><th>Description</th></tr>
        @foreach($entries as $e)
            <tr><td>{{ $e->transaction_date->format('Y-m-d') }}</td><td>{{ $e->transaction_type }}</td><td>{{ number_format($e->debit,2) }}</td><td>{{ number_format($e->credit,2) }}</td><td>{{ $e->reference }}</td><td>{{ $e->description }}</td></tr>
        @endforeach
    </table>
    @can('adjust', App\Models\RentLedger::class)
    <div class="card">
        <h3>Manual Adjustment (creates immutable journal entry)</h3>
        <form method="POST" action="{{ route('ledger.adjust', $tenancy) }}">
            @csrf
            <div style="display:flex;gap:.5rem;flex-wrap:wrap;">
                <select name="transaction_type"><option value="adjustment">Adjustment</option><option value="credit">Credit</option><option value="waiver">Waiver</option></select>
                <input type="number" step="0.01" min="0.01" name="amount" placeholder="Amount" required>
                <select name="direction"><option value="debit">Debit (charge)</option><option value="credit">Credit (reduce)</option></select>
                <input type="date" name="transaction_date" value="{{ now()->toDateString() }}" required>
                <input name="description" placeholder="Reason" required style="flex:2">
                <button class="btn">Post</button>
            </div>
        </form>
    </div>
    @endcan
</x-layout>
