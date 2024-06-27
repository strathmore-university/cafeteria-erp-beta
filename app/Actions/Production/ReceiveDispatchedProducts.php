<?php

namespace App\Actions\Production;

use App\Models\Inventory\Store;
use App\Models\Production\ProductDispatch;
use App\Models\Production\ProductDispatchItem;
use App\Services\Inventory\CreateMovements;
use App\Services\Inventory\MoveStock;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Throwable;

class ReceiveDispatchedProducts
{
    private CreateMovements $movements;

    private ProductDispatch $dispatch;

    private MoveStock $moveStock;

    private Store $from;

    private function setup(ProductDispatch $dispatch): void
    {
        $this->movements = create_movements();
        $this->dispatch = $dispatch;

        $from = $dispatch->from_store_id;
        $to = $dispatch->to_store_id;
        $stores = Store::with(['stockLevels', 'batches'])
            ->whereIn('id', [$to, $from])
            ->select(['name', 'id', 'can_ship_stock'])
            ->get();

        $this->from = $stores->firstWhere('id', '=', $from);
        $to = $stores->firstWhere('id', '=', $to);

        $this->moveStock = move_stock()
            ->movement($this->movements)
            ->from($this->from)
            ->to($to);
    }

    public function execute(ProductDispatch $dispatch): void
    {
        $this->setup($dispatch);

        try {
            DB::transaction(function (): void {
                $this->items()->each(fn ($item) => $this->process($item));
                $this->movements->execute();
                $this->moveStock->clear();
                $this->updateDispatch();

                success();
            });
        } catch (Throwable $exception) {
            error_notification($exception);
        }
    }

    private function items(): Collection
    {
        return ProductDispatchItem::with(['article'])
            ->whereProductDispatchId($this->dispatch->id)
            ->get();
    }

    /**
     * @throws Throwable
     */
    private function process(ProductDispatchItem $item): void
    {
        $batches = $this->from->batches
            ->where('article_id', '=', $item->article->id)
            ->where('current_units', '>', 0);

        $this->movements = $this->moveStock->batches($batches)
            ->units((int) $item->received_quantity)
            ->article($item->article)
            ->execute();

        //        dd($this->movements);
    }

    /**
     * @throws Throwable
     */
    private function updateDispatch(): void
    {
        $this->dispatch->received_at = now();
        $this->dispatch->updateStatus();
        $this->dispatch->update();
    }
}
