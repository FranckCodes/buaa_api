<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AdhesionRequestValidation extends Model
{
    public const LEVEL_PRESIDENT   = 'president';
    public const LEVEL_SUPERVISEUR = 'superviseur';
    public const LEVEL_ADMIN       = 'admin';

    public const DECISION_PENDING  = 'en_attente';
    public const DECISION_APPROVED = 'valide';
    public const DECISION_REJECTED = 'rejete';

    protected $fillable = [
        'adhesion_request_id', 'level', 'decision',
        'validator_id', 'motif', 'decided_at',
    ];

    protected $casts = [
        'decided_at' => 'datetime',
    ];

    public function adhesionRequest(): BelongsTo
    {
        return $this->belongsTo(AdhesionRequest::class);
    }

    public function validator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validator_id');
    }
}
