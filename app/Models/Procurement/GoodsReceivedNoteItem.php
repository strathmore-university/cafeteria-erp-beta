<?php

namespace App\Models\Procurement;

use App\Concerns\BelongsToArticle;
use App\Models\Inventory\Batch;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GoodsReceivedNoteItem extends Model
{
    use BelongsToArticle;

    protected $guarded = [];

    public function goodsReceivedNote(): BelongsTo
    {
        return $this->belongsTo(GoodsReceivedNote::class);
    }

    public function batch(): BelongsTo
    {
        return $this->belongsTo(Batch::class);
    }

    public function purchaseOrderItem(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrderItem::class);
    }

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'batch_number' => 'string',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (GoodsReceivedNoteItem $item): void {
            $item->total_value = $item->units * $item->price;
        });

        static::updating(function (GoodsReceivedNoteItem $item): void {
            $item->total_value = $item->units * $item->price;
        });
    }
}
