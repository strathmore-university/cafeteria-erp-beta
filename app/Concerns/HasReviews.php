<?php

namespace App\Concerns;

use App\Models\Core\Review;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Throwable;

trait HasReviews
{
    public function review(): MorphMany
    {
        return $this->morphMany(Review::class, 'reviewable');
    }

    public function latestPendingReview(): Review
    {
        return Review::where('reviewable_type', $this->getMorphClass())
            ->where('status', 'pending')
            ->where('reviewable_id', $this->id)
            ->latest()
            ->first();
    }

    public function latestApprovedReview(): Review
    {
        return Review::where('reviewable_type', $this->getMorphClass())
            ->where('status', 'approved')
            ->where('reviewable_id', $this->id)
            ->latest()
            ->first();
    }

    abstract public function canBeSubmittedForReview(): bool;

    abstract public function requestReview(): void;

    abstract public function approvalAction(): void;

    abstract public function returnAction(): void;

    abstract public function rejectedAction(): void;

    public function createReview(): void
    {
        $this->review()->create([
            'team_id' => auth()->user()->team_id ?? system_team()->id, // todo: make this a helper function
        ]);
    }

    /**
     * @throws Throwable
     */
    public function submitReview(array $data): void
    {
        $this->latestPendingReview()->update([
            'comment' => $data['comment'],
            'reviewed_by' => auth()->id() ?? User::first()->id, // todo: make system user
            'status' => $data['status'],
            'reviewed_at' => now(),
        ]);

        $this->afterReview($data['status']);
    }

    /**
     * @throws Throwable
     */
    public function afterReview(string $status): void
    {
        match ($status) {
            'approved' => $this->approvalAction(),
            'returned' => $this->returnAction(),
            'rejected' => $this->rejectedAction(),
        };
    }
}
