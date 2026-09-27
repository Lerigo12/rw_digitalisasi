<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Notifications\Dispatcher;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $guarded = [];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'last_login_at' => 'datetime',
        ];
    }

    public function resident()
    {
        return $this->belongsTo(Resident::class);
    }

    public function rw()
    {
        return $this->belongsTo(Rw::class);
    }

    public function rt()
    {
        return $this->belongsTo(Rt::class);
    }

    public function roles()
    {
        return $this->belongsToMany(Role::class, 'user_roles')
            ->withPivot(['scope_type', 'scope_id'])
            ->withTimestamps();
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    public function unreadNotifications()
    {
        return $this->hasMany(Notification::class)->whereNull('read_at');
    }

    public function routeNotificationForDatabase($notification)
    {
        return $this->notifications();
    }

    public function notify($instance)
    {
        if (method_exists($instance, 'toDatabase')) {
            $data = $instance->toDatabase($this);

            return Notification::create([
                'id' => (string) Str::uuid(),
                'user_id' => $this->id,
                'type' => get_class($instance),
                'title' => $data['title'] ?? 'Notifikasi',
                'body' => $data['body'] ?? $data['message'] ?? '',
                'data' => $data,
            ]);
        }

        app(Dispatcher::class)->send($this, $instance);
    }

    public function hasRole(string $roleSlug, ?string $scopeType = null, ?int $scopeId = null): bool
    {
        foreach ($this->roles as $role) {
            if ($role->slug === $roleSlug) {
                if ($scopeType === null) {
                    return true;
                }
                if ($role->pivot->scope_type === 'global' || ($role->pivot->scope_type === $scopeType && $role->pivot->scope_id === $scopeId)) {
                    return true;
                }
            }
        }

        return false;
    }
}
