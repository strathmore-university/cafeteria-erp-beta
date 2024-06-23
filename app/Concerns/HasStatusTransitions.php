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
        $column = 'status';

        $condition = ! array_key_exists($this->$column, $statusMap);
        $message = 'Invalid status transition from: ' . $this->$column;
        throw_if($condition, new Exception($message));

        $this->$column = $statusMap[$this->$column];
    }

    public function revertStatus(): void
    {
        $statusMap = $this->statusTransitions();
        $column = 'status';

        $currentStatus = $this->getAttribute($column);
        $key = array_search($currentStatus, $statusMap);
        $this->setAttribute($column, $key);
    }

    public function allowEdits(): bool
    {
        return $this->getAttribute('status') === 'draft';
    }

    public function preventEdit(): bool
    {
        return ! $this->allowEdits();
    }

    public function canBeReviewed(): bool
    {
        return $this->getAttribute('status') === 'pending review';
    }

    public function hasBeenApproved(): bool
    {
        $invalidStatus = ['draft', 'pending review', 'rejected'];
        $status = $this->getAttribute('status');

        return ! in_array($status, $invalidStatus);
    }

    abstract protected function statusTransitions(): array;
}
