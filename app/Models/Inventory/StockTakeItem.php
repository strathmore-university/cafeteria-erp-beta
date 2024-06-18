<?php

namespace App\Models\Inventory;

use App\Models\Procurement\PriceQuote;
use App\Support\Inventory\UpdateStockLevel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use Throwable;

class StockTakeItem extends Model
{
    protected $guarded = [];

    private int $articleId;

    private int $storeId;

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    /**
     * @throws Throwable
     */
    public function adjustStock(): void
    {
        DB::transaction(function (): void {
            match ($this->actual_units === $this->current_units) {
                default => $this->attemptAdjustment(),
                true => null,
            };
        });
    }

    /**
     * @throws Throwable
     */
    public function increaseStock(): void
    {
        $current = $this->current_units;
        $difference = (int) ($this->actual_units - $current);
        $narration = $this->fetchNarration($difference);
        $price = $this->fetchPriceQuote();

        $batch = Batch::create([
            'store_id' => $this->getAttribute('store_id'),
            'owner_type' => $this->store->getMorphClass(),
            'article_id' => $this->articleId,
            'initial_units' => $difference,
            'owner_id' => $this->storeId,
            'weighted_cost' => $price,
            'narration' => $narration,
            'team_id' => team_id(),
        ]);

        $this->createMovement($batch->id, $difference, $price);
        $this->updateStockLevel($difference)->index();
    }

    public function reduceStock(): void
    {
        $current = $this->current_units;
        $remaining = (int) ($current - $this->actual_units);

        $select = [
            'id', 'current_units', 'weighted_cost',
            'previous_units', 'depleted_at',
        ];
        $batches = Batch::where('article_id', $this->articleId)
            ->where('store_id', $this->storeId)
            ->select($select)
            ->get();

        $batches->each(
            /**
             * @throws Throwable
             */
            function (Batch $batch) use (&$remaining): void {
                if ($remaining <= 0) {
                    return;
                }

                $remaining = $this->reduceBatch($batch, $remaining);
                info((string) $remaining);
            }
        );
    }

    /**
     * @throws Throwable
     */
    private function attemptAdjustment(): void
    {
        $this->articleId = $this->getAttribute('article_id');
        $this->storeId = $this->getAttribute('store_id');

        $condition = $this->actual_units < $this->current_units;
        match ($condition) {
            true => $this->reduceStock(),
            false => $this->increaseStock(),
        };
    }

    private function fetchPriceQuote(): float
    {
        $id = $this->articleId;

        return PriceQuote::where('article_id', $id)
            ->select('price')
            ->latest()
            ->first()
            ->price;
    }

    /**
     * @throws Throwable
     */
    private function reduceBatch(Batch $batch, int $remaining): int
    {
        $check = $remaining >= $batch->current_units;
        $price = $batch->weighted_cost;
        $unitToReduce = match ($check) {
            true => (int) -$batch->current_units,
            default => -$remaining,
        };

        $batch->current_units = match ($check) {
            default => $batch->current_units + $unitToReduce,
            true => 0,
        };
        $batch->update();

        $this->createMovement($batch->id, $unitToReduce, $price);
        $this->updateStockLevel($unitToReduce)->index();
        $remaining += $unitToReduce;

        return $remaining;
    }

    private function fetchNarration(int $units): string
    {
        $condition = $units > 0;
        $action = tannery($condition, 'increment', 'decrement');

        return build_string([
            'Stock-take ' . $action . ' of', $units, 'units of',
            $this->article->getAttribute('name'), 'in',
            $this->store->getAttribute('name'), 'against stock-take id: ',
            $this->stock_take_id,
        ]);
    }

    private function createMovement(
        int $batchId,
        int $units,
        float $weighedCost
    ): void {
        $narration = $this->fetchNarration($units);

        StockMovement::create([
            'article_id' => $this->articleId,
            'weighted_cost' => $weighedCost,
            'store_id' => $this->storeId,
            'narration' => $narration,
            'batch_id' => $batchId,
            'team_id' => team_id(),
            'units' => $units,
        ]);
    }

    /**
     * @throws Throwable
     */
    private function updateStockLevel(int $units): UpdateStockLevel
    {
        return update_stock_level()->team(team_id())
            ->article($this->articleId)
            ->store($this->storeId)
            ->units($units);
    }
}
