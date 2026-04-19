<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Union extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id', 'nom', 'type', 'province', 'ville', 'adresse', 'telephone', 'email',
        'date_creation', 'president', 'secretaire', 'tresorier', 'commissaire',
        'membres_total', 'superficie_totale', 'cultures_principales', 'services',
    ];

    protected $casts = [
        'date_creation'        => 'date',
        'superficie_totale'    => 'decimal:2',
        'cultures_principales' => 'array',
        'services'             => 'array',
    ];

    public function adhesions(): HasMany { return $this->hasMany(Adhesion::class); }
}
