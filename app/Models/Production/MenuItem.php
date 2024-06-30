<?php

namespace App\Models\Production;

use App\Concerns\BelongsToArticle;
use App\Concerns\BelongsToTeam;
use App\Models\Core\Unit;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Throwable;

class MenuItem extends Model
{
    use BelongsToArticle, BelongsToTeam, SoftDeletes;

    protected $guarded = [];

    public function portions(): HasMany
    {
        return $this->hasMany(SellingPortion::class);
    }

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    /**
     * @throws Throwable
     */
    public function currentStock(): float|int
    {
        $method = 'defaultStore';
        $store = $this->menu->owner->$method();

        return article_units($this->article, $store);
    }

    /**
     * @throws Throwable
     */
    public function isInStock(): bool
    {
        return $this->currentStock() > 0;
    }

    public function station(): BelongsTo
    {
        return $this->belongsTo(Station::class);
    }

    protected static function booted(): void
    {
        parent::created(function (MenuItem $menuItem): void {
            $menuItem->portions()->create([
                'article_id' => $menuItem->getAttribute('article_id'),
                'unit_id' => get_unit_by_name('Full')->id,
                'selling_price' => $menuItem->selling_price,
                'code' => random_int(100, 999),
            ]);
        });
    }
}
