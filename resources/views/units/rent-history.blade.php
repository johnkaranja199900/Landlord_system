<x-layout title="Rent History">
    <h1>Rent History — Unit {{ $unit->unit_number }}</h1>
    <table>
        <tr><th>From</th><th>To</th><th>Rent (KES)</th><th>Reason</th><th>Changed By</th></tr>
        @foreach($histories as $h)
            <tr>
                <td>{{ $h->effective_from->format('Y-m-d') }}</td>
                <td>{{ $h->effective_to?->format('Y-m-d') ?? 'open (current)' }}</td>
                <td>{{ number_format($h->rent_amount,2) }}</td>
                <td>{{ $h->reason }}</td>
                <td>{{ $h->createdBy?->name ?? 'system' }}</td>
            </tr>
        @endforeach
    </table>
    <a class="btn btn-secondary" href="{{ route('units.show', $unit) }}">Back to unit</a>
</x-layout>
