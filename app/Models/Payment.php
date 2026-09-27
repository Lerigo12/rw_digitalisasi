<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function payerResident()
    {
        return $this->belongsTo(Resident::class, 'payer_resident_id');
    }

    public function allocations()
    {
        return $this->hasMany(PaymentAllocation::class);
    }

    public function proofs()
    {
        return $this->hasMany(PaymentProof::class);
    }

    public function verification()
    {
        return $this->hasOne(PaymentVerification::class);
    }
}
