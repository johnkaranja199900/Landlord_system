<x-layout title="Rent Ledger">
    <h1>Rent Ledger</h1>
    <table>
        <tr><th>Date</th><th>Tenant</th><th>Unit</th><th>Type</th><th>Debit</th><th>Credit</th><th>Reference</th></tr>
        @foreach($entries as $e)
            <tr>
                <td>{{ $e->transaction_date->format('Y-m-d') }}</td>
                <td>{{ optional($e->tenant)->full_name }}</td>
                <td>{{ optional($e->unit)->unit_number }}</td>
                <td><span class="badge">{{ $e->transaction_type }}</span></td>
                <td>{{ number_format($e->debit,2) }}</td>
                <td>{{ number_format($e->credit,2) }}</td>
                <td>{{ $e->reference }}</td>
            </tr>
        @endforeach
    </table>
    {{ $entries->links() }}
</x-layout>
