<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LetterRequest extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'form_data' => 'array',
    ];

    public function letterType()
    {
        return $this->belongsTo(LetterType::class);
    }

    public function resident()
    {
        return $this->belongsTo(Resident::class);
    }

    public function approvals()
    {
        return $this->hasMany(LetterApproval::class);
    }

    public function output()
    {
        return $this->hasOne(LetterOutput::class);
    }
}
