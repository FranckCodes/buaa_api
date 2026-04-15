<?php

namespace App\Models;

use App\Models\Reference\ReportStatus;
use App\Models\Reference\ReportType;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Report extends Model
{
    protected $fillable = [
        'client_id', 'superviseur_id', 'report_type_id', 'report_status_id',
        'summary', 'value_numeric', 'value_unit', 'value_text', 'details',
        'date_rapport', 'valide_par', 'motif_rejet',
    ];

    protected $casts = ['value_numeric' => 'decimal:2', 'date_rapport' => 'date'];

    public function client(): BelongsTo { return $this->belongsTo(Client::class); }
    public function superviseur(): BelongsTo { return $this->belongsTo(User::class, 'superviseur_id'); }
    public function type(): BelongsTo { return $this->belongsTo(ReportType::class, 'report_type_id'); }
    public function status(): BelongsTo { return $this->belongsTo(ReportStatus::class, 'report_status_id'); }
    public function validatedBy(): BelongsTo { return $this->belongsTo(User::class, 'valide_par'); }

    public function documents(): MorphMany
    {
        return $this->morphMany(Document::class, 'documentable');
    }
}
