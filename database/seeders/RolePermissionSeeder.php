<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Support\PermissionCatalog;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * Seeds the configurable permission catalog and maps default
     * role -> permission assignments (adjustable later via the admin UI).
     */
    public function run(): void
    {
        foreach (PermissionCatalog::all() as $module => $slugs) {
            foreach ($slugs as $slug) {
                Permission::updateOrCreate(
                    ['slug' => $slug],
                    ['name' => ucwords(str_replace(['.', '-'], ' ', $slug)), 'module' => $module],
                );
            }
        }

        foreach (PermissionCatalog::defaultRolePermissions() as $roleSlug => $permissions) {
            $role = Role::where('slug', $roleSlug)->first();

            if (! $role) {
                continue;
            }

            $role->permissions()->sync(
                Permission::whereIn('slug', $permissions)->pluck('id')
            );
        }
    }
}
