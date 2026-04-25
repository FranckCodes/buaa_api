<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Province extends Model
{
    protected $fillable = ['pays_id', 'designation'];

    public function pays(): BelongsTo
    {
        return $this->belongsTo(Pays::class);
    }

    public function territoires(): HasMany
    {
        return $this->hasMany(Territoire::class);
    }

    public function villes(): HasMany
    {
        return $this->hasMany(Ville::class);
    }
}
