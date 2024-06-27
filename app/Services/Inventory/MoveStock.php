<?php

namespace App\Services\Inventory;

use App\Models\Inventory\Article;
use App\Models\Inventory\Batch;
use App\Models\Inventory\Store;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Throwable;

class MoveStock
{
    private ?CreateMovements $movements = null;

    private ?Collection $batches = null;

    private bool $shouldClear = false;

    private ?Article $article = null;

    private ?int $originalUnits = null;

    private ?Model $owner = null;

    private ?int $remainingUnits = null;

    private ?Store $from = null;

    private ?Store $to = null;

    public function batches(Collection $batches): self
    {
        $this->batches = $batches;

        return $this;
    }

    public function units(int $units): self
    {
        $this->remainingUnits = $units;

        $this->originalUnits = $units;

        return $this;
    }

    public function movement(CreateMovements $movements): self
    {
        $this->movements = $movements;

        return $this;
    }

    public function from(Store $from): self
    {
        $this->from = $from;

        return $this;
    }

    public function to(Store $to): self
    {
        $this->to = $to;

        return $this;
    }

    public function owner(Model $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    public function article(Article $article): self
    {
        $this->article = $article;

        return $this;
    }

    public function empty(bool $value): self
    {
        $this->shouldClear = $value;

        return $this;
    }

    /**
     * @throws Throwable
     */
    public function execute(): CreateMovements
    {
        $this->validate();

        $this->batches->each(fn ($batch) => $this->attempt($batch));

        $update = update_stock_level()
            ->team($this->article->getAttribute('team_id'))
            ->units($this->originalUnits)
            ->article($this->article->id);

        $update->store($this->to->id)->index();
        $update->store($this->from->id)->reduce()->index();

        return match ($this->shouldClear) {
            true => $this->clearAndReturn(),
            false => $this->movements,
        };
    }

    public function clear(): void
    {
        $this->movements = $this->batches = $this->article = null;
        $this->remainingUnits = $this->originalUnits = null;
        $this->owner = $this->from = $this->to = null;
        $this->shouldClear = false;
    }

    /**
     * @throws Throwable
     */
    private function attempt(Batch $batch): void
    {
        match ($this->remainingUnits === 0) {
            false => $this->processBatch($batch),
            true => null,
        };
    }

    /**
     * @throws Throwable
     */
    private function processBatch(Batch $batch): void
    {
        $check = $this->remainingUnits > $batch->current_units;
        $units = (int) match ($check) {
            true => $batch->current_units,
            false => $this->remainingUnits,
        };

        $newBatch = $this->createBatch($batch, $units);
        $this->createMovement($newBatch, $units);
        $this->updateBatch($batch, -$units);

        $this->remainingUnits -= $units;
    }

    private function clearAndReturn(): ?CreateMovements
    {
        $movements = $this->movements;

        $this->clear();

        return $movements;
    }

    /**
     * @throws Throwable
     */
    private function createBatch(Batch $batch, int $units): Batch
    {
        $narration = build_string([
            'Created to facilitate movement of', $this->originalUnits,
            'units from', $this->from->getAttribute('name'), 'to',
            $this->to->getAttribute('name'),
        ]);

        // todo: setting up the nested set results
        // in soooo many queries. Review this!

        return Batch::create([
            'weighted_cost' => $this->article->valuation_rate,
            'owner_type' => $this->owner?->getMorphClass(),
            'owner_id' => $this->owner?->getKey(),
            'expires_at' => $batch->expires_at,
            'article_id' => $this->article->id,
            'store_id' => $this->to->id,
            'initial_units' => $units,
            'parent_id' => $batch->id,
            'narration' => $narration,
        ]);
    }

    private function createMovement(Batch $batch, int $units): void
    {
        $storeId = tannery($units > 0, $this->to->id, $this->from->id);

        $this->movements->add([
            'stock_value' => abs($units) * $this->article->valuation_rate,
            'team_id' => $this->article->getAttribute('team_id'),
            'weighted_cost' => $this->article->valuation_rate,
            'narration' => $this->narration($units),
            'article_id' => $this->article->id,
            'batch_id' => $batch->id,
            'store_id' => $storeId,
            'units' => $units,
        ]);
    }

    private function narration(int $units): string
    {
        $fromName = $this->from->getAttribute('name');
        $name = $this->article->getAttribute('name');
        $toName = $this->to->getAttribute('name');

        $action = tannery($units > 0, 'Received', 'Moved');
        $base = $action . ' ' . abs($units) . ' units of ' . $name;

        return match ($units > 0) {
            false => $base . ' from ' . $fromName . ' to ' . $toName,
            true => $base . ' at ' . $toName . ' from ' . $fromName,
        };
    }

    private function updateBatch(Batch $batch, int $units): void
    {
        $this->createMovement($batch, $units);

        $batch->previous_units = $batch->current_units;
        $batch->current_units += $units;
        $batch->update();
    }

    /**
     * @throws Throwable
     */
    private function validate(): void
    {
        fire(blank($this->movements), 'Movement class not found');
        fire(blank($this->originalUnits), 'No units to move');
        fire(blank($this->article), 'No article to move');
        fire(blank($this->batches), 'No batches to move');
        fire(blank($this->from), 'From store not found');
        fire(blank($this->to), 'To store not found');

        $message = 'From store cannot ship stock';
        fire( ! $this->from->can_ship_stock, $message);

        $check = $this->from->id === $this->to->id;
        fire($check, 'Stores are the same');
    }
}
