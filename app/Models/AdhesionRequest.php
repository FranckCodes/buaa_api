<?php

namespace App\Models;

use App\Models\Reference\ClientActivityType;
use App\Models\Reference\ClientStructureType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdhesionRequest extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id', 'nom', 'demandeur_type', 'client_activity_type_id', 'client_structure_type_id',
        'representant', 'telephone', 'email', 'adresse', 'province', 'date_demande',
        'cotisation', 'statut', 'membres_nombre', 'superficie_totale', 'type_culture',
        'experience_annees', 'nombre_animaux', 'type_elevage', 'traite_par',
    ];

    protected $casts = [
        'date_demande'    => 'date',
        'cotisation'      => 'decimal:2',
        'superficie_totale' => 'decimal:2',
    ];

    public function activityType(): BelongsTo { return $this->belongsTo(ClientActivityType::class, 'client_activity_type_id'); }
    public function structureType(): BelongsTo { return $this->belongsTo(ClientStructureType::class, 'client_structure_type_id'); }
    public function treatedBy(): BelongsTo { return $this->belongsTo(User::class, 'traite_par'); }
}
