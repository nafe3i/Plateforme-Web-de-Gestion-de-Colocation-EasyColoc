<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Colocation extends Model
{
    protected $fillable = [
        'name',
        'description',
        'owner_id',
        'status',
    ];

    protected $casts = [
        'status' => 'string',
    ];

    /**
     * Relation : Colocation appartient à un owner
     */
    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    /**
     * Relation : Colocation a plusieurs memberships
     */
    public function memberships(): HasMany
    {
        return $this->hasMany(Membership::class);
    }

    /**
     * Relation : Colocation a plusieurs membres
     */
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'memberships')
            ->withPivot('role', 'balance', 'manual_adjustment', 'joined_at', 'left_at')
            ->withTimestamps();
    }

    /**
     * Membres actifs uniquement
     */
    public function activeMembers(): BelongsToMany
    {
        return $this->members()->wherePivotNull('left_at');
    }

    /**
     * Vérifier si la colocation est active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Vérifier si un user est membre
     */
    public function hasMember(User $user): bool
    {
        return $this->activeMembers()->where('users.id', $user->id)->exists();
    }

    /**
     * Vérifier si un user est owner
     */
    public function isOwner(User $user): bool
    {
        return $this->owner_id === $user->id;
    }
    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

}
