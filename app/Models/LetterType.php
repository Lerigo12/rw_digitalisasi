<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LetterType extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function letterRequests()
    {
        return $this->hasMany(LetterRequest::class);
    }

    public function workflows()
    {
        return $this->hasMany(LetterWorkflow::class);
    }
}
