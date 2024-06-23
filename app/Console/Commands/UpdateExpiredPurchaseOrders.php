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

        $purchaseOrders = PurchaseOrder::withoutGlobalScopes()
            ->where('expires_at', '<', now())
            ->where('status', '!=', 'fulfilled')
            ->get();

        $this->log('Found ' . $purchaseOrders->count() . ' expired purchase orders');

        $purchaseOrders->each(
            /**
             * @throws Throwable
             */
            function (PurchaseOrder $purchaseOrder): void {
                DB::transaction(function () use ($purchaseOrder): void {
                    $code = $purchaseOrder->getAttribute('code');
                    $this->log('Updating purchase order: ' . $code);

                    $crn = $purchaseOrder->generateCrn();
                    $crn->issueCrn();

                    $purchaseOrder->update([
                        'status' => 'expired',
                        'is_lpo' => false,
                    ]);

                    // todo: send notification to creator
                    //                    $purchaseOrder->creator->notify(
                    //                        'Purchase order '.$code.' has expired',
                    //                        'Your purchase order '.$code.' has expired.
                    //                        A credit note has been generated for it.'
                    //                    );

                    $this->log('Purchase order: ' . $code . ' updated');
                });
            }
        );
    }

    private function log(string $message): void
    {
        $this->info($message);
        info($message);
    }
}
