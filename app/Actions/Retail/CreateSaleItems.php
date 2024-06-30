<?php

namespace App\Actions\Retail;

use App\Models\Production\SellingPortion;
use App\Models\Retail\Sale;
use App\Models\Retail\SaleItem;
use Illuminate\Support\Collection;

class CreateSaleItems
{
    public function execute(Sale $sale, Collection $items): void
    {
        $sellingPortions = $this->fetchSellingPortions($items);
        $saleItems = collect();
        $items->each(function ($item) use ($sale, $saleItems, $sellingPortions): void {
            $portion = $sellingPortions->firstWhere('code', $item['code']);

            $saleItems->push([
                'total_amount' => $portion->selling_price * $item['units'],
                'narration' => $this->fetchNarration($item, $portion),
                'team_id' => $sale->getAttribute('team_id'),
                'unit_id' => $portion->getAttribute('unit_id'),
                'menu_item_id' => $portion->menu_item_id,
                'sale_price' => $portion->selling_price,
                'selling_portion_id' => $portion->id,
                //                'unit_cost' => $item['units'],
                'units' => $item['units'],
                'sale_id' => $sale->id,
            ]);
        });

        SaleItem::insert($saleItems->toArray());
    }

    private function fetchSellingPortions(Collection $items): Collection
    {
        return SellingPortion::with('menuItem')
            ->whereIn('code', $items->pluck('code')->toArray())
            ->get();
    }

    private function fetchNarration(
        array $item,
        SellingPortion $portion
    ): string {
        return build_string([
            $item['units'] . ' x ' . $portion->menuItem->getAttribute('name')
            . ' (' . $portion->unit->getAttribute('name') . ')',
        ]);
    }
}
