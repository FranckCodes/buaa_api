<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BusinessPlan extends Model
{
    protected $fillable = [
        'credit_id', 'client_id', 'titre', 'resume', 'description',
        'retour_investissement', 'statut', 'score', 'date_soumission', 'evalue_par',
    ];

    protected $casts = ['date_soumission' => 'date'];

    public function credit(): BelongsTo { return $this->belongsTo(Credit::class); }
    public function client(): BelongsTo { return $this->belongsTo(Client::class); }
    public function evaluator(): BelongsTo { return $this->belongsTo(User::class, 'evalue_par'); }
}
