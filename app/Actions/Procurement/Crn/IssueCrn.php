<?php

namespace App\Actions\Procurement\Crn;

use App\Models\Procurement\CreditNote;
use App\Models\Procurement\CreditNoteItem;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Throwable;

class IssueCrn
{
    private CreditNote $crn;

    public function execute(CreditNote $crn): void
    {
        $this->crn = $crn;

        try {
            DB::transaction(function (): void {
                $items = $this->items();
                $items->each(fn ($item) => $this->processItem($item));
                $this->updateEntries($items->sum('total_value'));

                CreditNoteItem::where('units', '<=', 0)
                    ->whereCreditNoteId($this->crn->id)
                    ->delete();

                success('Credit note successfully!');
            });
        } catch (Throwable $exception) {
            error_notification($exception);
        }

        redirect(get_record_url($this->crn));
    }

    /**
     * @throws Throwable
     */
    public function processItem(CreditNoteItem $item): void
    {
        $poItem = $item->purchaseOrderItem;
        $poItem->remaining_units -= $item->units;
        $poItem->ordered_units -= $item->units;
        $poItem->total_value = $poItem->ordered_units * $poItem->price;
        $poItem->update();
    }

    /**
     * @throws Throwable
     */
    private function items(): Collection
    {
        $with = [
            'article:id,name,lifespan_days,valuation_rate,is_reference,team_id',
            'purchaseOrderItem:remaining_units,ordered_units,total_value,price',
        ];

        $items = CreditNoteItem::with($with)
            ->where('units', '>', 0)
            ->whereCreditNoteId($this->crn->id)
            ->get();

        fire(blank($items), 'There are no items to be written off!');

        return $items;
    }

    /**
     * @throws Throwable
     */
    private function updateEntries(float $total): void
    {
        $this->crn->issued_by = auth_id();
        $this->crn->total_value = $total;
        $this->crn->issued_at = now();
        $this->crn->updateStatus();
        $this->crn->update();

        $id = $this->crn->purchase_order_id;
        (new UpdatePurchaseOrder())->execute($id);
    }
}
