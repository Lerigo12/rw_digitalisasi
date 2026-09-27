<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashTransactionApproval extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function cashTransaction()
    {
        return $this->belongsTo(CashTransaction::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approver_id');
    }
}
