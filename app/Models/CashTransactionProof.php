<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashTransactionProof extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function cashTransaction()
    {
        return $this->belongsTo(CashTransaction::class);
    }

    public function uploadedBy()
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
