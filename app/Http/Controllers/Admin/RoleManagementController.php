<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use App\Support\PermissionCatalog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Super Administrator only — manages global configuration: roles, their
 * permission mappings, and explicit per-user permission grants for staff.
 */
class RoleManagementController extends Controller
{
    public function __construct()
    {
        // Server-side gate: every action requires the global role-management
        // permission (or super admin bypass). Not a Blade-only control.
        $this->middleware('permission:roles.manage');
    }

    public function index(): View
    {
        return view('admin.roles.index', [
            'roles' => Role::with('permissions')->get(),
            'modules' => PermissionCatalog::all(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'slug' => ['required', 'string', 'max:100', 'unique:roles,slug'],
            'description' => ['nullable', 'string'],
        ]);

        Role::create($data + ['is_system' => false]);

        return back()->with('success', 'Role created.');
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        $data = $request->validate([
            'permissions' => ['present', 'array'],
            'permissions.*' => ['integer', 'exists:permissions,id'],
        ]);

        $role->permissions()->sync($data['permissions']);

        return back()->with('success', "Permissions updated for {$role->name}.");
    }

    public function destroy(Role $role): RedirectResponse
    {
        if ($role->is_system) {
            return back()->with('error', 'System roles cannot be deleted.');
        }

        $role->delete();

        return back()->with('success', 'Role deleted.');
    }

    /**
     * Explicitly assign / revoke permissions for a single user
     * (Property Staff access must come from these grants).
     */
    public function userPermissions(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'permissions' => ['present', 'array'],
            'permissions.*' => ['integer', 'exists:permissions,id'],
        ]);

        $user->directPermissions()->sync($data['permissions']);

        return back()->with('success', "Explicit permissions updated for {$user->name}.");
    }

    public function assignRole(Request $request, User $user): RedirectResponse
    {
        $data = $request->validate([
            'role_id' => ['required', 'exists:roles,id'],
            'landlord_id' => ['nullable', 'exists:landlords,id'],
        ]);

        $user->assignedRoles()->syncWithoutDetaching([
            $data['role_id'] => ['landlord_id' => $data['landlord_id'] ?? $user->landlord_id],
        ]);

        return back()->with('success', 'Role assigned.');
    }

    public function removeRole(Request $request, User $user, Role $role): RedirectResponse
    {
        $user->assignedRoles()->detach($role->id);

        return back()->with('success', 'Role removed.');
    }

    public function permissions(): View
    {
        return view('admin.roles.permissions', [
            'permissions' => Permission::orderBy('module')->get()->groupBy('module'),
        ]);
    }
}
