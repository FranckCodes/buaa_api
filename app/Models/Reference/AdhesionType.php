<?php

namespace App\Models\Reference;

use App\Models\Adhesion;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AdhesionType extends Model
{
    protected $fillable = ['code', 'label', 'description', 'is_active', 'sort_order'];
    protected $casts = ['is_active' => 'boolean'];

    public function adhesions(): HasMany
    {
        return $this->hasMany(Adhesion::class);
    }
}
