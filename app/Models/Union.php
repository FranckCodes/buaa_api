<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Union extends Model
{
    protected $fillable = [
        'nom', 'type', 'province', 'ville', 'adresse', 'telephone', 'email',
        'date_creation', 'membres_total', 'superficie_totale', 'cultures_principales', 'services',
    ];

    protected $casts = [
        'date_creation' => 'date',
        'superficie_totale' => 'decimal:2',
        'cultures_principales' => 'array',
        'services' => 'array',
    ];

    public function members(): HasMany { return $this->hasMany(UnionMember::class); }
    public function adhesions(): HasMany { return $this->hasMany(Adhesion::class); }
}
