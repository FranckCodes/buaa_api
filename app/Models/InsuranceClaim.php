<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class InsuranceClaim extends Model
{
    protected $fillable = [
        'insurance_id', 'client_id', 'type_sinistre', 'montant_reclame',
        'montant_approuve', 'statut', 'description', 'date_sinistre',
        'date_soumission', 'traite_par',
    ];

    protected $casts = [
        'montant_reclame' => 'decimal:2', 'montant_approuve' => 'decimal:2',
        'date_sinistre' => 'date', 'date_soumission' => 'date',
    ];

    public function insurance(): BelongsTo { return $this->belongsTo(Insurance::class); }
    public function client(): BelongsTo { return $this->belongsTo(Client::class); }
    public function treatedBy(): BelongsTo { return $this->belongsTo(User::class, 'traite_par'); }

    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }
}
