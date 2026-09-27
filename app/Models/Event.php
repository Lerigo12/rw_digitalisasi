<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function rw()
    {
        return $this->belongsTo(Rw::class);
    }

    public function rt()
    {
        return $this->belongsTo(Rt::class);
    }

    public function registrations()
    {
        return $this->hasMany(EventRegistration::class);
    }

    public function attendanceSessions()
    {
        return $this->hasMany(AttendanceSession::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}
