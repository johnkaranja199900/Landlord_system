<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tenant extends Model
{
    protected $fillable = [
        'landlord_id', 'full_name', 'phone', 'alternative_phone', 'email',
        'national_id_reference', 'address', 'emergency_contact', 'next_of_kin',
        'account_number', 'status', 'registered_date', 'notes',
    ];

    protected $casts = ['registered_date' => 'date'];

    /**
     * Sensitive personal information is hidden from serialization by default;
     * it is only ever exposed through authorized controllers/policies.
     */
    protected $hidden = ['national_id_reference'];

    public function landlord(): BelongsTo
    {
        return $this->belongsTo(Landlord::class);
    }

    public function tenancies(): HasMany
    {
        return $this->hasMany(Tenancy::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(RentInvoice::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
