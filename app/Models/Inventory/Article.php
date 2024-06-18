<?php

namespace App\Models\Inventory;

use App\Concerns\BelongsToTeam;
use App\Concerns\HasCategory;
use App\Concerns\HasStock;
use App\Concerns\UsesNestedSets;
use App\Models\Core\Unit;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kalnoy\Nestedset\NodeTrait;

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

    //    public function recipe(): HasOne
    //    {
    //        return $this->hasOne(Recipe::class);
    //    }
    //
    //    /**
    //     * @throws Throwable
    //     */
    //    public function viableArticlesToDispatch(float $quantity): Collection
    //    {
    //        $descendants = $this->descendants;
    //
    ////        $descendants = $descendants->filter(function ($article) {
    ////            return $article->unit_measurement_id !== $this->unit_measurement_id;
    ////        });
    //
    //        $descendants = $descendants->filter(function ($article) use ($quantity) {
    //            $available = $article->totalStock();
    //
    //            $id = $this->unit_measurement_id;
    //            $inReferenceUnit = $article->unit->convert($id, $available);
    //
    //            return $inReferenceUnit >= $quantity;
    //        });
    //
    //        $message = 'No articles with adequate stock to dispatch found!';
    //        throw_if($descendants->isEmpty(), new Exception($message));
    //
    //        return $descendants;
    //    }
    //
    //    public function unitsToDispatch(int $quantity): int
    //    {
    //        $parent = $this->parent;
    //        $id = $this->unit_measurement_id;
    //        $quantity = $parent->unit->convert($id, $quantity);
    //
    //        return match ($id === $parent->unit_measurement_id) {
    //            false => ceil($quantity / $this->unit_quantity),
    //            true => $quantity,
    //        };
    //    }
    //
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

    public function scopeIsReference(Builder $query): Builder
    {
        return $query->where('is_reference', '=', true);
    }

    public function scopeIsIngredient(Builder $query): Builder
    {
        return $query->where('is_ingredient', '=', true);
    }

    public function scopeIsConsumable(Builder $query): Builder
    {
        return $query->where('is_consumable', '=', true);
    }

    protected static function booted(): void
    {
        parent::creating(function (Article $article): void {
            $check = $article->is_product ?? false;
            $value = $article->reorder_level;
            $article->reorder_level = tannery($check, $value, null);

            $check = $article->getAttribute('is_reference') ?? false;
            $value = $article->unit_capacity;
            $article->unit_capacity = tannery($check, $value, null);

            $value = $article->weighted_cost;
            $article->weighted_cost = tannery($check, $value, null);
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
