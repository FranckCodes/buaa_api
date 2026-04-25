<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Commune extends Model
{
    protected $fillable = ['ville_id', 'designation'];

    public function ville(): BelongsTo
    {
        return $this->belongsTo(Ville::class);
    }
}
