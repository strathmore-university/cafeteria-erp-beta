<?php

namespace App\Actions\Procurement\Grn\During;

use App\Models\Procurement\GoodsReceivedNote;
use Throwable;

class UpdateGrn
{
    /**
     * @throws Throwable
     */
    public function execute(
        GoodsReceivedNote $grn,
        array $data,
        float $total
    ): void {
        $invoice = $data['invoice_number'] ?? null;
        $deliveryNote = $data['delivery_note_number'] ?? null;
        $invoicedAt = tannery(filled($invoice), now(), null);

        $grn->delivery_note_number = $deliveryNote;
        $grn->invoice_number = $invoice;
        $grn->invoiced_at = $invoicedAt;
        $grn->received_by = auth_id();
        $grn->total_value = $total;
        $grn->received_at = now();
        $grn->updateStatus();
        $grn->update();
    }
}
