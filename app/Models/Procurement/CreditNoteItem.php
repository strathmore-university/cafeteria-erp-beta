<?php

namespace App\Models\Procurement;

use App\Models\Inventory\Article;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CreditNoteItem extends Model
{
    protected $guarded = [];

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    public function creditNote(): BelongsTo
    {
        return $this->belongsTo(CreditNote::class);
    }

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function purchaseOrderItem(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrderItem::class);
    }

    protected static function booted(): void
    {
        static::creating(function (CreditNoteItem $item): void {
            $item->total_value = $item->units * $item->price;
        });

        static::updating(function (CreditNoteItem $item): void {
            $item->total_value = $item->units * $item->price;
        });
    }
}
