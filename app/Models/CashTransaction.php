<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashTransaction extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function cashAccount()
    {
        return $this->belongsTo(CashAccount::class);
    }

    public function category()
    {
        return $this->belongsTo(CashCategory::class, 'category_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function approvals()
    {
        return $this->hasMany(CashTransactionApproval::class);
    }

    public function proofs()
    {
        return $this->hasMany(CashTransactionProof::class);
    }
}
