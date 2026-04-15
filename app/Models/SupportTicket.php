<?php

namespace App\Models;

use App\Models\Reference\SupportCategory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SupportTicket extends Model
{
    protected $fillable = [
        'client_id', 'support_category_id', 'sujet', 'description',
        'statut', 'traite_par', 'resolved_at',
    ];

    protected $casts = ['resolved_at' => 'datetime'];

    public function client(): BelongsTo { return $this->belongsTo(Client::class); }
    public function category(): BelongsTo { return $this->belongsTo(SupportCategory::class, 'support_category_id'); }
    public function treatedBy(): BelongsTo { return $this->belongsTo(User::class, 'traite_par'); }
}
