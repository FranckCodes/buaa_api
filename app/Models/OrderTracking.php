<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderTracking extends Model
{
    protected $table = 'order_tracking';

    protected $fillable = ['order_id', 'label', 'done', 'date_done', 'ordre'];

    protected $casts = ['done' => 'boolean', 'date_done' => 'date'];

    public function order(): BelongsTo { return $this->belongsTo(Order::class); }
}
