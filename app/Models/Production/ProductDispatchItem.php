<?php

namespace App\Models\Production;

use App\Concerns\BelongsToArticle;
use App\Concerns\BelongsToTeam;
use App\Concerns\HasStatusTransitions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductDispatchItem extends Model
{
    use BelongsToArticle, BelongsToTeam, HasStatusTransitions;

    protected $guarded = [];

    public function dispatch(): BelongsTo
    {
        return $this->belongsTo(ProductDispatch::class);
    }

    protected function statusTransitions(): array
    {
        return [
            'draft' => 'dispatched',
            'dispatched' => 'received',
        ];
    }
}
