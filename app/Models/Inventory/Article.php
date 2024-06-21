<?php

namespace App\Models\Inventory;

use App\Concerns\BelongsToTeam;
use App\Concerns\HasCategory;
use App\Concerns\HasStock;
use App\Concerns\UsesNestedSets;
use App\Models\Core\Unit;
use App\Models\Production\Recipe;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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

    protected $guarded = [];

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
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
    public function viableDispatchArticles(float $quantity): Collection
    {
        try {
            $descendants = $this->descendants;

            //        $descendants = $descendants->filter(function ($article) {
            //            return $article->unit_measurement_id !== $this->unit_measurement_id;
            //        });

            $descendants = $descendants->filter(function ($article) use ($quantity) {
                //                $available = article_units($article);
                $available = article_capacity($article);

                $id = $this->getAttribute('unit_id');
                $inReferenceUnit = quantity_converter($article->unit_id, $id, $available);

                return $inReferenceUnit >= $quantity;
            });

            $message = 'No articles with adequate stock to dispatch found!';
            throw_if($descendants->isEmpty(), new Exception($message));

            return $descendants;
        } catch (Throwable $exception) {
            error_notification($exception);
        }

        return collect();
    }

    /**
     * @throws Throwable
     */
    public function unitsToDispatch(int $quantity): int
    {
        $parentUnitId = $this->parent->getAttribute('unit_id');

        $id = $this->getAttribute('unit_id');
        $quantity = quantity_converter($parentUnitId, $id, $quantity);

        return (int) ceil($quantity / ($this->unit_capacity ?? 1));

        //        return match ($id === $parentUnitId) {
        //            false => (int) ceil($quantity / ($this->unit_capacity ?? 1)),
        //            true => ceil($quantity / ($this->unit_capacity ?? 1)),
        //        };
    }

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
                $article->valuation_rate = null;
            }

            //            $check = $article->getAttribute('is_reference') ?? false;
            //            $value = $article->unit_capacity;
            //            $article->unit_capacity = tannery($check, $value, null);
            //
            //            $value = $article->weighted_cost;
            //            $article->weighted_cost = tannery($check, $value, null);
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
