<?php

namespace App\Actions\Procurement\Grn\After;

use App\Models\Inventory\StockTransfer;
use App\Models\Inventory\StockTransferItem;
use App\Models\Procurement\GoodsReceivedNote;
use Illuminate\Support\Collection;

class CreateStockTransferRequest
{
    private Collection $items;

    private string $base;
    private GoodsReceivedNote $grn;

    public function execute(
        GoodsReceivedNote $grn,
        Collection $items
    ): void {
        $code = $grn->getAttribute('code');
        $this->base = 'Auto generated stock transfer against:'.$code.
            ' from: '.$grn->store->getAttribute('name').' to:';
        $this->items = $items;
        $this->grn = $grn;

        $this->createTransfers();

        $transfers = StockTransfer::where(
            'narration',
            'like',
            '%'.$code.'%'
        )->get();

        $this->populateItems($transfers);
    }

    private function createTransfers(): void
    {
        $items = collect();
        $this->items->each(function ($item) use ($items) {
            $items->push([
                'narration' => $this->base.$item->article->store->name,
                'team_id' => $this->grn->getAttribute('team_id'),
                'to_store_id' => $item->article->store_id,
                'from_store_id' => $this->grn->store_id,
                'created_by' => auth_id(),
                'status' => 'draft',
            ]);
        });

        StockTransfer::insert($items->unique()->toArray());
    }

    private function populateItems(Collection $transfers): void
    {
        $items = collect();
        $this->items->each(function ($item) use ($transfers, $items) {
            $name = $item->article->store->getAttribute('name');
            $narration = $this->base.$name;
            $transfer = $transfers->firstWhere('narration', '=', $narration);

            $items->push([
                'team_id' => $this->grn->getAttribute('team_id'),
                'stock_transfer_id' => $transfer->id,
                'article_id' => $item->article_id,
                'units' => $item->units,
                'status' => 'draft',
            ]);
        });

        StockTransferItem::insert($items->toArray());
    }
}
