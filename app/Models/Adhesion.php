<?php

namespace App\Models;

use App\Models\Reference\AdhesionStatus;
use App\Models\Reference\AdhesionType;
use App\Models\Reference\PaymentMode;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Adhesion extends Model
{
    protected $fillable = [
        'client_id', 'union_id', 'adhesion_type_id', 'adhesion_status_id',
        'numero_membre', 'date_adhesion', 'prochaine_echeance',
        'cotisation_initiale', 'cotisation_annuelle', 'payment_mode_id', 'avantages',
    ];

    protected $casts = [
        'date_adhesion' => 'date',
        'prochaine_echeance' => 'date',
        'cotisation_initiale' => 'decimal:2',
        'cotisation_annuelle' => 'decimal:2',
        'avantages' => 'array',
    ];

    public function client(): BelongsTo { return $this->belongsTo(Client::class); }
    public function union(): BelongsTo { return $this->belongsTo(Union::class); }
    public function type(): BelongsTo { return $this->belongsTo(AdhesionType::class, 'adhesion_type_id'); }
    public function status(): BelongsTo { return $this->belongsTo(AdhesionStatus::class, 'adhesion_status_id'); }
    public function paymentMode(): BelongsTo { return $this->belongsTo(PaymentMode::class, 'payment_mode_id'); }
    public function cotisations(): HasMany { return $this->hasMany(Cotisation::class); }

    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }
}
