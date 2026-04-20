<?php

namespace App\Models;

use App\Models\Reference\OrderStatus;
use App\Models\Reference\OrderType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Order extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id', 'client_id', 'order_type_id', 'order_status_id',
        'montant', 'description', 'justification', 'quantite', 'unite',
        'priorite', 'progression', 'date_soumission', 'traite_par',
    ];

    protected $casts = [
        'montant'         => 'decimal:2',
        'quantite'        => 'decimal:2',
        'date_soumission' => 'date',
    ];

    public function client(): BelongsTo { return $this->belongsTo(Client::class); }
    public function type(): BelongsTo { return $this->belongsTo(OrderType::class, 'order_type_id'); }
    public function status(): BelongsTo { return $this->belongsTo(OrderStatus::class, 'order_status_id'); }
    public function treatedBy(): BelongsTo { return $this->belongsTo(User::class, 'traite_par'); }
    public function trackingSteps(): HasMany { return $this->hasMany(OrderTracking::class); }

    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }
}
