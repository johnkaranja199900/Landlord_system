<x-layout title="Roles & Access">
    <h1>Roles &amp; Permissions (configurable)</h1>
    <p class="muted">Super Administrator area — global configuration of role-based access control.</p>
    <div class="grid">
        @foreach($roles as $role)
            <div class="card">
                <strong>{{ $role->name }}</strong> @if($role->is_system)<span class="badge">system</span>@endif
                <div class="muted">{{ $role->slug }}</div>
                <p style="margin:.5rem 0;font-size:.85rem;">{{ $role->description }}</p>
                <details>
                    <summary class="btn btn-secondary" style="cursor:pointer;">Permissions ({{ $role->permissions->count() }})</summary>
                    <form method="POST" action="{{ route('admin.roles.update', $role) }}" style="margin-top:.5rem;">
                        @csrf @method('PUT')
                        @foreach($modules as $module => $slugs)
                            <div style="margin-bottom:.4rem;"><em>{{ $module }}</em><br>
                            @foreach(\App\Models\Permission::whereIn('slug',$slugs)->get() as $perm)
                                <label style="font-weight:normal;font-size:.78rem;">
                                    <input type="checkbox" name="permissions[]" value="{{ $perm->id }}" style="width:auto"
                                        @checked($role->permissions->contains($perm->id))> {{ $perm->slug }}
                                </label><br>
                            @endforeach
                            </div>
                        @endforeach
                        <button class="btn">Save {{ $role->name }}</button>
                    </form>
                </details>
                @if(!$role->is_system)
                <form method="POST" action="{{ route('admin.roles.destroy', $role) }}" class="inline">@csrf @method('DELETE')<button class="btn btn-danger" style="margin-top:.4rem;">Delete</button></form>
                @endif
            </div>
        @endforeach
    </div>
    <div class="card">
        <h3>Create Custom Role</h3>
        <form method="POST" action="{{ route('admin.roles.store') }}">
            @csrf
            <div style="display:flex;gap:.5rem;flex-wrap:wrap;">
                <input name="name" placeholder="Role name" required>
                <input name="slug" placeholder="role-slug" required>
                <input name="description" placeholder="Description" style="flex:2">
                <button class="btn">Create</button>
            </div>
        </form>
    </div>
</x-layout>
