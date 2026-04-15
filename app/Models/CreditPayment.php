<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CreditPayment extends Model
{
    protected $fillable = [
        'credit_id', 'periode_annee', 'periode_mois', 'montant',
        'statut', 'date_paiement', 'date_echeance',
    ];

    protected $casts = [
        'montant' => 'decimal:2',
        'date_paiement' => 'date',
        'date_echeance' => 'date',
    ];

    public function credit(): BelongsTo { return $this->belongsTo(Credit::class); }
}
