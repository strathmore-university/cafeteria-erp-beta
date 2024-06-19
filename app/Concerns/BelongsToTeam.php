<?php

namespace App\Concerns;

use App\Models\Core\Team;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait BelongsToTeam
{
    public static function bootBelongsToTeam(): void
    {
        static::creating(function (Model $model): void {
            if (filled($model->getAttribute('team_id'))) {
                return;
            }

            $id = auth()->user()->team_id ?? system_team()->id;
            $model->setAttribute('team_id', $id);
        });

        if ( ! auth()->check()) {
            return;
        }

        // todo: exempt admins

        static::addGlobalScope('team', function (Builder $query): void {
            $query
                ->whereNull('team_id')
                ->orWhere('team_id', '=', auth()->user()->id);
        });
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }
}
