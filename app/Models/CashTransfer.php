<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashTransfer extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function sourceAccount()
    {
        return $this->belongsTo(CashAccount::class, 'source_account_id');
    }

    public function destinationAccount()
    {
        return $this->belongsTo(CashAccount::class, 'destination_account_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
