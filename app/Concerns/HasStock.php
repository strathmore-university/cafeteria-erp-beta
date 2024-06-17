<?php

namespace App\Concerns;

use App\Models\Inventory\Batch;
use App\Models\Inventory\StockLevel;
use App\Models\Inventory\StockMovement;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasStock
{
    public function stockLevels(): HasMany
    {
        return $this->hasMany(StockLevel::class);
    }

    public function batches(): HasMany
    {
        return $this->hasMany(Batch::class);
    }

    public function movements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }
}
