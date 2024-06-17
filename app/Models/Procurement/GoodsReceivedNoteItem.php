<?php

namespace App\Models\Procurement;

use App\Models\Inventory\Article;
use App\Models\Inventory\Batch;
use App\Models\Inventory\StockMovement;
use App\Models\Inventory\Store;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use Throwable;

class GoodsReceivedNoteItem extends Model
{
    protected $guarded = [];

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

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

    /**
     * @throws Throwable
     */
    public function receive(Store $store): void
    {
        DB::transaction(function () use ($store): void {
            $narration = build_string([
                'Receiving', $this->units, 'units of',
                $this->article->getAttribute('name'), 'for purchase order of code:',
                $this->purchaseOrder->getAttribute('code'),
            ]);

            $batch = $this->createBatch($store, $narration);

            $this->createMovement($store, $batch);

            $this->updatePriceQuote();

            $this->updatePOItem();

            update_stock_level()->team($store->getAttribute('team_id'))
                ->article($this->article_id)
                ->units($this->units)
                ->store($store->id)
                ->index();

            $this->updatePurchaseOrder();
        });
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

    private function createBatch(Store $store, string $narration): Batch
    {
        // todo: add expires at here and on the form as well

        $batch = Batch::create([
            'team_id' => $store->getAttribute('team_id'),
            'owner_type' => $this->getMorphClass(),
            'batch_number' => $this->batch_number,
            'article_id' => $this->article_id,
            'initial_units' => $this->units,
            'weighted_cost' => $this->price,
            'narration' => $narration,
            'store_id' => $store->id,
            'owner_id' => $this->id,
        ]);

        $this->batch_id = $batch->id;
        $this->update();

        return $batch;
    }

    private function createMovement(Store $store, Batch $batch): void
    {
        StockMovement::create([
            'team_id' => $store->getAttribute('team_id'),
            'article_id' => $this->article_id,
            'narration' => $batch->narration,
            'weighted_cost' => $this->price,
            'batch_id' => $batch->id,
            'store_id' => $store->id,
            'units' => $this->units,
        ]);
    }

    private function updatePOItem(): void
    {
        $id = $this->purchase_order_item_id;
        $poItem = PurchaseOrderItem::whereId($id)
            ->select(['id', 'remaining_units'])
            ->first();

        $poItem->remaining_units -= $this->units;
        $poItem->update();
    }

    private function updatePriceQuote(): void
    {
        $grn = GoodsReceivedNote::select('supplier_id')
            ->whereId($this->goods_received_note_id)
            ->first();

        $quote = PriceQuote::whereSupplierId($grn->supplier_id)
            ->where('article_id', '=', $this->article_id)
            ->select(['id', 'price'])
            ->first();

        $price = $this->getAttribute('price');

        if ($quote->price === $price) {
            return;
        }

        $quote->update(['price' => $price]);
    }

    /**
     * @throws Throwable
     */
    private function updatePurchaseOrder(): void
    {
        $purchaseOrder = PurchaseOrder::whereId($this->purchase_order_id)
            ->select(['id', 'is_fulfilled', 'delivered_at', 'status'])
            ->first();

        if ($purchaseOrder->isFulfilled()) {
            $purchaseOrder->delivered_at = now();
            $purchaseOrder->is_fulfilled = true;
            $purchaseOrder->updateStatus();
            $purchaseOrder->update();
        }
    }
}
