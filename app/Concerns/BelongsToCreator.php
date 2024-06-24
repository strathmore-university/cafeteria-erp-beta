<?php

namespace App\Concerns;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToCreator
{
    public static function bootBelongsToCreator(): void
    {
        static::creating(function (Model $model): void {
            $model->setAttribute('created_by', auth_id());
        });
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
