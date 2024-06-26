<?php

namespace App\Actions\Inventory;

use App\Models\Inventory\Article;
use App\Models\Production\RequestedIngredient;
use Exception;
use Illuminate\Support\Facades\DB;
use Throwable;

class CreateDispatchIngredient
{
    private RequestedIngredient $ingredient;

    private Article $article;

    private int $units;

    private int|float $articleCapacity;

    private array $item = [];

    public function execute(
        RequestedIngredient $ingredient,
        Article $article,
        int $units,
        bool $quiet = false
    ): array {
        $this->ingredient = $ingredient;
        $this->article = $article;
        $this->units = $units;

        try {
            $this->validate();

            DB::transaction(function () use ($quiet): void {
                $this->createDispatch();
                $this->updateRemainingQuantity();

                if ( ! $quiet) {
                    success();
                }
            });
        } catch (Throwable $exception) {
            error_notification($exception);
        }

        return $this->item;
    }

    public function createDispatch(): void
    {
        $this->item = [
            'unit_id' => $this->article->getAttribute('unit_id'),
            'food_order_id' => $this->ingredient->food_order_id,
            'requested_ingredient_id' => $this->ingredient->id,
            'store_id' => $this->ingredient->store_id,
            'article_id' => $this->article->id,
            'initial_units' => $this->units,
            'current_units' => $this->units,
            'dispatched_by' => auth_id(),
            'updated_at' => now(),
            'created_at' => now(),
            'status' => 'draft',
        ];
    }

    /**
     * @throws Throwable
     */
    private function validate(): void
    {
        $message = 'You cannot dispatch zero or negative units';
        $check = $this->units <= 0;
        throw_if($check, new Exception($message));

        $remaining = $this->ingredient->remaining_quantity;
        $this->articleCapacityToReference();

        $message = 'You cannot dispatch more than the requested quantity';
        $total = $remaining + $this->article->unit_capacity;
        $check = $this->articleCapacity > $total;
        throw_if($check, new Exception($message));
    }

    /**
     * @throws Throwable
     */
    private function articleCapacityToReference(): void
    {
        $capacity = $this->units * $this->article->unit_capacity;
        $from = $this->article->getAttribute('unit_id');
        $to = $this->ingredient->unit_id;

        $capacity = quantity_converter($from, $to, $capacity);
        $this->articleCapacity = $capacity;
    }

    /**
     * @throws Throwable
     */
    private function updateRemainingQuantity(): void
    {
        $remaining = $this->ingredient->remaining_quantity;
        $capacity = $this->articleCapacity;
        $remaining -= ceil($capacity);

        $this->ingredient->remaining_quantity = max($remaining, 0);
        $this->ingredient->dispatched_quantity += ceil($capacity);
        $this->ingredient->update();
    }
}
