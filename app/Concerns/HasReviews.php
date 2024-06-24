<?php

namespace App\Concerns;

use App\Models\Core\Review;
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

    public function requestReview(): void
    {
        try {
            $this->createReview();
            $this->updateStatus();
            $this->update();

            success('Submitted successfully');
        } catch (Throwable $exception) {
            error_notification($exception);
        }
    }

    abstract public function canBeSubmittedForReview(): bool;

    abstract public function approvalAction(): void;

    abstract public function returnAction(): void;

    abstract public function rejectedAction(): void;

    public function createReview(): void
    {
        $this->review()->create(['team_id' => team_id()]);
    }

    /**
     * @throws Throwable
     */
    public function submitReview(array $data): void
    {
        $this->latestPendingReview()->update([
            'comment' => $data['comment'],
            'reviewed_by' => auth_id(),
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
            'rejected' => $this->rejectedAction(),
            'returned' => $this->returnAction(),
        };
    }
}
