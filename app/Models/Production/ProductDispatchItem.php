<?php

namespace App\Models\Production;

use App\Concerns\BelongsToTeam;
use App\Concerns\HasStatusTransitions;
use App\Models\Inventory\Article;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductDispatchItem extends Model
{
    use BelongsToTeam, HasStatusTransitions;

    public function article(): BelongsTo
    {
        return $this->belongsTo(Article::class);
    }

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
