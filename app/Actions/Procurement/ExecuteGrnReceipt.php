<?php

namespace App\Actions\Procurement;

use App\Models\Inventory\Batch;
use App\Models\Inventory\StockMovement;
use App\Models\Inventory\Store;
use App\Models\Procurement\GoodsReceivedNote;
use App\Models\Procurement\GoodsReceivedNoteItem;
use App\Models\Procurement\PriceQuote;
use App\Models\Procurement\PurchaseOrder;
use App\Models\Procurement\PurchaseOrderItem;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Throwable;

class ExecuteGrnReceipt
{
    private GoodsReceivedNote $grn;

    private GoodsReceivedNoteItem $item;

    public function execute(GoodsReceivedNote $grn, array $data = []): void
    {
        $this->grn = $grn;

        try {
            DB::transaction(function () use ($data): void {
                $items = $this->grnItems();

                // todo: create payment voucher

                // todo: create transfer request

                $store = $this->grn->store;
                $items->each(fn ($item) => $this->processGrnItem($item, $store));

                $this->updatePurchaseOrder();

                $total = $items->sum('total_value');
                $this->updateGrn($data, $total);

                GoodsReceivedNoteItem::where('units', '<=', 0)
                    ->where('goods_received_note_id', '=', $this->grn->id)
                    ->delete();

                success('Receipt executed successfully!');
            });
        } catch (Throwable $exception) {
            error_notification($exception);
        }

        // todo: post PV to kfs

        redirect(get_record_url($this->grn));
    }

    /**
     * @throws Throwable
     */
    public function processGrnItem(
        GoodsReceivedNoteItem $item,
        Store $store
    ): void {
        $this->item = $item;

        $narration = build_string([
            'Receiving', $this->item->units, 'units of',
            $this->item->article->getAttribute('name'), 'for purchase order of code:',
            $this->grn->purchaseOrder->getAttribute('code'),
        ]);

        $batch = $this->createBatch($store, $narration);
        $this->createMovement($store, $batch);
        $this->updateArticleValuationRate();
        $this->updatePriceQuote();
        $this->updatePOItem();

        update_stock_level()->team(team_id())
            ->article($this->item->article_id)
            ->units($this->item->units)
            ->store($store->id)
            ->index();

        // todo: add accounting lines
        // todo: add to transfer request
    }

    /**
     * @throws Throwable
     */
    public function updateArticleValuationRate(): void
    {
        $article = $this->item->article;
        $previousUnits = article_units($article);
        $totalUnits = $previousUnits + $this->item->units;

        $newStockValue = $this->item->price * $this->item->units;
        $previousStockValue = $article->valuation_rate * $previousUnits;

        $totalNewValuation = $previousStockValue + $newStockValue;
        $newValuationRate = $totalNewValuation / $totalUnits;
        $article->update(['valuation_rate' => $newValuationRate]);
    }

    /**
     * @throws Throwable
     */
    private function grnItems(): Collection
    {
        $with = ['article:id,name,lifespan_days,valuation_rate,is_reference,team_id'];

        $items = GoodsReceivedNoteItem::with($with)
            ->where('units', '>', 0)
            ->whereGoodsReceivedNoteId($this->grn->id)
            ->get();

        $message = 'There are no items to be received';
        throw_if(! $items->count(), new Exception($message));

        return $items;
    }

    private function createBatch(Store $store, string $narration): Batch
    {
        if (filled($this->item->expires_at)) {
            $expiryDate = $this->item->expires_at;
        } else {
            $lifespan = $this->item->article->lifespan_days;
            $expiryDate = match (filled($lifespan)) {
                true => now()->addDays($lifespan),
                false => null
            };
        }

        $batch = Batch::create([
            'team_id' => $store->getAttribute('team_id'),
            'owner_type' => $this->item->getMorphClass(),
            'batch_number' => $this->item->batch_number,
            'article_id' => $this->item->article_id,
            'initial_units' => $this->item->units,
            'weighted_cost' => $this->item->price,
            'owner_id' => $this->item->id,
            'expires_at' => $expiryDate,
            'narration' => $narration,
            'store_id' => $store->id,
        ]);

        $this->item->update(['batch_id' => $batch->id]);

        return $batch;
    }

    private function createMovement(Store $store, Batch $batch): void
    {
        StockMovement::create([
            'team_id' => $store->getAttribute('team_id'),
            'article_id' => $this->item->article_id,
            'weighted_cost' => $this->item->price,
            'narration' => $batch->narration,
            'units' => $this->item->units,
            'batch_id' => $batch->id,
            'store_id' => $store->id,
        ]);
    }

    private function updatePOItem(): void
    {
        $id = $this->item->purchase_order_item_id;
        $poItem = PurchaseOrderItem::whereId($id)
            ->select(['id', 'remaining_units'])
            ->first();

        $poItem->remaining_units -= $this->item->units;
        $poItem->update();
    }

    private function updatePriceQuote(): void
    {
        $id = $this->item->article_id;
        $price = $this->item->getAttribute('price');

        PriceQuote::whereSupplierId($this->grn->supplier_id)
            ->where('article_id', '=', $id)
            ->update(['price' => $price]);
    }

    /**
     * @throws Throwable
     */
    private function updateGrn(array $data, float $total): void
    {
        $invoice = $data['invoice_number'] ?? null;
        $deliveryNote = $data['delivery_note_number'] ?? null;
        $invoicedAt = tannery(filled($invoice), now(), null);

        $this->grn->delivery_note_number = $deliveryNote;
        $this->grn->invoice_number = $invoice;
        $this->grn->invoiced_at = $invoicedAt;
        $this->grn->received_by = auth_id();
        $this->grn->total_value = $total;
        $this->grn->received_at = now();
        $this->grn->updateStatus();
        $this->grn->update();
    }

    /**
     * @throws Throwable
     */
    private function updatePurchaseOrder(): void
    {
        $purchaseOrder = PurchaseOrder::whereId($this->grn->purchase_order_id)
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
