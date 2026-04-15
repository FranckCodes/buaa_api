<?php

namespace App\Models\Reference;

use App\Models\Report;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReportStatus extends Model
{
    protected $fillable = ['code', 'label', 'description', 'is_active', 'sort_order'];
    protected $casts = ['is_active' => 'boolean'];

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }
}
