<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rw extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function rts()
    {
        return $this->hasMany(Rt::class);
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
