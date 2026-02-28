<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Membership extends Model
{
    protected $fillable = [
        'user_id',
        'colocation_id',
        'role',
        'balance',
        'manual_adjustment',
        'joined_at',
        'left_at',
    ];

    protected $casts = [
        'joined_at' => 'datetime',
        'left_at' => 'datetime',
        'balance' => 'decimal:2',
        'manual_adjustment' => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function colocation(): BelongsTo
    {
        return $this->belongsTo(Colocation::class);
    }

    public function isActive(): bool
    {
        return $this->left_at === null;
    }

    public function hasDebt(): bool
    {
        return (float) $this->balance > 0;
    }

    public function getDebtAmount(): float
    {
        return max(0, (float) $this->balance);
    }
}
