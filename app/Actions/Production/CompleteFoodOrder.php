<?php

namespace App\Actions\Production;

use App\Models\Production\FoodOrder;
use Throwable;

class CompleteFoodOrder
{
    private FoodOrder $foodOrder;

    public function execute(FoodOrder $foodOrder, array $data):void
    {
        $this->foodOrder = $foodOrder;

        try {
            // reduce stock

            // calculate cost of production

            // assess the performance of the produced portion



            $quantity = $data['produced_portions'];
            $productionCost = $this->calculateProductionCost();

            $this->foodOrder->produced_portions = $quantity;
            $this->foodOrder->production_cost = $productionCost;
            $this->foodOrder->unit_cost = $productionCost / $quantity;

            success();
        } catch (Throwable $exception) {
            error_notification($exception);
        }
    }

    private function calculateProductionCost(): float
    {

    }
}