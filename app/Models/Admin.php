<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Admin extends Model
{
    protected $table = 'admins';

    protected $fillable = [
        'id',
        // Profil pro
        'matricule', 'telephone_pro', 'notes', 'is_active',
        // Identité
        'date_naissance', 'lieu_naissance', 'sexe', 'etat_civil', 'nationalite',
        // Localisation personnelle
        'adresse_complete', 'province_id', 'territoire_id', 'secteur_id', 'ville_id', 'commune_id',
        // Professionnel
        'niveau_etude', 'specialite', 'experience_annees',
        // Pièce d'identité
        'type_piece_identite', 'numero_piece_identite',
    ];

    public $incrementing = false;
    protected $keyType = 'string';

    protected $casts = [
        'date_naissance'    => 'date',
        'is_active'         => 'boolean',
        'experience_annees' => 'integer',
    ];

    // ── Relations utilisateur ────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id');
    }

    // ── Relations localisation personnelle ───────────────────────────────────

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }

    public function territoire(): BelongsTo
    {
        return $this->belongsTo(Territoire::class);
    }

    public function secteur(): BelongsTo
    {
        return $this->belongsTo(Secteur::class);
    }

    public function ville(): BelongsTo
    {
        return $this->belongsTo(Ville::class);
    }

    public function commune(): BelongsTo
    {
        return $this->belongsTo(Commune::class);
    }

    // ── Provinces à charge ───────────────────────────────────────────────────

    public function provinces(): BelongsToMany
    {
        return $this->belongsToMany(Province::class, 'admin_provinces', 'admin_id', 'province_id')
            ->withPivot('is_active')
            ->withTimestamps();
    }

    public function activeProvinces(): BelongsToMany
    {
        return $this->belongsToMany(Province::class, 'admin_provinces', 'admin_id', 'province_id')
            ->withPivot('is_active')
            ->wherePivot('is_active', true)
            ->withTimestamps();
    }
}
