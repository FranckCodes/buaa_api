<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Secteur extends Model
{
    protected $fillable = ['territoire_id', 'designation'];

    public function territoire(): BelongsTo
    {
        return $this->belongsTo(Territoire::class);
    }
}
