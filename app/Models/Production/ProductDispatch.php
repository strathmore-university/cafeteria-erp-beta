<?php

namespace App\Models\Production;

use App\Actions\Production\ReceiveDispatchedProducts;
use App\Concerns\BelongsToTeam;
use App\Concerns\HasReviews;
use App\Concerns\HasStatusTransitions;
use App\Models\Inventory\Store;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Throwable;

class ProductDispatch extends Model
{
    use BelongsToTeam, HasReviews, HasStatusTransitions;

    public function destination(): MorphTo
    {
        return $this->morphTo('destination');
    }

    public function to(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'to_store_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(ProductDispatchItem::class);
    }

    public function from(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'from_store_id');
    }

    public function dispatcher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'dispatched_by');
    }

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function canBeSubmittedForReview(): bool
    {
        $one = $this->status() === 'draft';

        return and_check($one, $this->items()->exists());
    }

    /**
     * @throws Throwable
     */
    public function approvalAction(): void
    {
        $this->updateStatus();
        $this->update();
    }

    public function canDispatch(): bool
    {
        $one = blank($this->dispatched_at);

        return and_check($one, $this->hasBeenApproved());
    }

    /**
     * @throws Throwable
     */
    public function dispatch(): void
    {
        $this->dispatched_at = now();
        $this->updateStatus();
        $this->update();

        success();
    }

    public function canReceive(): bool
    {
        $one = blank($this->received_at);

        return and_check($one, filled($this->dispatched_at));
    }

    public function receive(): void
    {
        (new ReceiveDispatchedProducts())->execute($this);
    }

    protected function casts(): array
    {
        return [
            'dispatched_at' => 'datetime',
            'received_at' => 'datetime',
        ];
    }

    protected function statusTransitions(): array
    {
        return [
            'draft' => 'pending review',
            'pending review' => 'pending dispatch',
            'pending dispatch' => 'pending receipt',
            'pending receipt' => 'received',
        ];
    }
}
