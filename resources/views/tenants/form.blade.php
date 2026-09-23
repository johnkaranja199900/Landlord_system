<x-layout :title="$tenant->exists ? 'Edit Tenant' : 'Register Tenant'">
    <div class="card" style="max-width:640px;">
        <h1>{{ $tenant->exists ? 'Edit' : 'Register' }} Tenant</h1>
        <p class="muted">Personal data is protected — visible only to your organisation's authorized staff.</p>
        <form method="POST" action="{{ $tenant->exists ? route('tenants.update', $tenant) : route('tenants.store') }}">
            @csrf @if($tenant->exists) @method('PUT') @endif
            <div class="grid">
                <div><label>Full Name</label><input name="full_name" value="{{ old('full_name',$tenant->full_name) }}" required></div>
                <div><label>Phone</label><input name="phone" value="{{ old('phone',$tenant->phone) }}" required></div>
                <div><label>Alternative Phone</label><input name="alternative_phone" value="{{ old('alternative_phone',$tenant->alternative_phone) }}"></div>
                <div><label>Email</label><input type="email" name="email" value="{{ old('email',$tenant->email) }}"></div>
                <div><label>National ID Ref.</label><input name="national_id_reference" value="{{ old('national_id_reference',$tenant->national_id_reference) }}"></div>
                <div><label>Account Number</label><input name="account_number" value="{{ old('account_number',$tenant->account_number) }}" required></div>
                <div><label>Emergency Contact</label><input name="emergency_contact" value="{{ old('emergency_contact',$tenant->emergency_contact) }}"></div>
                <div><label>Next of Kin</label><input name="next_of_kin" value="{{ old('next_of_kin',$tenant->next_of_kin) }}"></div>
                <div><label>Status</label><select name="status">@foreach(['active','inactive','blacklisted'] as $s)<option value="{{ $s }}" @selected(old('status',$tenant->status)==$s)>{{ $s }}</option>@endforeach</select></div>
            </div>
            <label>Address</label><input name="address" value="{{ old('address',$tenant->address) }}">
            <label>Notes</label><textarea name="notes">{{ old('notes',$tenant->notes) }}</textarea>
            <button class="btn">Save Tenant</button>
        </form>
    </div>
</x-layout>
