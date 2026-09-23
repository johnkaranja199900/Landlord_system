<x-layout :title="$property->exists ? 'Edit Property' : 'New Property'">
    <div class="card" style="max-width:560px;">
        <h1>{{ $property->exists ? 'Edit' : 'New' }} Property</h1>
        <form method="POST" action="{{ $property->exists ? route('properties.update', $property) : route('properties.store') }}">
            @csrf @if($property->exists) @method('PUT') @endif
            @if(!$property->exists && isset($landlords))
                <label>Landlord</label>
                <select name="landlord_id">@foreach($landlords as $l)<option value="{{ $l?->id }}">{{ $l->name ?? '—' }}</option>@endforeach</select>
            @endif
            <label>Name</label><input name="name" value="{{ old('name', $property->name) }}" required>
            <label>Property Code</label><input name="property_code" value="{{ old('property_code', $property->property_code) }}" required>
            <label>Address</label><input name="address" value="{{ old('address', $property->address) }}">
            <label>Description</label><textarea name="description">{{ old('description', $property->description) }}</textarea>
            <label>Status</label>
            <select name="status">@foreach(['active','inactive','archived'] as $s)<option value="{{ $s }}" @selected(old('status',$property->status)==$s)>{{ $s }}</option>@endforeach</select>
            <label><input type="checkbox" name="shared_occupancy_enabled" value="1" style="width:auto" @checked($property->shared_occupancy_enabled)> Allow shared occupancy in this property</label>
            <br><button class="btn">Save</button>
        </form>
    </div>
</x-layout>
