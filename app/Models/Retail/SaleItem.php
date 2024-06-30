<?php

namespace App\Models\Retail;

use App\Concerns\BelongsToTeam;
use App\Models\Core\Unit;
use App\Models\Production\MenuItem;
use App\Models\Production\SellingPortion;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SaleItem extends Model
{
    use BelongsToTeam, SoftDeletes;

    public function sale(): BelongsTo
    {
        return $this->belongsTo(Sale::class);
    }

    public function menuItem(): BelongsTo
    {
        return $this->belongsTo(MenuItem::class);
    }

    public function portion(): BelongsTo
    {
        return $this->belongsTo(SellingPortion::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }
}
