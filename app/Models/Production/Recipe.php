<?php

namespace App\Models\Production;

use App\Concerns\HasCategory;
use App\Models\Inventory\Article;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Recipe extends Model
{
    use HasCategory, HasFactory, SoftDeletes;

    protected $guarded = [];

    public function product(): BelongsTo
    {
        return $this->belongsTo(
            Article::class, 'article_id'
        );
    }

    public function ingredients(): HasMany
    {
        return $this->hasMany(Ingredient::class);
    }
}
