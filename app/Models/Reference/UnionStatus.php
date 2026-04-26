<?php

namespace App\Models\Reference;

use App\Models\Union;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class UnionStatus extends Model
{
    public const SUSPENDU   = 'suspendu';
    public const EN_REVUE   = 'en_revue';
    public const ACTIVE     = 'active';
    public const DESACTIVEE = 'desactivee';

    protected $fillable = ['code', 'label', 'description', 'is_active', 'sort_order'];
    protected $casts = ['is_active' => 'boolean'];

    public function unions(): HasMany
    {
        return $this->hasMany(Union::class);
    }
}
