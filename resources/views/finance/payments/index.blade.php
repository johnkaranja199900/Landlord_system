<x-layout title="Payments">
    <div style="display:flex;justify-content:space-between;">
        <h1>Payments &amp; Receipts</h1>
        @can('record', App\Models\Payment::class)<a class="btn" href="{{ route('payments.create') }}">+ Record Payment</a>@endcan
    </div>
    <table>
        <tr><th>Date</th><th>Tenant</th><th>Method</th><th>Amount (KES)</th><th>Status</th><th></th></tr>
        @foreach($payments as $p)
            <tr>
                <td>{{ $p->payment_date->format('Y-m-d') }}</td>
                <td>{{ optional($p->tenant)->full_name }}</td>
                <td>{{ str_replace('_',' ',$p->payment_method) }}</td>
                <td>{{ number_format($p->amount,2) }}</td>
                <td><span class="badge">{{ $p->status }}</span></td>
                <td><a class="btn" href="{{ route('payments.receipt',$p) }}">Receipt</a></td>
            </tr>
        @endforeach
    </table>
    {{ $payments->links() }}
</x-layout>
