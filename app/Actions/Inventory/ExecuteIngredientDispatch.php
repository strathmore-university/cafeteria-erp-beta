<?php

namespace App\Actions\Inventory;

use App\Models\Inventory\Article;
use App\Models\Inventory\Batch;
use App\Models\Inventory\StockMovement;
use App\Models\Inventory\Store;
use App\Models\Production\DispatchedIngredient;
use App\Models\Production\FoodOrder;
use App\Models\Production\RequestedIngredient;
use App\Models\Production\Station;
use App\Support\Inventory\UpdateStockLevel;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Throwable;

class ExecuteIngredientDispatch
{
    private DispatchedIngredient $item;

    private Collection $movements;

    private FoodOrder $foodOrder;

    private Station $station;

    private Article $article;

    //    private int $batchCount;
    private Store $store;

    private function setUp(FoodOrder $foodOrder): void
    {
        $id = $foodOrder->station_id;
        $this->station = Station::select(['id', 'name'])->find($id);
        $this->store = $this->station->defaultStore();
        //        $this->batchCount = get_next_id(new Batch()); // todo: doing this can cause exceptions if another process attempts tp create batches at the same time
        $this->foodOrder = $foodOrder;
        $this->movements = collect();
    }

    public function execute(FoodOrder $foodOrder): void
    {
        try {
            $this->setUp($foodOrder);

            DB::transaction(function (): void {
                $this
                    ->fetchItems()
                    ->each(fn ($item) => $this->dispatch($item));

                StockMovement::insert($this->movements->toArray());

                $this->updateRecords();
            });

            success();
        } catch (Throwable $exception) {
            error_notification($exception);
        }
    }

    private function fetchItems(): Collection
    {
        $id = $this->foodOrder->id;

        return DispatchedIngredient::with('article.batches')
            ->whereFoodOrderId($id)
            ->get();
    }

    /**
     * @throws Throwable
     */
    private function dispatch(DispatchedIngredient $item): void
    {
        $original = (int) $item->initial_units;
        $remaining = $original;

        // todo: create another relation for article batches
        $this->item = $item;
        $this->article = $item->article;
        $this->article->batches->each(function (Batch $batch) use (&$remaining): void {
            if ($remaining === 0) {
                return;
            }

            $units = (int) match ($remaining > $batch->current_units) {
                true => $batch->current_units,
                false => $remaining,
            };

            $newBatch = $this->createBatch($batch, $units);
            $this->createMovement($newBatch, $units);
            $this->createMovement($batch, -$units);

            $batch->previous_units = $batch->current_units;
            $batch->current_units -= $units;
            $batch->update();

            //            $this->batchCount++;
            $remaining -= $units;
        });

        $teamId = $this->article->getAttribute('team_id');
        (new UpdateStockLevel())->team($teamId)
            ->article($this->article->id)
            ->store($this->store->id)
            ->units($original)
            ->index();

        (new UpdateStockLevel())->team($teamId)
            ->article($this->article->id)
            ->store(Store::find(1)->id) // todo: remove
            ->units($original)
            ->reduce()
            ->index();

        //        $item->current_units = $item->initial_units;
        //        $item->setAttribute('status', 'dispatched');
        //        $item->update();
    }

    //    private function fetchBatches($item): Collection
    //    {
    //        $id = $item->article_id;
    //
    //        return Batch::where('article_id', '=', $id)->oldest()
    ////            ->whereDate('expires_at', '>', now())
    ////            ->whereNull('owner_id') // todo: add
    //            ->get();
    //    }

    private function createBatch(Batch $batch, int $units): Batch
    {
        $code = $this->foodOrder->getAttribute('code');
        $name = $this->article->getAttribute('name');
        $teamId = $batch->getAttribute('team_id');

        $narration = build_string([
            'Received' . ' ' . abs($units) . ' units of ' . $name .
            ' for food order code:' . $code . ' at station ' .
            $this->station->getAttribute('name'),
        ]);

        $newBatch = Batch::create([
            'weighted_cost' => $this->article->valuation_rate,
            //            'batch_number' => 'BATCH-'.$this->batchCount,
            'owner_type' => $this->item->getMorphClass(),
            'expires_at' => $batch->expires_at,
            'article_id' => $this->article->id,
            'store_id' => $this->store->id,
            'owner_id' => $this->item->id,
            'initial_units' => $units,
            'narration' => $narration,
            'team_id' => $teamId,
        ]);

        // todo: setting up the nested set results in soooo many queries. Review this!
        $batch->appendNode($newBatch);

        return $batch;
    }

    private function narration(int|float $units): string
    {
        $name = $this->article->getAttribute('name');
        $code = $this->foodOrder->getAttribute('code');
        $action = $units > 0 ? 'Moving' : 'Dispatching';

        return build_string([
            $action . ' ' . abs($units) . ' units of ' . $name .
            ' for food order code:' . $code . ' to station ' .
            $this->station->getAttribute('name'),
        ]);
    }

    private function createMovement(
        Batch $batch,
        float $units
    ): void {
        $this->movements->push([
            'stock_value' => abs($units) * $this->article->valuation_rate,
            'article_id' => $batch->getAttribute('article_id'),
            'weighted_cost' => $this->article->valuation_rate,
            'team_id' => $batch->getAttribute('team_id'),
            'narration' => $this->narration($units),
            'store_id' => $this->store->id,
            'batch_id' => $batch->id,
            'units' => $units,
        ]);
    }

    private function updateRecords(): void
    {
        $id = $this->foodOrder->id;
        RequestedIngredient::whereFoodOrderId($id)->update([
            'dispatched_at' => now(),
        ]);

        DispatchedIngredient::whereFoodOrderId($id)->update([
            'status' => 'dispatched',
        ]);

        $this->foodOrder->ingredients_dispatched_by = auth_id();
        $this->foodOrder->ingredients_dispatched_at = now();
        $this->foodOrder->update();
    }
}
