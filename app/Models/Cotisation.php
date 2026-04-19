<?php

namespace App\Models;

use App\Models\Reference\PaymentMode;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Cotisation extends Model
{
    protected $fillable = [
        'adhesion_id', 'annee', 'montant', 'statut',
        'date_paiement', 'payment_mode_id', 'reference_recu',
    ];

    protected $casts = [
        'montant'       => 'decimal:2',
        'date_paiement' => 'date',
    ];

    public function adhesion(): BelongsTo { return $this->belongsTo(Adhesion::class); }
    public function paymentMode(): BelongsTo { return $this->belongsTo(PaymentMode::class, 'payment_mode_id'); }
}
