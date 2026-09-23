<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Landlord extends Model
{
    protected $fillable = ['name', 'business_name', 'phone', 'email', 'status', 'notes'];

    public function properties(): HasMany
    {
        return $this->hasMany(Property::class);
    }

    public function tenants(): HasMany
    {
        return $this->hasMany(Tenant::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
