<?php

namespace App\Models\Reference;

use App\Models\Client;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ClientActivityType extends Model
{
    protected $fillable = ['code', 'label', 'description', 'is_active', 'sort_order'];
    protected $casts = ['is_active' => 'boolean'];

    public function clients(): HasMany
    {
        return $this->hasMany(Client::class);
    }
}
