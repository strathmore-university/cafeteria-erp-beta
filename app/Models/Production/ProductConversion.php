<?php

namespace App\Models\Production;

use App\Concerns\BelongsToCreator;
use App\Concerns\BelongsToTeam;
use App\Models\Inventory\Article;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductConversion extends Model
{
    use BelongsToCreator, BelongsToTeam;

    protected $guarded = [];

    public function station(): BelongsTo
    {
        return $this->belongsTo(Station::class);
    }

    public function from(): BelongsTo
    {
        return $this->belongsTo(Article::class, 'from_id');
    }

    public function to(): BelongsTo
    {
        return $this->belongsTo(Article::class, 'to_id');
    }
}
