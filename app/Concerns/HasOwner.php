<?php

namespace App\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

trait HasOwner
{
    public function owner(): MorphTo
    {
        return $this->morphTo('owner');
    }

    public function isOwner(Model $model): bool
    {
        $type = $this->getAttribute('owner_type') ?? null;
        $identicalClass = $type === $model->getMorphClass();

        $id = $this->getAttribute('owner_id') ?? null;
        $identicalId = $id === $model->getKey();

        return and_Check($identicalClass, $identicalId);
    }
}
