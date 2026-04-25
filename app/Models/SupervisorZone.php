<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupervisorZone extends Model
{
    protected $table = 'supervisor_zones';

    protected $fillable = [
        'superviseur_id',
        'province_id',
        'territoire_id',
        'secteur_id',
        'commune_id',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // ── Relations ────────────────────────────────────────────────────────────

    public function superviseur(): BelongsTo
    {
        return $this->belongsTo(Superviseur::class, 'superviseur_id');
    }

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }

    public function territoire(): BelongsTo
    {
        return $this->belongsTo(Territoire::class);
    }

    public function secteur(): BelongsTo
    {
        return $this->belongsTo(Secteur::class);
    }

    public function commune(): BelongsTo
    {
        return $this->belongsTo(Commune::class);
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    /**
     * Retourne une description lisible de la zone.
     * Province normale : "Kongo Central > Luozi > Secteur X"
     * Kinshasa         : "Kinshasa > Gombe"
     */
    public function getDescriptionAttribute(): string
    {
        $parts = [];

        if ($this->province)   $parts[] = $this->province->designation;
        if ($this->territoire) $parts[] = $this->territoire->designation;
        if ($this->secteur)    $parts[] = $this->secteur->designation;
        if ($this->commune)    $parts[] = $this->commune->designation;

        return implode(' > ', $parts);
    }
}
