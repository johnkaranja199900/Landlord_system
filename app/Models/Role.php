<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    protected $fillable = ['name', 'slug', 'description', 'is_system'];

    protected $casts = ['is_system' => 'boolean'];

    public const SUPER_ADMIN = 'super-admin';
    public const LANDLORD = 'landlord';
    public const PROPERTY_MANAGER = 'property-manager';
    public const ACCOUNTANT = 'accountant';
    public const PROPERTY_STAFF = 'property-staff';

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_permission');
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'assigned_roles');
    }
}
