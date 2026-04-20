<?php

namespace App\Models;

use App\Models\Reference\InsuranceStatus;
use App\Models\Reference\InsuranceType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Insurance extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id', 'client_id', 'insurance_type_id', 'insurance_status_id',
        'montant_annuel', 'date_souscription', 'date_debut', 'date_fin',
        'prochaine_echeance', 'description', 'couvertures', 'etablissement',
        'niveau_etude', 'superficie_hectares', 'type_culture', 'valeur_materiel',
        'antecedents_medicaux', 'medecin_traitant', 'traite_par',
    ];

    protected $casts = [
        'montant_annuel'      => 'decimal:2',
        'date_souscription'   => 'date',
        'date_debut'          => 'date',
        'date_fin'            => 'date',
        'prochaine_echeance'  => 'date',
        'couvertures'         => 'array',
        'superficie_hectares' => 'decimal:2',
        'valeur_materiel'     => 'decimal:2',
    ];

    public function client(): BelongsTo { return $this->belongsTo(Client::class); }
    public function type(): BelongsTo { return $this->belongsTo(InsuranceType::class, 'insurance_type_id'); }
    public function status(): BelongsTo { return $this->belongsTo(InsuranceStatus::class, 'insurance_status_id'); }
    public function treatedBy(): BelongsTo { return $this->belongsTo(User::class, 'traite_par'); }
    public function beneficiaries(): HasMany { return $this->hasMany(InsuranceBeneficiary::class); }
    public function claims(): HasMany { return $this->hasMany(InsuranceClaim::class); }

    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }
}
