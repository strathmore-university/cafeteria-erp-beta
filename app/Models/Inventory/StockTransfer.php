<?php

namespace App\Models\Inventory;

use App\Concerns\BelongsToCreator;
use App\Concerns\BelongsToTeam;
use App\Concerns\HasReviews;
use App\Concerns\HasStatusTransitions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Throwable;

class StockTransfer extends Model
{
    use BelongsToTeam, BelongsToCreator;
    use HasStatusTransitions, HasReviews;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'approved_at' => 'datetime',
            'actioned_at' => 'datetime',
            'received_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        parent::creating(function (StockTransfer $transfer) {
            $transfer->setAttribute('status', 'draft');
        });
    }

    public function items(): HasMany
    {
        return $this->hasMany(StockTransferItem::class);
    }

    public function from(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'from_store_id');
    }

    public function to(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'to_store_id');
    }

    public function canBeSubmittedForReview(): bool
    {
        $one = $this->status() === 'draft';
        $two = $this->items()->exists();

        return and_check($one, $two);
    }

    /**
     * @throws Throwable
     */
    public function approvalAction(): void
    {
        $this->approved_at = now();
        $this->updateStatus();
        $this->update();
    }

    public function returnAction(): void
    {

    }

    public function rejectedAction(): void
    {

    }

    protected function statusTransitions(): array
    {
        return [
            'draft' => 'pending review',
            'pending review' => 'pending action',
            'pending action' => 'pending receipt',
            'pending receipt' => 'received',
        ];
    }
}
