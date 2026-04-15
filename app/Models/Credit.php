<?php

namespace App\Models;

use App\Models\Reference\CreditStatus;
use App\Models\Reference\CreditType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Credit extends Model
{
    protected $fillable = [
        'client_id', 'credit_type_id', 'credit_status_id',
        'montant_demande', 'montant_approuve', 'montant_rembourse',
        'date_demande', 'date_approbation', 'duree_mois', 'taux_interet',
        'prochaine_echeance', 'montant_echeance', 'objet_credit',
        'description_projet', 'retour_investissement', 'revenus_mensuels',
        'autres_credits', 'montant_autres_credits', 'traite_par',
    ];

    protected $casts = [
        'montant_demande' => 'decimal:2', 'montant_approuve' => 'decimal:2',
        'montant_rembourse' => 'decimal:2', 'date_demande' => 'date',
        'date_approbation' => 'date', 'taux_interet' => 'decimal:2',
        'prochaine_echeance' => 'date', 'montant_echeance' => 'decimal:2',
        'revenus_mensuels' => 'decimal:2', 'autres_credits' => 'boolean',
        'montant_autres_credits' => 'decimal:2',
    ];

    public function client(): BelongsTo { return $this->belongsTo(Client::class); }
    public function type(): BelongsTo { return $this->belongsTo(CreditType::class, 'credit_type_id'); }
    public function status(): BelongsTo { return $this->belongsTo(CreditStatus::class, 'credit_status_id'); }
    public function treatedBy(): BelongsTo { return $this->belongsTo(User::class, 'traite_par'); }
    public function payments(): HasMany { return $this->hasMany(CreditPayment::class); }
    public function businessPlan(): HasOne { return $this->hasOne(BusinessPlan::class); }

    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }
}
