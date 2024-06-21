<?php

namespace App\Http\Controllers\Downloads;

use App\Http\Controllers\Controller;
use App\Models\Procurement\GoodsReceivedNote;

class DownloadGRN extends Controller
{
    public function __invoke(string $grn)
    {
        $grn = GoodsReceivedNote::with([
            'supplier', 'items.article', 'items.batch.store',
            'purchaseOrder:code,id,expected_delivery_date', 'items.purchaseOrderItem',
        ])
            ->where('id', '=', $grn)
            ->first();

        $code = $grn->getAttribute('code');
        $pdf = $grn->toPDF();

        // todo: fix css and image
        //        return  view('pdf.procurement.lpo', ['purchaseOrder' => $purchaseOrder]);

        return $pdf->download($code . '-' . now() . '.pdf');
    }
}
