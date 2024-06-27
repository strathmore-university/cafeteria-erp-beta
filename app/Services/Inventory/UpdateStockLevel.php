<?php

namespace App\Services\Inventory;

use App\Models\Inventory\StockLevel;
use App\Models\Inventory\Store;
use Exception;
use Illuminate\Support\Facades\DB;
use Throwable;

class UpdateStockLevel
{
    private bool $sale = false;

    private bool $add = true;

    private int $articleId;

    private int $storeId;

    private ?Store $store = null;

    private int $teamId;

    private int $units;

    public function article(int $articleId): self
    {
        $this->articleId = $articleId;

        return $this;
    }

    public function store(int $storeId): self
    {
        $this->storeId = $storeId;

        return $this;
    }

    public function useStore(Store $store): self
    {
        $this->store = $store;

        return $this;
    }

    public function team(int $teamId): self
    {
        $this->teamId = $teamId;

        return $this;
    }

    public function units(int $units): self
    {
        $this->units = $units;

        return $this;
    }

    public function sale(bool $sale): self
    {
        $this->sale = $sale;

        return $this;
    }

    public function increase(): self
    {
        $this->add = true;

        return $this;
    }

    public function reduce(): self
    {
        $this->add = false;

        return $this;
    }

    /**
     * @throws Throwable
     */
    public function index(): void
    {
        DB::transaction(function (): void {
            $this->units *= $this->add ? 1 : -1;
            $stockLevel = $this->fetchStockLevel();

            match (filled($stockLevel)) {
                true => $this->update($stockLevel),
                false => $this->create(),
            };
        });
    }

    private function fetchStockLevel()
    {
        $id = $this->articleId;

        if (filled($this->store)) {
            return $this->store->stockLevels
                ->where('team_id', '=', $this->teamId)
                ->where('article_id', '=', $id);
        }

        return StockLevel::where('article_id', '=', $id)
            ->where('store_id', '=', $this->storeId)
            ->where('team_id', '=', $this->teamId)
            ->select(['id', 'current_units', 'previous_units'])
            ->first();
    }

    /**
     * @throws Throwable
     */
    private function update(StockLevel $stockLevel): void
    {
        $condition = $this->units > $stockLevel->current_units;
        $condition = and_check( ! $this->add, $condition);
        $message = 'Insufficient stock';
        throw_if($condition, new Exception($message));

        $stockLevel->previous_units = $stockLevel->current_units;
        $stockLevel->current_units += $this->units;
        $stockLevel->update();
    }

    private function create(): void
    {
        StockLevel::create([
            'store_id' => $this->store?->id ?? $this->storeId,
            'article_id' => $this->articleId,
            'current_units' => $this->units,
            'is_sold_stock' => $this->sale,
            'team_id' => $this->teamId,
            'previous_units' => 0,
        ]);
    }
}
