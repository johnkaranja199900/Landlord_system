<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Property extends Model
{
    protected $fillable = [
        'landlord_id', 'name', 'property_code', 'address',
        'description', 'status', 'shared_occupancy_enabled',
    ];

    protected $casts = ['shared_occupancy_enabled' => 'boolean'];

    public function landlord(): BelongsTo
    {
        return $this->belongsTo(Landlord::class);
    }

    public function blocks(): HasMany
    {
        return $this->hasMany(Block::class);
    }

    public function units(): HasMany
    {
        return $this->hasManyThrough(Unit::class, Block::class);
    }
}
