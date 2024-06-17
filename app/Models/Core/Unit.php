<?php

namespace App\Models\Core;

use App\Concerns\UsesNestedSets;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\HigherOrderCollectionProxy;
use Kalnoy\Nestedset\NodeTrait;

/**
 * @property HigherOrderCollectionProxy|mixed $descendants
 */
class Unit extends Model
{
    use NodeTrait, SoftDeletes, SoftDeletes, UsesNestedSets;

    protected $guarded = [];

    public function from(): HasMany
    {
        return $this->hasMany(UnitConversion::class, 'from_unit_id');
    }

    public function to(): HasMany
    {
        return $this->hasMany(UnitConversion::class, 'to_unit_id');
    }
}
