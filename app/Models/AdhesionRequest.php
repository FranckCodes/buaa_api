<?php

namespace App\Models;

use App\Models\Reference\ClientActivityType;
use App\Models\Reference\ClientStructureType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class AdhesionRequest extends Model
{
    public const ETAPE_EN_ATTENTE         = 'en_attente';
    public const ETAPE_VALIDE_PRESIDENT   = 'valide_president';
    public const ETAPE_VALIDE_SUPERVISEUR = 'valide_superviseur';
    public const ETAPE_ACCEPTE            = 'accepte';
    public const ETAPE_REJETE             = 'rejete';

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id', 'nom', 'demandeur_type',
        'union_id', 'client_id',
        'client_activity_type_id', 'client_structure_type_id',
        'representant', 'telephone', 'email', 'adresse',
        'province_id', 'territoire_id', 'secteur_id', 'ville_id', 'commune_id',
        'date_demande', 'cotisation', 'statut', 'etape_courante', 'motif_rejet',
        'numero_membre_attribue',
        'membres_nombre', 'superficie_totale', 'type_culture',
        'experience_annees', 'nombre_animaux', 'type_elevage', 'traite_par',
    ];

    protected $casts = [
        'date_demande'      => 'date',
        'cotisation'        => 'decimal:2',
        'superficie_totale' => 'decimal:2',
    ];

    public function union(): BelongsTo         { return $this->belongsTo(Union::class); }
    public function client(): BelongsTo        { return $this->belongsTo(Client::class); }
    public function activityType(): BelongsTo  { return $this->belongsTo(ClientActivityType::class, 'client_activity_type_id'); }
    public function structureType(): BelongsTo { return $this->belongsTo(ClientStructureType::class, 'client_structure_type_id'); }
    public function treatedBy(): BelongsTo     { return $this->belongsTo(User::class, 'traite_par'); }

    public function province(): BelongsTo   { return $this->belongsTo(Province::class); }
    public function territoire(): BelongsTo { return $this->belongsTo(Territoire::class); }
    public function secteur(): BelongsTo    { return $this->belongsTo(Secteur::class); }
    public function ville(): BelongsTo      { return $this->belongsTo(Ville::class); }
    public function commune(): BelongsTo    { return $this->belongsTo(Commune::class); }

    public function validations(): HasMany
    {
        return $this->hasMany(AdhesionRequestValidation::class);
    }

    public function presidentValidation()    { return $this->validations()->where('level', AdhesionRequestValidation::LEVEL_PRESIDENT); }
    public function superviseurValidation()  { return $this->validations()->where('level', AdhesionRequestValidation::LEVEL_SUPERVISEUR); }
    public function adminValidation()        { return $this->validations()->where('level', AdhesionRequestValidation::LEVEL_ADMIN); }

    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }
}
