<?php

namespace App\Models\Core;

use App\Concerns\BelongsToTeam;
use App\Concerns\HasStatusTransitions;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Review extends Model
{
    use BelongsToTeam, HasStatusTransitions;

    protected $guarded = [];

    public function reviewable(): MorphTo
    {
        return $this->morphTo('reviewable');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function statusTransitions(): array
    {
        return [
            'pending' => 'approved',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (Review $review): void {
            $review->setAttribute('status', 'pending');
        });
    }
}
