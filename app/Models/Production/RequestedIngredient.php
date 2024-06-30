<?php

namespace App\Models\Production;

use App\Concerns\BelongsToArticle;
use App\Models\Core\Unit;
use App\Models\Inventory\Article;
use App\Models\Inventory\Batch;
use App\Models\Inventory\Store;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Throwable;

class RequestedIngredient extends Model
{
    use BelongsToArticle;

    protected $guarded = [];

    public function ingredient(): BelongsTo
    {
        return $this->belongsTo(Ingredient::class);
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function dispatchedIngredients(): HasMany
    {
        return $this->hasMany(DispatchedIngredient::class);
    }

    /**
     * @throws Throwable
     */
    public function viableArticles(): array
    {
        $article = $this->article;
        $store = Store::select('id')->find(1); // todo: remove hardcoding
        $viable = $article->viableDispatchArticles($store);

        return $viable->pluck('name', 'id')->toArray();
    }

    public function isFulfilled(): bool
    {
        return $this->remaining_quantity < 1;
    }

    /**
     * @throws Throwable
     */
    protected function getCapacityAtStationAttribute(): string
    {
        $id = $this->article_id;
        $reference = Article::with('descendants')->find($id);
        $store = Store::select('id')->find($this->store_id);

        $ids = $reference->descendants->pluck('id')->toArray();
        $batches = Batch::where('store_id', $store->id)
            ->where('owner_id', null)
            ->whereIn('article_id', $ids)
            ->exists();

        $id = $reference->getAttribute('unit_id');
        $unit = Unit::select('name')->find($id);

        $capacity = match ($batches) {
            true => article_capacity($reference, $store),
            false => 0
        };

        $name = $unit->getAttribute('name');
        $unitName = Str::plural($name, $capacity);

        return number_format($capacity) . ' ' . $unitName;
    }
}
