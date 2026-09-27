<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    protected $guarded = [];

    protected $casts = [
        'data' => 'array',
        'read_at' => 'datetime',
    ];

    public $incrementing = false;

    protected $keyType = 'string';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function unread()
    {
        return is_null($this->read_at);
    }

    public function markAsRead()
    {
        if (is_null($this->read_at)) {
            $this->forceFill(['read_at' => $this->freshTimestamp()])->save();
        }
    }
}
