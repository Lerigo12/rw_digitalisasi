<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnnouncementTarget extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function announcement()
    {
        return $this->belongsTo(Announcement::class);
    }
}
