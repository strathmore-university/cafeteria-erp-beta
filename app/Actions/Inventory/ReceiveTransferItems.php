<?php

namespace App\Actions\Inventory;

use App\Models\Inventory\StockTransfer;
use App\Models\Inventory\StockTransferItem;
use App\Models\Inventory\Store;
use App\Services\Inventory\CreateMovements;
use App\Services\Inventory\MoveStock;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Throwable;

class ReceiveTransferItems
{
    private CreateMovements $movements;

    private StockTransfer $transfer;

    private MoveStock $moveStock;

    private Store $from;

    private function setup(StockTransfer $transfer): void
    {
        $this->movements = create_movements();
        $this->transfer = $transfer;

        $this->from = Store::with(['stockLevels', 'batches'])
            ->whereId($this->transfer->from_store_id)
            ->select(['name', 'id', 'can_ship_stock'])
            ->first();

        $to = Store::with('stockLevels')
            ->whereId($this->transfer->to_store_id)
            ->select(['name', 'id'])->first();

        $this->moveStock = move_stock()
            ->movement($this->movements)
            ->from($this->from)->to($to);
    }

    public function execute(StockTransfer $transfer): void
    {
        $this->setup($transfer);

        try {
            DB::transaction(function (): void {
                // todo: what if received and dispatched are different?
                $this->items()->each(fn ($item) => $this->processItem($item));
                $this->movements->execute();
                $this->moveStock->clear();
                $this->updateTransfer();

                $id = $this->transfer->id;
                StockTransferItem::whereStockTransferId($id)
                    ->update(['status' => 'received']);

                success();
            });
        } catch (Throwable $exception) {
            error_notification($exception);
        }
    }

    private function items(): Collection
    {
        return StockTransferItem::with('article')
            ->whereStockTransferId($this->transfer->id)
            ->get();
    }

    /**
     * @throws Throwable
     */
    private function processItem(StockTransferItem $item): void
    {
        $batches = $this->from->batches
            ->where('article_id', '=', $item->article->id)
            ->where('current_units', '>', 0);

        $this->movements = $this->moveStock->batches($batches)
            ->units($item->received_units)
            ->article($item->article)
            ->execute();
    }

    /**
     * @throws Throwable
     */
    private function updateTransfer(): void
    {
        $this->transfer->received_at = now();
        $this->transfer->updateStatus();
        $this->transfer->update();
    }
}
