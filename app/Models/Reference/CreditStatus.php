<?php

namespace App\Models\Reference;

use App\Models\Credit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CreditStatus extends Model
{
    protected $fillable = ['code', 'label', 'description', 'is_active', 'sort_order'];
    protected $casts = ['is_active' => 'boolean'];

    public function credits(): HasMany
    {
        return $this->hasMany(Credit::class);
    }
}
