<?php

namespace App\Actions\Inventory;

use App\Models\Inventory\Article;
use App\Models\Production\DispatchedIngredient;
use App\Models\Production\FoodOrder;
use App\Models\Production\RequestedIngredient;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Throwable;

class PopulateDispatch
{
    private FoodOrder $foodOrder;

    private Collection $items;

    public function execute(FoodOrder $foodOrder): void
    {
        $this->foodOrder = $foodOrder;
        $this->items = collect();

        // todo: check store first

        try {
            DB::transaction(function (): void {
                $ingredients = RequestedIngredient::with(['article.descendants'])
                    ->whereFoodOrderId($this->foodOrder->id)
                    ->get();

                $ingredients->each(function (RequestedIngredient $ingredient): void {
                    [$sameUnit, $otherUnits] = $this->createGroups($ingredient);

                    $this->processGroup($sameUnit, $ingredient);
                    $this->processGroup($otherUnits, $ingredient);
                });

                $items = $this->items->toArray();
                DispatchedIngredient::insert($items);
            });

            success();
        } catch (Throwable $exception) {
            error_notification($exception);
        }
    }

    private function createGroups(RequestedIngredient $ingredient): array
    {
        $reference = $ingredient->article;
        $descendants = $reference->descendants;

        $sameUnit = $descendants->filter(
            function (Article $article) use ($ingredient) {
                $one = $ingredient->getAttribute('unit_id');
                $two = $article->getAttribute('unit_id');

                return $one === $two;
            }
        );

        $otherUnits = $descendants->diff($sameUnit);

        return [$sameUnit, $otherUnits];
    }

    private function processGroup(
        Collection $group,
        RequestedIngredient $item
    ): void {
        $group->each(
            /**
             * @throws Throwable
             */
            fn (Article $article) => $this->attemptDispatch($article, $item)
        );
    }

    /**
     * @throws Throwable
     */
    private function attemptDispatch(
        Article $article,
        RequestedIngredient $item
    ): void {
        $remaining = $item->getAttribute('remaining_quantity');
        if ($remaining === 0) {
            return;
        }

        $units = $article->unitsToDispatch($remaining, parentUnitId: $item->unit_id);
        $dispatch = new CreateDispatchIngredient();
        $item = $dispatch->execute($item, $article, $units, true);
        $this->items->push($item);
    }
}
