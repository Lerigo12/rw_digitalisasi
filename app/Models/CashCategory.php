<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashCategory extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function transactions()
    {
        return $this->hasMany(CashTransaction::class, 'category_id');
    }
}
