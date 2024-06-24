<?php

namespace App\Concerns;

use Exception;
use Throwable;

trait HasStatusTransitions
{
    /**
     * @throws Throwable
     */
    public function updateStatus(): void
    {
        $statusMap = $this->statusTransitions();
        $status = $this->status();

        $condition = ! array_key_exists($status, $statusMap);
        $message = 'Invalid status transition from: ' . $status;
        throw_if($condition, new Exception($message));

        $this->setAttribute('status', $statusMap[$status]);
    }

    public function revertStatus(): void
    {
        $statusMap = $this->statusTransitions();
        $status = $this->status();
        $key = array_search($status, $statusMap);
        $this->setAttribute('status', $key);
    }

    public function allowEdits(): bool
    {
        return $this->status() === 'draft';
    }

    public function status(): string
    {
        return $this->status();
    }

    public function preventEdit(): bool
    {
        return ! $this->allowEdits();
    }

    public function canBeReviewed(): bool
    {
        return $this->status() === 'pending review';
    }

    public function hasBeenApproved(): bool
    {
        $invalidStatus = ['draft', 'pending review', 'rejected'];

        return ! in_array($this->status(), $invalidStatus);
    }

    abstract protected function statusTransitions(): array;
}
