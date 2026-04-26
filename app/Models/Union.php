<?php

namespace App\Models;

use App\Models\Reference\UnionStatus;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Union extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id', 'nom', 'type',
        'union_status_id', 'president_id',
        'province_id', 'territoire_id', 'secteur_id', 'ville_id', 'commune_id',
        'adresse', 'telephone', 'email', 'date_creation',
        'secretaire', 'tresorier', 'commissaire',
        'membres_total', 'superficie_totale', 'cultures_principales', 'services',
        'validated_by', 'validated_at',
        'deactivated_by', 'deactivated_at', 'deactivation_reason',
    ];

    protected $casts = [
        'date_creation'        => 'date',
        'validated_at'         => 'datetime',
        'deactivated_at'       => 'datetime',
        'superficie_totale'    => 'decimal:2',
        'cultures_principales' => 'array',
        'services'             => 'array',
    ];

    public function status(): BelongsTo     { return $this->belongsTo(UnionStatus::class, 'union_status_id'); }
    public function president(): BelongsTo  { return $this->belongsTo(User::class, 'president_id'); }
    public function validator(): BelongsTo  { return $this->belongsTo(User::class, 'validated_by'); }
    public function deactivator(): BelongsTo{ return $this->belongsTo(User::class, 'deactivated_by'); }

    public function province(): BelongsTo   { return $this->belongsTo(\App\Models\Province::class); }
    public function territoire(): BelongsTo { return $this->belongsTo(\App\Models\Territoire::class); }
    public function secteur(): BelongsTo    { return $this->belongsTo(\App\Models\Secteur::class); }
    public function ville(): BelongsTo      { return $this->belongsTo(\App\Models\Ville::class); }
    public function commune(): BelongsTo    { return $this->belongsTo(\App\Models\Commune::class); }

    public function adhesions(): HasMany     { return $this->hasMany(Adhesion::class); }
    public function adhesionRequests(): HasMany { return $this->hasMany(AdhesionRequest::class); }
    public function members(): HasMany       { return $this->hasMany(UnionMember::class); }

    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function isActive(): bool     { return $this->status?->code === UnionStatus::ACTIVE; }
    public function isSuspended(): bool  { return $this->status?->code === UnionStatus::SUSPENDU; }
    public function isDeactivated(): bool{ return $this->status?->code === UnionStatus::DESACTIVEE; }
}
