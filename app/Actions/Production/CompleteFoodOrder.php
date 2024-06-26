<?php

namespace App\Actions\Production;

use App\Models\Inventory\Batch;
use App\Models\Inventory\StockMovement;
use App\Models\Inventory\Store;
use App\Models\Production\DispatchedIngredient;
use App\Models\Production\FoodOrder;
use App\Models\Production\FoodOrderByProducts;
use App\Models\Production\Recipe;
use App\Models\Production\Station;
use App\Services\Inventory\UpdateStockLevel;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Throwable;

class CompleteFoodOrder
{
    private FoodOrder $foodOrder;

    private Collection $movements;

    private Station $station;

    private Store $store;

    private int $teamId;

    public function execute(FoodOrder $foodOrder, array $data): void
    {
        $start = start_watch();
        $id = $foodOrder->station_id;

        $this->foodOrder = $foodOrder;
        $this->movements = collect();
        $this->station = Station::select(['id', 'name'])->find($id);
        $this->teamId = $foodOrder->getAttribute('team_id');
        $this->store = $this->station->defaultStore();

        try {
            DB::transaction(function () use ($data): void {
                $items = DispatchedIngredient::with(['batches', 'article'])
                    ->whereFoodOrderId($this->foodOrder->id)
                    ->get();

                $items->each(function (DispatchedIngredient $ingredient): void {
                    $article = $ingredient->article;
                    $batches = $ingredient->batches;

                    $original = (int) $ingredient->used_units;
                    $unitToReduce = $original;

                    $batches->each(function (Batch $batch) use ($article, &$unitToReduce): void {
                        if ($unitToReduce === 0) {
                            return;
                        }

                        $units = match ($unitToReduce > $batch->current_units) {
                            true => -$batch->current_units,
                            false => -$unitToReduce,
                        };

                        $code = $this->foodOrder->getAttribute('code');
                        $name = $article->getAttribute('name');

                        $narration = build_string([
                            'Consuming ' . abs($units) . ' units of ' . $name .
                            ' in food order code:' . $code . ' at station:' .
                            $this->station->getAttribute('name'),
                        ]);

                        $this->movements->push([
                            'stock_value' => abs($units) * $article->valuation_rate,
                            'article_id' => $batch->getAttribute('article_id'),
                            'weighted_cost' => $article->valuation_rate,
                            'store_id' => $this->store->id,
                            'team_id' => $this->teamId,
                            'narration' => $narration,
                            'batch_id' => $batch->id,
                            'units' => $units,
                        ]);

                        $batch->previous_units = $batch->current_units;
                        $batch->current_units += $units;
                        $batch->update();

                        $unitToReduce += $units;
                    });

                    (new UpdateStockLevel())->team($this->teamId)
                        ->store($this->store->id)
                        ->article($article->id)
                        ->units($original)
                        ->reduce()
                        ->index();

                    $rate = $article->valuation_rate;
                    $ingredient->cost_of_production = $original * $rate;
                    $ingredient->update();
                });

                $byProducts = FoodOrderByProducts::with('article')
                    ->whereFoodOrderId($this->foodOrder->id)
                    ->get();

                $byProducts->each(function ($byProduct): void {
                    $article = $byProduct->article;
                    $code = $this->foodOrder->getAttribute('code');
                    $name = $article->getAttribute('name');

                    $narration = build_string([
                        'Increasing ' . $byProduct->quantity . ' portions of by-product '
                        . $name . ' produced during execution of food order code:' .
                        $code . ' at station:' . $this->station->getAttribute('name'),
                    ]);

                    $batch = Batch::create([
                        'weighted_cost' => $byProduct->article->valuation_rate ?? 0,
                        'article_id' => $byProduct->getAttribute('article_id'),
                        'initial_units' => $byProduct->quantity,
                        //                    'expires_at' => now()->addDays(7), todo: reiew expiry dates
                        'store_id' => $this->store->id,
                        'team_id' => $this->teamId,
                        'narration' => $narration,
                        'previous_units' => 0,
                    ]);

                    // todo: what is the value of the by-product
                    $this->movements->push([
                        'stock_value' => $byProduct->quantity * $article->valuation_rate,
                        'article_id' => $batch->getAttribute('article_id'),
                        'weighted_cost' => $article->valuation_rate ?? 0,
                        'units' => $byProduct->quantity,
                        'store_id' => $this->store->id,
                        'team_id' => $this->teamId,
                        'narration' => $narration,
                        'batch_id' => $batch->id,
                    ]);

                    (new UpdateStockLevel())->team($this->teamId)
                        ->units((int) $byProduct->quantity)
                        ->store($this->store->id)
                        ->article($article->id)
                        ->index();
                });

                $id = $this->foodOrder->recipe_id;
                $recipe = Recipe::with('product')->find($id);
                $product = $recipe->product;

                $produced = $data['produced_portions'];
                $this->foodOrder->produced_portions = $produced;

                $narration = build_string([
                    'Produced ' . $produced . ' portions of ' .
                    $product->getAttribute('name') . ' from food order code:' .
                    $this->foodOrder->getAttribute('code') . ' at station:' .
                    $this->station->getAttribute('name'),
                ]);

                $batch = Batch::create([
                    'weighted_cost' => $product->valuation_rate ?? 0,
                    'article_id' => $product->id,
                    'initial_units' => $produced,
                    //                    'expires_at' => now()->addDays(7), todo: reiew expiry dates
                    'store_id' => $this->store->id,
                    'team_id' => $this->teamId,
                    'narration' => $narration,
                    'previous_units' => 0,
                ]);

                $this->movements->push([
                    // todo: what is the value of the by-product
                    'stock_value' => $produced * ($product->valuation_rate ?? 0),
                    'weighted_cost' => $product->valuation_rate ?? 0,
                    'store_id' => $this->store->id,
                    'article_id' => $product->id,
                    'team_id' => $this->teamId,
                    'narration' => $narration,
                    'batch_id' => $batch->id,
                    'units' => $produced,
                ]);

                (new UpdateStockLevel())->team($this->teamId)
                    ->store($this->store->id)
                    ->units((int) $produced)
                    ->article($product->id)
                    ->index();

                StockMovement::insert($this->movements->toArray());

                $ids = $items->pluck('batches')->collapse()->pluck('id');
                Batch::whereIn('id', $ids)->update([
                    'owner_type' => null, 'owner_id' => null,
                ]);

                $this->assessProduction($recipe);

                success();
            });
        } catch (Throwable $exception) {
            error_notification($exception);
        }

        end_watch($start);
    }

    private function assessProduction(Recipe $recipe): void
    {
        $id = $this->foodOrder->id;
        $cost = DispatchedIngredient::whereFoodOrderId($id)
            ->sum('cost_of_production');

        $produced = $this->foodOrder->produced_portions;
        $expected = $this->foodOrder->expected_portions;
        $rating = $produced / $expected * 100;

        $surplusRate = $expected * $recipe->surplus_tolerance / 100 / $recipe->yield;
        $surplusRate += 1;
        $allowedSurplus = (int) ceil($expected * $surplusRate);

        $wastageRate = $expected * $recipe->wastage_tolerance / 100 / $recipe->yield;
        $wastageRate = abs($wastageRate - 1);
        $allowedWastage = (int) ceil($expected * $wastageRate);

        $isSurplus = $produced > $allowedSurplus;
        $isWastage = $produced < $allowedWastage;

        if (or_check($isSurplus, $isWastage)) {
            $status = $isSurplus ? 'surplus' : 'wastage' . ' detected';

            $this->foodOrder->setAttribute('status', $status);
            $this->foodOrder->is_flagged = true;

            // todo: if flagged do i create a review and prevent dispatch
        } else {
            $this->foodOrder->setAttribute('status', 'prepared');
        }

        $this->foodOrder->expected_portions_upper_limit = $allowedSurplus;
        $this->foodOrder->expected_portions_lower_limit = $allowedWastage;
        $this->foodOrder->unit_cost = $cost / $produced;
        $this->foodOrder->performance_rating = $rating;
        $this->foodOrder->production_cost = $cost;
        $this->foodOrder->prepared_by = auth_id();
        $this->foodOrder->prepared_at = now();
        $this->foodOrder->update();

        // todo: create product dispatch
    }
}
