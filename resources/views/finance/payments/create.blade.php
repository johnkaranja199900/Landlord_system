<x-layout title="Record Payment">
    <h1>Record Payment</h1>
    <div class="card" style="max-width:560px;">
        <form method="POST" action="{{ route('payments.store') }}">
            @csrf
            <label>Tenancy</label>
            <select name="tenancy_id" required>@foreach($tenancies as $t)<option value="{{ $t->id }}">{{ $t->tenant->full_name }} — {{ optional($t->unit)->unit_number }}</option>@endforeach</select>
            <div style="display:flex;gap:.5rem;">
                <div style="flex:1"><label>Amount (KES)</label><input type="number" step="0.01" min="0.01" name="amount" required></div>
                <div style="flex:1"><label>Date</label><input type="date" name="payment_date" value="{{ now()->toDateString() }}" required></div>
            </div>
            <label>Method</label>
            <select name="payment_method">@foreach(['mpesa','cash','bank_transfer','cheque','other'] as $m)<option value="{{ $m }}">{{ str_replace('_',' ',ucfirst($m)) }}</option>@endforeach</select>
            <label>Reference (e.g. M-PESA code)</label><input name="reference">
            <label>Notes</label><textarea name="notes"></textarea>
            <button class="btn">Record & Issue Receipt</button>
        </form>
    </div>
</x-layout>
