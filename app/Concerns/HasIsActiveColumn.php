<?php

namespace App\Concerns;

use Illuminate\Database\Eloquent\Builder;

trait HasIsActiveColumn
{
    public function scopeIsActive(Builder $query): Builder
    {
        return $query->where('is_active', '=', true);
    }
}
