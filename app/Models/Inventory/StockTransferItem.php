<?php

namespace App\Models\Inventory;

use App\Concerns\BelongsToTeam;
use App\Concerns\HasStatusTransitions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockTransferItem extends Model
{
    use BelongsToTeam, HasStatusTransitions;

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    public function transfer(): BelongsTo
    {
        return $this->belongsTo(StockTransfer::class);
    }

    protected static function booted(): void
    {
        parent::creating(function (StockTransferItem $item): void {
            $item->dispatched_units = $item->units;
            $item->received_units = $item->units;
            $item->setAttribute('status', 'draft');
        });
    }

    protected function statusTransitions(): array
    {
        return [
            'draft' => 'dispatched',
            'dispatched' => 'received',
        ];
    }
}
