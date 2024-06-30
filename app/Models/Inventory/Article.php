<?php

namespace App\Models\Inventory;

use App\Concerns\BelongsToTeam;
use App\Concerns\HasCategory;
use App\Concerns\HasStock;
use App\Concerns\UsesNestedSets;
use App\Models\Core\Unit;
use App\Models\Procurement\PriceQuote;
use App\Models\Production\Recipe;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Kalnoy\Nestedset\NodeTrait;
use Throwable;

/**
 * @property mixed $descendants
 */
class Article extends Model
{
    use BelongsToTeam, NodeTrait, UsesNestedSets;
    use HasCategory, HasStock, SoftDeletes;

    // todo: refactor

    protected $guarded = [];

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function quotes(): HasMany
    {
        return $this->hasMany(PriceQuote::class);
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function recipe(): HasOne
    {
        return $this->hasOne(Recipe::class);
    }

    /**
     * @throws Throwable
     */
    public function viableDispatchArticles(
        ?Store $store = null
    ): Collection {
        try {
            $children = $this->descendants;
            $articles = $children->filter(function ($article) use ($store) {
                $available = article_capacity($article, $store);
                $id = $this->getAttribute('unit_id');
                $from = $article->unit_id;

                return quantity_converter($from, $id, $available) >= 0;
            });

            $message = 'No articles with adequate stock to dispatch found!';
            throw_if( ! count($articles), new Exception($message));

            return $articles;
        } catch (Throwable $exception) {
            error_notification($exception);
        }

        return collect();
    }

    /**
     * @throws Throwable
     */
    public function unitsToDispatch(
        int $requiredCapacity,
        ?Store $store = null,
        ?int $parentUnitId = null
    ): int {
        $parentUnitId = $parentUnitId ?? $this->parent->getAttribute('unit_id');
        $id = $this->getAttribute('unit_id');

        $capacity = article_capacity($this, $store);
        $capacity = quantity_converter($id, $parentUnitId, $capacity);

        if ($capacity < $requiredCapacity) {
            return article_units($this, $store);
        }

        $capacity = quantity_converter($parentUnitId, $id, $requiredCapacity);

        return (int) ceil($capacity / ($this->unit_capacity ?? 1));
    }

    // todo: lorem ipsum

    public function scopeCanBeOrdered(Builder $query): Builder
    {
        return $query
            ->where('is_reference', '=', false)
            ->where(function (Builder $query): void {
                $query
                    ->where('is_ingredient', true)
                    ->orWhere('is_consumable', true);
            });
    }

    public function scopeCanBeSold(Builder $query): Builder
    {
        return $query
            ->where('is_reference', '=', false)
            ->where(function (Builder $query): void {
                $query
                    ->where('is_product', true)
                    ->orWhere('is_consumable', true);
            });
    }

    public function scopeIsIngredient(Builder $query): Builder
    {
        return $query
            ->where('is_ingredient', '=', true)
            ->where('is_reference', '=', false);
    }

    public function scopeIsConsumable(Builder $query): Builder
    {
        return $query
            ->where('is_consumable', '=', true)
            ->where('is_reference', '=', false);
    }

    public function scopeIsProduct(Builder $query): Builder
    {
        return $query->where('is_product', '=', true)
            ->where('is_reference', '=', false);
    }

    protected static function booted(): void
    {
        parent::creating(function (Article $article): void {
            $check = $article->is_product ?? false;
            $value = $article->reorder_level;
            $article->reorder_level = tannery($check, $value, null);

            if ($article->getAttribute('is_reference')) {
                $article->unit_capacity = null;
            }
        });
    }

    protected function casts(): array
    {
        return [
            'is_reference' => 'boolean',
        ];
    }

    protected function getTypeAttribute(): string
    {
        $type = null;
        $type = tannery($this->is_product, 'Product', $type);
        $type = tannery($this->is_consumable, 'Consumable', $type);

        return tannery($this->is_ingredient, 'Ingredient', $type);
    }
}
