<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class Resident extends Model
{
    use HasFactory;

    protected $guarded = ['id'];

    protected $casts = [
        'birth_date' => 'date',
    ];

    public function family()
    {
        return $this->belongsTo(Family::class);
    }

    public function user()
    {
        return $this->hasOne(User::class);
    }

    public function feeBills()
    {
        return $this->hasMany(FeeBill::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'payer_resident_id');
    }

    public function letterRequests()
    {
        return $this->hasMany(LetterRequest::class);
    }

    public function eventRegistrations()
    {
        return $this->hasMany(EventRegistration::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function assetLoans()
    {
        return $this->hasMany(AssetLoan::class, 'borrower_resident_id');
    }

    public function setNikAttribute($value)
    {
        $this->attributes['nik_encrypted'] = Crypt::encryptString($value);
        $this->attributes['nik_hash'] = hash('sha256', $value);
    }

    public function getNikAttribute()
    {
        try {
            return Crypt::decryptString($this->attributes['nik_encrypted']);
        } catch (\Exception $e) {
            return null;
        }
    }
}
