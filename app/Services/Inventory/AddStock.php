<?php

namespace App\Services\Inventory;

use App\Models\Inventory\Article;
use App\Models\Inventory\Batch;
use App\Models\Inventory\Store;
use Illuminate\Database\Eloquent\Model;
use Throwable;

class AddStock
{
    private ?CreateMovements $movements = null;

    private bool $shouldClear = false;

    private ?Article $article = null;
    private ?Model $owner = null;

    private ?int $units = null;
    private ?Store $at = null;
    private ?string $event = null;
    private ?float $valuationRate = null;
    private ?string $code = null;
    private ?string $expiry = null;

    public function units(int $units): self
    {
        $this->units = $units;

        return $this;
    }

    public function event(string $event): self
    {
        $this->event = $event;

        return $this;
    }
    
    public function valuationRate(float $valuationRate): self
    {
        $this->valuationRate = $valuationRate;

        return $this;
    }

    public function owner(Model $owner): self
    {
        $this->owner = $owner;

        return $this;
    }

    public function expiry(?string $expiry= null): self
    {
        $this->expiry = $expiry;

        return $this;
    }

    public function code(?string $code = null): self
    {
        $this->code = $code;

        return $this;
    }

    public function movement(CreateMovements $movements): self
    {
        $this->movements = $movements;

        return $this;
    }

    public function at(Store $at): self
    {
        $this->at = $at;

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

        $this->createMovement($this->createBatch());

        update_stock_level()->units($this->units)
            ->team($this->article->getAttribute('team_id'))
            ->useStore($this->at)->article($this->article->id)
            ->index();

        return match ($this->shouldClear) {
            true => $this->clearAndReturn(),
            false => $this->movements,
        };
    }

    public function clear(): void
    {
        $this->event = $this->expiry = $this->valuationRate = null;
        $this->movements = $this->article = $this->at = null;
        $this->owner = $this->units = $this->code = null;
        $this->shouldClear = false;
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
    private function createBatch(): Batch
    {
        $rate = $this->article->valuation_rate ?? $this->valuationRate;
        
        $batch = Batch::create([
            'team_id' => $this->article->getAttribute('team_id'),
            'article_id' => $this->article->id,
            'narration' => $this->narration(),
            'initial_units' => $this->units,
            'expires_at' => $this->expiry,
            'batch_number' => $this->code,
            'store_id' => $this->at->id,
            'weighted_cost' => $rate,
        ]);

        if (filled($this->owner)) {
            $this->owner->update(['batch_id' => $batch->id]);
        }

        return $batch;
    }

    private function createMovement(Batch $batch): void
    {
        $rate = $this->article->valuation_rate ?? $this->valuationRate;

        $this->movements->add([
            'team_id' => $this->article->getAttribute('team_id'),
            'stock_value' => $this->units * $rate,
            'article_id' => $this->article->id,
            'narration' => $this->narration(),
            'store_id' => $this->at->id,
            'weighted_cost' => $rate,
            'batch_id' => $batch->id,
            'units' => $this->units,
        ]);
    }

    private function narration(): string
    {
        $name = $this->article->getAttribute('name');
        $at = $this->at->getAttribute('name');

        return build_string([
            'Increased stock for article:', $name, 'by ', $this->units,
            'units at ',$at, '. Event', $this->event
        ]);
    }

    /**
     * @throws Throwable
     */
    private function validate(): void
    {
        fire(blank($this->movements), 'Movement class not found');
        fire(blank($this->article), 'No article to increase');
        fire(blank($this->event), 'Event name is required');
        fire(blank($this->units), 'No units to increase');
        fire(blank($this->at), 'store not found');
    }
}
