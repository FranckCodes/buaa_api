<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OtpCode extends Model
{
    protected $fillable = [
        'user_id',
        'telephone',
        'code',
        'purpose',
        'expires_at',
        'verified_at',
        'attempts',
        'is_used',
    ];

    protected $casts = [
        'expires_at'  => 'datetime',
        'verified_at' => 'datetime',
        'is_used'     => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isExpired(): bool
    {
        return now()->greaterThan($this->expires_at);
    }
}
