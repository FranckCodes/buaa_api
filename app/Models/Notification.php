<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notification extends Model
{
    protected $fillable = [
        'user_id', 'category', 'type', 'title', 'body',
        'is_read', 'action_label', 'action_url', 'from_user_id',
    ];

    protected $casts = ['is_read' => 'boolean'];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function fromUser(): BelongsTo { return $this->belongsTo(User::class, 'from_user_id'); }
}
