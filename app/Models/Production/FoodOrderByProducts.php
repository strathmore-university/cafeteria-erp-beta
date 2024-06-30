<?php

namespace App\Models\Production;

use App\Concerns\BelongsToArticle;
use App\Models\Core\Unit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FoodOrderByProducts extends Model
{
    use BelongsToArticle;

    protected $guarded = [];

    public function foodOrder(): BelongsTo
    {
        return $this->belongsTo(FoodOrder::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }
}
