<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashAccount extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function rw()
    {
        return $this->belongsTo(Rw::class);
    }

    public function rt()
    {
        return $this->belongsTo(Rt::class);
    }

    public function transactions()
    {
        return $this->hasMany(CashTransaction::class);
    }

    public function sourceTransfers()
    {
        return $this->hasMany(CashTransfer::class, 'source_account_id');
    }

    public function destinationTransfers()
    {
        return $this->hasMany(CashTransfer::class, 'destination_account_id');
    }
}
