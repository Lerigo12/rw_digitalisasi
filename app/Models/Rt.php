<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rt extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function rw()
    {
        return $this->belongsTo(Rw::class);
    }

    public function families()
    {
        return $this->hasMany(Family::class);
    }

    public function cashAccounts()
    {
        return $this->hasMany(CashAccount::class);
    }

    public function assets()
    {
        return $this->hasMany(Asset::class);
    }
}
