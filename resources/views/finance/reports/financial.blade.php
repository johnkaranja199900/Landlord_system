<x-layout title="Financial Report">
    <h1>Financial Report</h1>
    <form method="GET" class="card" style="display:flex;gap:.5rem;">
        <input type="date" name="from" value="{{ $from->toDateString() }}">
        <input type="date" name="to" value="{{ $to->toDateString() }}">
        <button class="btn">Run</button>
    </form>
    <div class="grid">
        <div class="card"><div class="muted">Billed (KES)</div><strong>{{ number_format($billed,2) }}</strong></div>
        <div class="card"><div class="muted">Collected (KES)</div><strong>{{ number_format($collected,2) }}</strong></div>
        <div class="card"><div class="muted">Net Arrears (KES)</div><strong>{{ number_format($arrears,2) }}</strong></div>
    </div>
    <h2>Collections by Method</h2>
    <table>
        <tr><th>Method</th><th>Total (KES)</th></tr>
        @foreach($byMethod as $method => $total)
            <tr><td>{{ str_replace('_',' ',$method) }}</td><td>{{ number_format($total,2) }}</td></tr>
        @endforeach
    </table>
</x-layout>
