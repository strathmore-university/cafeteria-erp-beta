<?php

namespace App\Models\Procurement;

use App\Concerns\BelongsToArticle;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseOrderItem extends Model
{
    use BelongsToArticle;

    protected $guarded = [];

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    protected static function booted(): void
    {
        static::creating(function (PurchaseOrderItem $item): void {
            $item->remaining_units = $item->ordered_units;
            $total = $item->ordered_units * $item->price;
            $item->total_value = $total;
        });

        static::created(function (PurchaseOrderItem $item): void {
            $id = $item->purchase_order_id;
            $select = ['id', 'total_value', 'supplier_id'];
            $purchaseOrder = PurchaseOrder::select($select)->find($id);
            $purchaseOrder->total_value += $item->total_value;
            $purchaseOrder->update();

            $id = $item->getAttribute('article_id');
            $supplierId = $purchaseOrder->supplier_id;
            $priceQuote = PriceQuote::whereSupplierId($supplierId)
                ->where('article_id', '=', $id)
                ->select(['id', 'price'])
                ->first();

            if (filled($priceQuote)) {
                $priceQuote->update(['price' => $item->price]);
            }

            PriceQuote::create([
                'supplier_id' => $supplierId,
                'price' => $item->price,
                'article_id' => $id,
            ]);
        });
    }

    protected function getDeliveredUnitsAttribute(): int
    {
        return $this->ordered_units - $this->remaining_units;
    }
}
