<?php

namespace App\Console\Commands;

use App\Models\Procurement\PurchaseOrder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Throwable;

class UpdateExpiredPurchaseOrders extends Command
{
    protected $signature = 'update:expired-purchase-orders';

    protected $description = 'Update expired purchase orders 
        and auto generates credit notes for them';

    public function handle(): void
    {
        $this->log('Updating expired purchase orders');

        $purchaseOrders = PurchaseOrder::where('expires_at', '<', now())
            ->where('status', '!=', 'fulfilled')
            ->get();

        $this->log('Found '.$purchaseOrders->count().' expired purchase orders');

        $purchaseOrders->each(
        /**
         * @throws Throwable
         */
            function (PurchaseOrder $purchaseOrder) {
                DB::transaction(function () use ($purchaseOrder) {
                    $code = $purchaseOrder->getAttribute('code');
                    $this->log('Updating purchase order: '.$code);

                    $crn = $purchaseOrder->generateCrn();
                    $crn->issueCrn();

                    $purchaseOrder->update([
                        'status' => 'expired',
                        'is_lpo' => false
                    ]);
                    $this->log('Purchase order: '.$code.' updated');
                });
            });
    }

    private function log(string $message): void
    {
        $this->info($message);
        info($message);
    }
}
