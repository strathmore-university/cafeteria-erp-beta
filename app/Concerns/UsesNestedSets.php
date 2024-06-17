<?php

namespace App\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait UsesNestedSets
{
    public function scopeIsReference(Builder $query): Builder
    {
        return $query->where('is_reference', '=', true);
    }

    public function scopeIsDescendant(Builder $query): Builder
    {
        return $query->where('is_reference', '=', false);
    }
}
