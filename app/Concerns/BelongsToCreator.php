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
            $id = auth()->user()->id ?? system_team()->members->random()->id;
            $model->setAttribute('created_by', $id);
        });
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
