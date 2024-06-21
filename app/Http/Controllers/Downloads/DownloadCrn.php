<?php

namespace App\Http\Controllers\Downloads;

use App\Http\Controllers\Controller;
use App\Models\Procurement\CreditNote;

class DownloadCrn extends Controller
{
    public function __invoke(string $crn)
    {
        $crn = CreditNote::with([
            'purchaseOrder:code,id', 'supplier', 'items.article',
        ])
            ->where('id', '=', $crn)
            ->first();

        $code = $crn->getAttribute('code');
        $pdf = $crn->toPDF();

        return $pdf->download($code.'-'.now().'.pdf');
    }
}
