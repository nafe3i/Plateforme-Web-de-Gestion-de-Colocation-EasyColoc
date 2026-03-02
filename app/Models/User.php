<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasRoles;
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'is_banned',
        'reputation',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts()
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function memberships()
    {
        return $this->hasMany(Membership::class);
    }

    public function colocations()
    {
        return $this->belongsToMany(Colocation::class, 'memberships')
            ->withPivot('role', 'balance', 'manual_adjustment', 'joined_at', 'left_at')
            ->withTimestamps();
    }

    public function activeColocations()
    {
        return $this->colocations()
            ->wherePivotNull('left_at')
            ->where('colocations.status', 'active');
    }

    public function hasActiveColocation()
    {
        return $this->activeColocations()->exists();
    }

    public function activeColocation()
    {
        return $this->activeColocations()->first();
    }
}
