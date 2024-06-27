<?php

namespace App\Actions\Production;

use App\Models\Production\FoodOrder;
use App\Models\Production\ProductDispatch;
use App\Models\Production\Restaurant;
use Throwable;

class DispatchProduct
{
    public function execute(FoodOrder $foodOrder, array $data): void
    {
        try {
            $restaurant = Restaurant::find($data['restaurant_id']);
            $from = $foodOrder->station->defaultStore();
            $to = $restaurant->defaultStore();

            $dispatch = ProductDispatch::create([
                'destination_type' => $restaurant->getMorphClass(),
                'destination_id' => $restaurant->id,
                'dispatched_by' => auth_id(),
                'from_store_id' => $from->id,
                'to_store_id' => $to->id,
                'status' => 'draft',
            ]);

            $dispatch->items()->create([
                'article_id' => $foodOrder->recipe->getAttribute('article_id'),
                'dispatched_quantity' => $foodOrder->produced_portions,
            ]);

            success();
            redirect(get_record_url($dispatch));
        } catch (Throwable $exception) {
            error_notification($exception);
        }

        redirect(get_record_url($foodOrder));
    }
}
