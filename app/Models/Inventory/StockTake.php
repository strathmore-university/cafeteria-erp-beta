<?php

namespace App\Models\Inventory;

use App\Concerns\BelongsToCreator;
use App\Concerns\BelongsToTeam;
use App\Concerns\HasStatusTransitions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Throwable;

/**
 * @method preventEdit()
 */
class StockTake extends Model
{
    use BelongsToCreator, BelongsToTeam;
    use HasStatusTransitions;

    protected $guarded = [];

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(StockTakeItem::class);
    }

    public function statusTransitions(): array
    {
        return [
            'draft' => 'completed',
        ];
    }

    public function adjustStock(): void
    {
        try {
            $items = StockTakeItem::with(['article', 'store'])
                ->whereStockTakeId($this->id)
                ->get();

            $items->each(function (StockTakeItem $item): void {
                $item->adjustStock();
            });

            $this->concluded_at = now();
            $this->updateStatus();
            $this->update();

            success();
        } catch (Throwable $exception) {
            error_notification($exception);
        }

        redirect(get_record_url($this));
    }

    protected static function booted(): void
    {
        parent::creating(function (StockTake $stockTake): void {
            $default = 'Stock take for ' . now()->format('Y-m-d');
            $value = $stockTake->getAttribute('description');
            $stockTake->setAttribute('description', $value ? $value : $default);
        });
    }

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'concluded_at' => 'datetime',
        ];
    }
}
