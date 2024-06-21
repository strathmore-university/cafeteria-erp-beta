<?php

namespace App\Models\Inventory;

use App\Concerns\BelongsToTeam;
use App\Concerns\HasCategory;
use App\Concerns\HasIsActiveColumn;
use App\Concerns\HasOwner;
use App\Concerns\HasStock;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Store extends Model
{
    use BelongsToTeam, HasCategory, SoftDeletes;
    use HasIsActiveColumn, HasOwner, HasStock;

    protected $guarded = [];

    public static function booted(): void
    {
        static::creating(function (Store $store): void {
            $one = filled($store->owner_id);
            $two = filled($store->owner_type);

            if (and_check($two, $one)) {
                return;
            }

            $team = system_team();
            $store->owner_type = $team->getMorphClass();
            $store->owner_id = $team->id;
        });
    }

    public function articles(): HasMany
    {
        return $this->hasMany(Article::class);
    }

    public function stockTakes(): HasMany
    {
        return $this->hasMany(StockTake::class);
    }

    public function createStockTake()
    {
        $stockTake = $this->stockTakes()->create([
            'created_by' => auth_id(),
            'store_id' => $this->id,
            'started_at' => now(),
            'status' => 'draft',
        ]);

        $items = collect();
        $levels = StockLevel::where('store_id', $this->id)->get();
        $levels->each(function (StockLevel $level) use ($items): void {
            $items->push([
                'article_id' => $level->getAttribute('article_id'),
                'store_id' => $level->getAttribute('store_id'),
                'current_units' => $level->current_units,
                'actual_units' => $level->current_units,
            ]);
        });
        $stockTake->items()->createMany($items->toArray());

        return $stockTake;
    }

    public function performStockTake(): StockTake
    {
        $stockTake = $this->latestOpenStockTake();

        return match (filled($stockTake)) {
            false => $this->createStockTake(),
            default => $stockTake,
        };
    }

    public function canBeModified(): bool
    {
        $forbiddenFor = ['Station', 'Restaurant'];
        $class = class_basename($this->owner_type);

        return ! in_array($class, $forbiddenFor);
    }

    private function latestOpenStockTake(): ?StockTake
    {
        return StockTake::with('items')
            ->where('store_id', $this->id)
            ->whereNull('concluded_at')
            ->latest()
            ->first();
    }
}
