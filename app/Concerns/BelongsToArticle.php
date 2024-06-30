<?php

namespace App\Concerns;

use App\Models\Inventory\Article;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToArticle
{
    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }
}
