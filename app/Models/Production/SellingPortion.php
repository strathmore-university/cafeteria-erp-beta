<?php

namespace App\Models\Production;

use App\Concerns\BelongsToArticle;
use App\Concerns\BelongsToTeam;
use App\Models\Core\Unit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Throwable;

class SellingPortion extends Model
{
    use BelongsToArticle, BelongsToTeam;

    protected $guarded = [];

    public function menuItem(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    /**
     * @throws Throwable
     */
    public function available(): int|float
    {
        $portions = $this->menuItem->currentStock();
        $from = $this->article->unit;

        return quantity_converter($from, $this->unit, $portions);
    }
}
