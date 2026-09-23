<x-layout title="Landlords">
    <h1>Landlord Accounts (global)</h1>
    <div class="card">
        <form method="POST" action="{{ route('admin.landlords.store') }}">
            @csrf
            <div style="display:flex;gap:.5rem;flex-wrap:wrap;">
                <input name="name" placeholder="Full name" required>
                <input name="business_name" placeholder="Business name">
                <input name="phone" placeholder="Phone">
                <input name="email" placeholder="Email">
                <select name="status"><option>active</option><option>suspended</option><option>inactive</option></select>
                <button class="btn">Add Landlord</button>
            </div>
        </form>
    </div>
    <table>
        <tr><th>Name</th><th>Business</th><th>Properties</th><th>Tenants</th><th>Status</th></tr>
        @foreach($landlords as $l)
            <tr><td>{{ $l->name }}</td><td>{{ $l->business_name }}</td><td>{{ $l->properties_count }}</td><td>{{ $l->tenants_count }}</td><td><span class="badge">{{ $l->status }}</span></td></tr>
        @endforeach
    </table>
    {{ $landlords->links() }}
</x-layout>
