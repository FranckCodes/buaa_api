<?php

namespace App\Models;

use App\Models\Reference\ClientActivityType;
use App\Models\Reference\ClientStructureType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Client extends Model
{
    protected $fillable = [
        'id',
        // Identité
        'date_naissance', 'lieu_naissance', 'sexe', 'etat_civil', 'nationalite',
        // Localisation
        'adresse_complete', 'province_id', 'territoire_id', 'secteur_id', 'ville_id', 'commune_id',
        // Activité
        'client_activity_type_id', 'client_structure_type_id',
        'profession_detaillee', 'experience_annees', 'superficie_exploitation',
        'type_culture', 'nombre_animaux',
        // Finances
        'revenus_mensuels', 'autres_sources_revenus', 'banque_principale', 'numero_compte',
        // Référence
        'ref_nom', 'ref_telephone', 'ref_relation',
        // Assignation
        'superviseur_id',
    ];

    public $incrementing = false;
    protected $keyType = 'string';

    protected $casts = [
        'date_naissance'          => 'date',
        'superficie_exploitation' => 'decimal:2',
        'revenus_mensuels'        => 'decimal:2',
    ];

    // ── Relations utilisateur ────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id');
    }

    public function superviseur(): BelongsTo
    {
        return $this->belongsTo(User::class, 'superviseur_id');
    }

    // ── Relations localisation ───────────────────────────────────────────────

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

    // ── Relations références ─────────────────────────────────────────────────

    public function activityType(): BelongsTo
    {
        return $this->belongsTo(ClientActivityType::class, 'client_activity_type_id');
    }

    public function structureType(): BelongsTo
    {
        return $this->belongsTo(ClientStructureType::class, 'client_structure_type_id');
    }

    // ── Relations métier ─────────────────────────────────────────────────────

    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function adhesions(): HasMany      { return $this->hasMany(Adhesion::class); }
    public function credits(): HasMany        { return $this->hasMany(Credit::class); }
    public function insurances(): HasMany     { return $this->hasMany(Insurance::class); }
    public function orders(): HasMany         { return $this->hasMany(Order::class); }
    public function reports(): HasMany        { return $this->hasMany(Report::class); }
    public function supportTickets(): HasMany { return $this->hasMany(SupportTicket::class); }
    public function insuranceClaims(): HasMany { return $this->hasMany(InsuranceClaim::class); }
    public function businessPlans(): HasMany  { return $this->hasMany(BusinessPlan::class); }
}
