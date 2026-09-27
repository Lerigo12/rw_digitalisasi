<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LetterApproval extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function letterRequest()
    {
        return $this->belongsTo(LetterRequest::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
