<?php

namespace App\Models\Procurement;

use App\Concerns\BelongsToTeam;
use App\Concerns\HasStatusTransitions;
use App\Models\User;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Throwable;

class GoodsReceivedNote extends Model
{
    use BelongsToTeam, HasStatusTransitions;

    protected $guarded = [];

    public function statusTransitions(): array
    {
        return [
            'draft' => 'received',
        ];
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function purchaseOrder(): BelongsTo
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(GoodsReceivedNoteItem::class);
    }

    public function canBeReceived(): bool
    {
        return $this->getAttribute('status') === 'draft';
    }

    public function receive(): void
    {
        try {
            $with = ['article:id,name', 'purchaseOrder:id,code'];
            $items = GoodsReceivedNoteItem::with($with)
                ->where('units', '>', 0)
                ->whereGoodsReceivedNoteId($this->id)
                ->get();

            $message = 'There are no items to be received';
            throw_if( ! $items->count(), new Exception($message));

            $store = $this->purchaseOrder->store;
            $items->each(function (GoodsReceivedNoteItem $item) use ($store): void {
                $item->receive($store);
            });

            $this->received_at = now();
            $this->updateStatus();
            $this->update();

            GoodsReceivedNoteItem::where('units', '<=', 0)
                ->where('goods_received_note_id', '=', $this->id)
                ->delete();

            success('Receipt executed successfully!');
        } catch (Throwable $exception) {
            error_notification($exception);
        }

        redirect(get_record_url($this));
    }

    protected static function booted(): void
    {
        static::creating(function (GoodsReceivedNote $grn): void {
            $code = generate_code('GRN-', get_next_id($grn));

            $grn->setAttribute('code', $code);
            $grn->setAttribute('status', 'draft');
        });
    }

    //    public function postToKfs()
    //    {
    //        // todo: implement
    //    }
}
