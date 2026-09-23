<x-layout title="Receipt">
    <div class="card" style="max-width:480px;margin:2rem auto;text-align:center;">
        <h1>OFFICIAL RECEIPT</h1>
        <p class="muted">{{ optional(optional($payment->tenancy)->unit)->block->property->name ?? '' }}</p>
        <hr style="margin:1rem 0;">
        <p><strong>Receipt No:</strong> RCP-{{ str_pad($payment->id,6,'0',STR_PAD_LEFT) }}</p>
        <p><strong>Received from:</strong> {{ $payment->tenant->full_name }}</p>
        <p><strong>Amount:</strong> KES {{ number_format($payment->amount,2) }}</p>
        <p><strong>Via:</strong> {{ str_replace('_',' ',$payment->payment_method) }} @if($payment->reference) ({{ $payment->reference }}) @endif</p>
        <p><strong>Date:</strong> {{ $payment->payment_date->format('Y-m-d') }}</p>
        <p><strong>Status:</strong> {{ strtoupper($payment->status) }}</p>
        <br><a class="btn btn-secondary" onclick="window.print()">Print</a>
    </div>
</x-layout>
