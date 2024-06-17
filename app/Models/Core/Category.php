<?php

namespace App\Models\Core;

use App\Concerns\UsesNestedSets;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Kalnoy\Nestedset\NodeTrait;

/**
 * @property mixed $descendants
 */
class Category extends Model
{
    use NodeTrait, SoftDeletes, UsesNestedSets;

    protected $guarded = [];
}
