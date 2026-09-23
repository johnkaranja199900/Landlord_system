<x-layout :title="'Invoice '.$invoice->invoice_number">
    <h1>Invoice {{ $invoice->invoice_number }} <span class="badge">{{ $invoice->status }}</span></h1>
    <div class="card">
        <table>
            <tr><th>Tenant</th><td>{{ optional($invoice->tenant)->full_name }}</td><th>Unit</th><td>{{ optional($invoice->unit)->unit_number }}</td></tr>
            <tr><th>Period</th><td>{{ sprintf('%04d-%02d',$invoice->billing_year,$invoice->billing_month) }}</td><th>Due</th><td>{{ $invoice->due_date->format('Y-m-d') }}</td></tr>
            <tr><th>Rent (applicable at generation)</th><td>KES {{ number_format($invoice->rent_amount,2) }}</td><th>Prev. balance</th><td>KES {{ number_format($invoice->previous_balance,2) }}</td></tr>
            <tr><th>Total Due</th><td colspan="3"><strong>KES {{ number_format($invoice->total_due,2) }}</strong></td></tr>
        </table>
    </div>
    @can('cancel', $invoice)
    <form method="POST" action="{{ route('invoices.cancel', $invoice) }}" class="inline">@csrf<button class="btn btn-danger">Cancel Invoice</button></form>
    @endcan
</x-layout>
