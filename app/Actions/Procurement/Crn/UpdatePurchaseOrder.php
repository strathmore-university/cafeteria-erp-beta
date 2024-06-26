<?php

namespace App\Actions\Procurement\Crn;

use App\Models\Procurement\PurchaseOrder;
use Throwable;

class UpdatePurchaseOrder
{
    /**
     * @throws Throwable
     */
    public function execute(int $id): void
    {
        $select = [
            'id', 'is_fulfilled', 'delivered_at', 'status', 'total_value',
        ];
        $order = PurchaseOrder::whereId($id)->select($select)->first();
        $order->total_value = $order->items->sum();
        $order = match ($order->isFulfilled()) {
            true => $this->fulfil($order),
            false => $order,
        };

        $order->update();
    }

    /**
     * @throws Throwable
     */
    private function fulfil(PurchaseOrder $order): PurchaseOrder
    {
        $order->delivered_at = now();
        $order->is_fulfilled = true;
        $order->updateStatus();

        return $order;
    }
}
