<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InsuranceBeneficiary extends Model
{
    protected $fillable = ['insurance_id', 'nom', 'age', 'relation'];

    public function insurance(): BelongsTo { return $this->belongsTo(Insurance::class); }
}
