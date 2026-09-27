<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeeBill extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function feeType()
    {
        return $this->belongsTo(FeeType::class);
    }

    public function resident()
    {
        return $this->belongsTo(Resident::class);
    }

    public function paymentAllocations()
    {
        return $this->hasMany(PaymentAllocation::class);
    }

    public function payments()
    {
        return $this->hasManyThrough(Payment::class, PaymentAllocation::class, 'fee_bill_id', 'id', 'id', 'payment_id');
    }
}
