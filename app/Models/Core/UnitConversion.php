<?php

namespace App\Models\Core;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UnitConversion extends Model
{
    protected $guarded = [];

    protected $with = ['from', 'to'];

    public function from(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'from_unit_id');
    }

    public function to(): BelongsTo
    {
        return $this->belongsTo(Unit::class, 'to_unit_id');
    }
}
