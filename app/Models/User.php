<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'landlord_id',
        'is_super_admin',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_super_admin' => 'boolean',
        ];
    }

    public function landlord(): BelongsTo
    {
        return $this->belongsTo(Landlord::class);
    }

    public function assignedRoles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'assigned_roles')
            ->withPivot('landlord_id');
    }

    /**
     * Explicit per-user permission grants (used to restrict staff access).
     */
    public function directPermissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'permission_grants')
            ->withPivot('landlord_id');
    }

    public function hasRole(string ...$slugs): bool
    {
        if ($this->is_super_admin && in_array(Role::SUPER_ADMIN, $slugs, true)) {
            return true;
        }

        return $this->assignedRoles->whereIn('slug', $slugs)->isNotEmpty();
    }

    /**
     * Server-side permission check. This is the single source of truth for
     * authorization — Blade visibility alone is never relied upon.
     */
    public function hasPermission(string $slug): bool
    {
        if ($this->is_super_admin) {
            return true; // Super Administrator manages global configuration.
        }

        // Role-derived permissions.
        $roleIds = $this->assignedRoles->modelKeys();
        $viaRole = Permission::query()
            ->where('slug', $slug)
            ->whereHas('roles', fn ($q) => $q->whereIn('roles.id', $roleIds))
            ->exists();

        if ($viaRole) {
            return true;
        }

        // Explicit per-user grants (staff restricted to assigned permissions).
        return $this->directPermissions()->where('slug', $slug)->exists();
    }

    public function allPermissionSlugs(): array
    {
        if ($this->is_super_admin) {
            return Permission::pluck('slug')->all();
        }

        $roleIds = $this->assignedRoles->modelKeys();

        $fromRoles = Permission::query()
            ->whereHas('roles', fn ($q) => $q->whereIn('roles.id', $roleIds))
            ->pluck('slug');

        $fromGrants = $this->directPermissions()->pluck('slug');

        return $fromRoles->merge($fromGrants)->unique()->values()->all();
    }

    /**
     * The id of the landlord this user acts on behalf of
     * (self for landlords, employer for staff/managers/accountants).
     */
    public function actingLandlordId(): ?int
    {
        return $this->landlord_id;
    }
}
