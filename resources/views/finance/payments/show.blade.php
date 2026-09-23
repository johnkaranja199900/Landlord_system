<x-layout title="Payment">
    <h1>Payment — KES {{ number_format($payment->amount,2) }}</h1>
    <div class="card">
        <table>
            <tr><th>Tenant</th><td>{{ optional($payment->tenant)->full_name }}</td><th>Method</th><td>{{ $payment->payment_method }}</td></tr>
            <tr><th>Date</th><td>{{ $payment->payment_date->format('Y-m-d') }}</td><th>Status</th><td><span class="badge">{{ $payment->status }}</span></td></tr>
            <tr><th>Reference</th><td colspan="3">{{ $payment->reference ?? '—' }}</td></tr>
        </table>
    </div>
    <a class="btn" href="{{ route('payments.receipt',$payment) }}">View Receipt</a>
    @can('reverse', $payment)
    <form method="POST" action="{{ route('payments.reverse',$payment) }}" class="card" style="margin-top:1rem;">
        @csrf
        <label>Reversal reason (creates an audit ledger entry)</label>
        <div style="display:flex;gap:.5rem;"><input name="reason" required><button class="btn btn-danger">Reverse</button></div>
    </form>
    @endcan
</x-layout>
