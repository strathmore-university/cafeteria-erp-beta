<?php

namespace App\Models\Inventory;

use App\Concerns\BelongsToTeam;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockTransferItem extends Model
{
    use BelongsToTeam;

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    public function transfer(): BelongsTo
    {
        return $this->belongsTo(StockTransfer::class);
    }
}
