<?php

namespace App\Models\Procurement;

use App\Actions\Procurement\FetchOrCreateGrn;
use App\Actions\Procurement\GenerateCrn;
use App\Concerns\BelongsToCreator;
use App\Concerns\BelongsToTeam;
use App\Concerns\HasReviews;
use App\Concerns\HasStatusTransitions;
use App\Models\Inventory\Store;
use App\Models\User;
use Filament\Notifications\Notification;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Spatie\LaravelPdf\PdfBuilder;
use function Spatie\LaravelPdf\Support\pdf;
use Throwable;

//use PDF;

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

    public function creditNotes(): HasMany
    {
        return $this->hasMany(CreditNote::class);
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

    public function downloadLink(): string
    {
        return route('download.purchase-order', [
            'purchaseOrder' => $this->id,
        ]);
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
        $this->expires_at = now()->addDays(14); // todo: add to procurement settings
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
        //        return PDF::loadView('pdf.procurement.lpo', [
        //            'purchaseOrder' => $this,
        //        ]);

        return pdf('pdf.procurement.lpo', [
            'purchaseOrder' => $this,
        ]);
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
    public function fetchGrn(): ?GoodsReceivedNote
    {
        return (new FetchOrCreateGrn())->execute($this);
    }

    public function isExpired(): bool
    {
        return now()->isAfter($this->expires_at ?? now());
    }

    public function isValidLPO(): bool
    {
        $notExpired = !$this->isExpired();

        return and_check($this->hasBeenApproved(), $notExpired);
    }

    public function canBeReceived(): bool
    {
        $one = $this->pendingFulfillment();

        return and_check($this->isValidLPO(), $one);
    }

    public function canBeDownloaded(): bool
    {
        return $this->hasBeenApproved();
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

    /**
     * @throws Throwable
     */
    public function generateCrn(): CreditNote
    {
        return (new GenerateCrn())->execute($this);
    }

    public function canGeneratedCrn():bool
    {
        $one = or_check($this->isExpired(), $this->isValidLPO());

        return and_check($one, $this->pendingFulfillment());
    }
}
