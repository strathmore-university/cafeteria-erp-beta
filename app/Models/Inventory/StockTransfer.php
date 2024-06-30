<?php

namespace App\Models\Inventory;

use App\Actions\Inventory\ReceiveTransferItems;
use App\Concerns\BelongsToCreator;
use App\Concerns\BelongsToTeam;
use App\Concerns\HasReviews;
use App\Concerns\HasStatusTransitions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use Throwable;

class StockTransfer extends Model
{
    use BelongsToCreator, BelongsToTeam;
    use HasReviews, HasStatusTransitions;

    // todo: refactor

    protected $guarded = [];

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
    public function dispatch(): void
    {
        try {
            DB::transaction(function (): void {
                StockTransferItem::whereStockTransferId($this->id)->update([
                    'status' => 'dispatched',
                ]);

                $this->actioned_at = now();
                $this->updateStatus();
                $this->update();

                success();
            });
        } catch (Throwable $exception) {
            error_notification($exception);
        }
    }

    public function canDispatch(): bool
    {
        $one = filled($this->approved_at);

        return and_check($one, blank($this->actioned_at));
    }

    public function canReceive(): bool
    {
        $one = filled($this->actioned_at);

        return and_check($one, blank($this->received_at));
    }

    public function receive(): void
    {
        (new ReceiveTransferItems())->execute($this);
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
        parent::creating(function (StockTransfer $transfer): void {
            $transfer->setAttribute('status', 'draft');
        });
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
