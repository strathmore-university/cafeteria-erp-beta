<?php

namespace App\Actions\Procurement\Grn\After;

use App\Models\Procurement\PurchaseOrder;
use Throwable;

class UpdatePurchaseOrderIfComplete
{
    /**
     * @throws Throwable
     */
    public function execute(int $id): void
    {
        $purchaseOrder = PurchaseOrder::whereId($id)
            ->select(['id', 'is_fulfilled', 'delivered_at', 'status'])
            ->first();

        match ($purchaseOrder->isFulfilled()) {
            true => $this->update($purchaseOrder),
            default => null,
        };
    }

    /**
     * @throws Throwable
     */
    private function update(PurchaseOrder $order): void
    {
        $order->delivered_at = now();
        $order->is_fulfilled = true;
        $order->updateStatus();
        $order->update();
    }
}
