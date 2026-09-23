<x-layout title="Invoices">
    <div style="display:flex;justify-content:space-between;">
        <h1>Rent Invoices</h1>
        @can('generate', App\Models\RentInvoice::class)<a class="btn" href="{{ route('invoices.create') }}">+ Generate Invoice</a>@endcan
    </div>
    <table>
        <tr><th>Invoice #</th><th>Tenant</th><th>Unit</th><th>Period</th><th>Rent (KES)</th><th>Total Due</th><th>Status</th></tr>
        @foreach($invoices as $i)
            <tr>
                <td><a href="{{ route('invoices.show',$i) }}">{{ $i->invoice_number }}</a></td>
                <td>{{ optional($i->tenant)->full_name }}</td>
                <td>{{ optional($i->unit)->unit_number }}</td>
                <td>{{ sprintf('%04d-%02d', $i->billing_year, $i->billing_month) }}</td>
                <td>{{ number_format($i->rent_amount,2) }}</td>
                <td>{{ number_format($i->total_due,2) }}</td>
                <td><span class="badge">{{ $i->status }}</span></td>
            </tr>
        @endforeach
    </table>
    {{ $invoices->links() }}
</x-layout>
