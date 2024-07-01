<?php

namespace App\Concerns\Retail\Frontend\Items;

use App\Models\Production\SellingPortion;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

trait SaleItems
{
    use AlterItem, LoadSellingPortions;

    public Collection $allSellingPortions;

    public Collection $sellingPortions;

    public string $searchPortions = '';

    public string $itemCode = '';

    public Collection $saleItems;

    public function updatedSearchPortions(): void
    {
        match (filled($this->searchPortions)) {
            false => $this->sellingPortions = $this->allSellingPortions,
            true => $this->search(),
        };
    }

    public function addFromSelect(SellingPortion $portion): void
    {
        $this->sellingPortions = $this->allSellingPortions;
        $this->dispatch('close-items-modal');
        $this->addSaleItem($portion);
    }

    public function addItemByCode(): void
    {
        $portion = $this->allSellingPortions
            ->firstWhere('code', '=', $this->itemCode);

        match (blank($portion)) {
            false => $this->addSaleItem($portion),
            true => $this->openSearch(),
        };
    }

    protected function search(?string $search = null): void
    {
        $search = $search ?? $this->searchPortions;
        $items = $this->allSellingPortions;

        $items = $items->filter(function ($portion) use ($search) {
            $name = $portion->menuItem->getAttribute('name');
            $one = Str::contains(mb_strtolower($name), mb_strtolower($search));
            $two = Str::contains($portion->code, $search);

            return or_check($one, $two);
        });

        $this->sellingPortions = $items;
    }

    private function addSaleItem(SellingPortion $portion): void
    {
        $existingItem = $this->saleItems
            ->firstWhere('code', $portion->code);

        match (filled($existingItem)) {
            true => $this->increase($portion->code),
            false => $this->addNewItem($portion)
        };

        $this->searchPortions = $this->itemCode = '';
    }

    private function addNewItem($portion): void
    {
        $this->saleItems->push([
            'menu_item_id' => $portion->menu_item_id,
            'name' => $portion->menuItem->name,
            'price' => $portion->selling_price,
            'code' => $portion->code,
            'units' => 1,
        ]);

        $this->saleTotal += $portion->selling_price;
        $this->recalculate();
    }

    private function openSearch(): void
    {
        $this->searchPortions = $this->itemCode;
        $this->dispatch('open-items-modal');
        $this->search($this->itemCode);
    }
}
