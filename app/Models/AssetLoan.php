<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetLoan extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function borrowerResident()
    {
        return $this->belongsTo(Resident::class, 'borrower_resident_id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function returnRecord()
    {
        return $this->hasOne(AssetLoanReturn::class);
    }

    public function setStatusAttribute($value)
    {
        $this->attributes['status'] = $value;
        // Additional logic if needed for status transitions
    }
}
