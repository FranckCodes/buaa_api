<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UnionMember extends Model
{
    protected $fillable = [
        'union_id', 'user_id', 'nom_complet', 'telephone',
        'role_dans_union', 'date_debut', 'date_fin',
    ];

    protected $casts = ['date_debut' => 'date', 'date_fin' => 'date'];

    public function union(): BelongsTo { return $this->belongsTo(Union::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
