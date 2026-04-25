<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Superviseur extends Model
{
    protected $table = 'superviseurs';

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
        'date_naissance'   => 'date',
        'is_active'        => 'boolean',
        'experience_annees'=> 'integer',
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

    // ── Relations zones de supervision ───────────────────────────────────────

    public function zones(): HasMany
    {
        return $this->hasMany(SupervisorZone::class, 'superviseur_id');
    }

    public function activeZones(): HasMany
    {
        return $this->hasMany(SupervisorZone::class, 'superviseur_id')->where('is_active', true);
    }

    // ── Relations clients ────────────────────────────────────────────────────

    public function clients(): HasMany
    {
        return $this->hasMany(Client::class, 'superviseur_id', 'id');
    }
}
