<?php

namespace App\Models;

use App\Models\Reference\PostStatus;
use App\Models\Reference\PostTag;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Post extends Model
{
    protected $fillable = [
        'author_id', 'content', 'post_tag_id', 'post_status_id',
        'valide_par', 'motif_rejet', 'likes_count',
    ];

    public function author(): BelongsTo { return $this->belongsTo(User::class, 'author_id'); }
    public function tag(): BelongsTo { return $this->belongsTo(PostTag::class, 'post_tag_id'); }
    public function status(): BelongsTo { return $this->belongsTo(PostStatus::class, 'post_status_id'); }
    public function validatedBy(): BelongsTo { return $this->belongsTo(User::class, 'valide_par'); }
    public function media(): HasMany { return $this->hasMany(PostMedia::class); }
    public function likes(): HasMany { return $this->hasMany(PostLike::class); }
    public function saves(): HasMany { return $this->hasMany(PostSave::class); }
    public function comments(): HasMany { return $this->hasMany(Comment::class); }

    public function likedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'post_likes', 'post_id', 'user_id')->withTimestamps();
    }

    public function savedByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'post_saves', 'post_id', 'user_id')->withTimestamps();
    }
}
