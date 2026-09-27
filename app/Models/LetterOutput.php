<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LetterOutput extends Model
{
    use HasFactory;

    protected $fillable = [
        'letter_request_id',
        'document_number',
        'file_path',
        'generated_body',
        'generated_by',
        'generated_at',
    ];

    protected $casts = [
        'generated_at' => 'datetime',
    ];

    public function letterRequest()
    {
        return $this->belongsTo(LetterRequest::class);
    }

    public function generatedBy()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }
}
