<?php

namespace App\Actions\Procurement\Grn;

use App\Actions\Procurement\Grn\During\UpdateArticleValuationRate;
use App\Actions\Procurement\Grn\During\UpdateGrn;
use App\Models\Inventory\Store;
use App\Models\Procurement\GoodsReceivedNote;
use App\Models\Procurement\GoodsReceivedNoteItem;
use App\Services\Inventory\AddStock;
use App\Services\Inventory\CreateMovements;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Throwable;

class ExecuteGrnReceipt
{
    private CreateMovements $movements;

    private GoodsReceivedNote $grn;

    private AddStock $addStock;

    private function setup(GoodsReceivedNote $grn): void
    {
        $store = Store::with('stockLevels')->find($grn->store_id);
        $this->movements = create_movements();
        $this->grn = $grn;

        $code = $this->grn->getAttribute('code');
        $addStock = add_stock()->event('(Receiving ' . $code . ')');
        $this->addStock = $addStock->movement($this->movements)->at($store);
    }

    public function execute(
        GoodsReceivedNote $grn,
        array $data = []
    ): void {
        $this->setup($grn);

        try {
            DB::transaction(function () use ($data): void {
                $items = $this->items();
                $items->each(fn ($item) => $this->processItem($item));

                $this->movements->execute();

                $total = $items->sum('total_value');
                (new UpdateGrn())->execute($this->grn, $data, $total);
                (new PostGrnReceipt())->execute($this->grn, $items);
                success('Receipt executed successfully!');
            });
        } catch (Throwable $exception) {
            error_notification($exception);
        }
    }

    /**
     * @throws Throwable
     */
    public function processItem(GoodsReceivedNoteItem $item): void
    {
        (new UpdateArticleValuationRate())->execute($item);

        $this->movements = $this->addStock->owner($item)
            ->valuationRate($item->price)
            ->expiry($item->expires_at)
            ->code($item->batch_number)
            ->article($item->article)
            ->units($item->units)
            ->execute();

        $item->purchaseOrderItem->remaining_units -= $item->units;
        $item->purchaseOrderItem->update();

        $id = $this->grn->supplier_id;
        $quote = $item->article->quotes->where('supplier_id', '=', $id);
        $quote->toQuery()->update(['price' => $item->price]);
    }

    /**
     * @throws Throwable
     */
    private function items(): Collection
    {
        $extra = 'is_reference,team_id,store_id';
        $items = GoodsReceivedNoteItem::with([
            'article:id,name,lifespan_days,valuation_rate,' . $extra,
            'purchaseOrderItem:id,remaining_units',
            'article.quotes',
            'article.store',
        ])
            ->where('units', '>', 0)
            ->whereGoodsReceivedNoteId($this->grn->id)
            ->get();

        fire(blank($items), 'No items to receive!');

        return $items;
    }
}
