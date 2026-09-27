<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;

class Family extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function rt()
    {
        return $this->belongsTo(Rt::class);
    }

    public function headResident()
    {
        return $this->belongsTo(Resident::class, 'head_resident_id');
    }

    public function residents()
    {
        return $this->hasMany(Resident::class);
    }

    public function setFamilyNumberAttribute($value)
    {
        $this->attributes['family_number_encrypted'] = Crypt::encryptString($value);
        $this->attributes['family_number_hash'] = hash('sha256', $value);
    }

    public function getFamilyNumberAttribute()
    {
        try {
            return Crypt::decryptString($this->attributes['family_number_encrypted']);
        } catch (\Exception $e) {
            return null;
        }
    }
}
