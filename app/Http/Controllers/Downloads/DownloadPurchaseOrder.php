<?php

namespace App\Http\Controllers\Downloads;

use App\Http\Controllers\Controller;
use App\Models\Procurement\PurchaseOrder;

class DownloadPurchaseOrder extends Controller
{
    public function __invoke(string $purchaseOrder)
    {
        $purchaseOrder = PurchaseOrder::with(['supplier', 'items.article'])
            ->where('id', '=', $purchaseOrder)
            ->first();

        $code = $purchaseOrder->getAttribute('code');
        $pdf = $purchaseOrder->toPDF();

        // todo: fix css and image
        //        return  view('pdf.procurement.lpo', ['purchaseOrder' => $purchaseOrder]);

        return $pdf->download($code . '-' . now() . '.pdf');
    }
}
