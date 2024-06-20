<?php

namespace App\Models\Procurement;

use App\Concerns\BelongsToCreator;
use App\Concerns\BelongsToTeam;
use App\Concerns\HasReviews;
use App\Concerns\HasStatusTransitions;
use App\Models\Inventory\Store;
use App\Models\User;
use Barryvdh\Snappy\PdfWrapper;
use Exception;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
//use PDF;
use Spatie\LaravelPdf\PdfBuilder;
use Throwable;

use function Spatie\LaravelPdf\Support\pdf;

class PurchaseOrder extends Model
{
    use BelongsToCreator, HasStatusTransitions;
    use BelongsToTeam, HasReviews;

    protected $guarded = [];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function goodsReceivedNotes(): HasMany
    {
        return $this->hasMany(GoodsReceivedNote::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function statusTransitions(): array
    {
        return [
            'draft' => 'pending review',
            'pending review' => 'pending fulfilment',
            'pending fulfilment' => 'fulfilled',
        ];
    }

    public function requestReview(): void
    {
        try {
            $this->createReview();
            $this->updateStatus();
            $this->update();

            Notification::make()->title('Submitted successfully')
                ->success()
                ->send();
        } catch (Throwable $exception) {
            Notification::make()->title($exception->getMessage())
                ->persistent()
                ->danger()
                ->send();
        }
    }

    /**
     * @throws Throwable
     */
    public function approvalAction(): void
    {
        $this->expires_at = now()->addDays(14);
        $this->lpo_generated_at = now();
        $this->updateStatus();
        $this->is_lpo = true;
        $this->update();

        // todo: notify creator
        // todo: send LPO to supplier via email
    }

    public function returnAction(): void
    {
        $this->revertStatus();
        $this->update();

        // TODO: send notifications
    }

    public function rejectedAction(): void
    {
        $this->setAttribute('status', 'rejected');
        $this->update();

        // TODO: send notifications
    }

    public function canBeSubmittedForReview(): bool
    {
        $items = $this->items->count();

        return and_check($items > 0, $this->allowEdits());
    }

    //    public function toPDF(): PdfWrapper
    public function toPDF(): PdfBuilder
    {
        //        return PDF::loadView('pdf.purchases.lpo', [
        //            'purchaseOrder' => $this,
        //        ]);

        return pdf('pdf.purchases.lpo', [
            'purchaseOrder' => $this,
        ]);
    }

    public function canBeDownload(): bool
    {
        return match (filled($this->expires_at)) {
            true => $this->canDownload(),
            false => false,
        };
    }

    public function fetchItems(): Collection
    {
        $select = [
            'id', 'article_id', 'ordered_units',
            'price', 'remaining_units',
        ];
        $id = $this->getKey();

        return PurchaseOrderItem::where('purchase_order_id', $id)
            ->with('article')
            ->select($select)
            ->get();
    }

    public function isFulfilled(): bool
    {
        $id = $this->getKey();

        return PurchaseOrderItem::wherePurchaseOrderId($id)
            ->where('remaining_units', '>', 0)
            ->select('remaining_units')
            ->doesntExist();
    }

    public function pendingFulfillment(): bool
    {
        return ! $this->isFulfilled();
    }

    public function remainingItems(): Collection
    {
        $id = $this->getKey();

        $select = [
            'id', 'article_id', 'ordered_units', 'price',
            'remaining_units', 'purchase_order_id',
        ];

        return PurchaseOrderItem::where('purchase_order_id', $id)
            ->where('remaining_units', '>', 0)
            ->with('article')
            ->select($select)
            ->get();
    }

    /**
     * @throws Throwable
     */
    public function fetchOrCreateGrn(): ?GoodsReceivedNote
    {
        try {
            $message = 'Purchase order has already been fulfilled!';
            throw_if($this->isFulfilled(), new Exception($message));

            $message = 'Purchase order is no-longer valid!';
            throw_if( ! $this->canDownload(), new Exception($message));

            $grn = GoodsReceivedNote::wherePurchaseOrderId($this->id)
                ->where('status', '=', 'draft')
                ->first();

            return match (filled($grn)) {
                false => $this->createGrn(),
                true => $grn,
            };
        } catch (Throwable $exception) {
            error_notification($exception);
        }

        return null;
    }

    protected static function booted(): void
    {
        static::creating(function (PurchaseOrder $purchaseOrder): void {
            $id = get_next_id($purchaseOrder);
            $code = generate_code('PO-', $id);

            $purchaseOrder->setAttribute('code', $code);
            $purchaseOrder->setAttribute('status', 'draft');
        });
    }

    private function canDownload(): bool
    {
        $check = $this->getAttribute('status') !== 'fulfilled';
        $valid = now()->isBefore($this->expires_at);
        $check = and_check($valid, $check);

        return and_check($this->hasBeenApproved(), $check);
    }

    /**
     * @throws Throwable
     */
    private function createGrn(): ?GoodsReceivedNote
    {
        $grn = DB::transaction(function () {
            $grn = $this->goodsReceivedNotes()->create([
                'supplier_id' => $this->supplier_id,
                'created_by' => auth_id(),
                'team_id' => team_id(),
            ]);

            $items = collect();
            $remainingItems = $this->remainingItems();
            $remainingItems->each(function (PurchaseOrderItem $item) use ($items): void {
                $items->push([
                    'article_id' => $item->getAttribute('article_id'),
                    'purchase_order_id' => $item->purchase_order_id,
                    'purchase_order_item_id' => $item->id,
                    'units' => $item->remaining_units,
                    'price' => $item->price,
                ]);
            });

            $grn->items()->createMany($items->toArray());

            return $grn;
        });

        return filled($grn) ? $grn : null;
    }
}
