<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeeType extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'allowed_payment_methods' => 'array',
    ];

    public function feeBills()
    {
        return $this->hasMany(FeeBill::class);
    }
}
