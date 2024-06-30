<?php

namespace App\Models\Procurement;

use App\Actions\Procurement\Crn\GenerateCrn;
use App\Actions\Procurement\Grn\FetchOrCreateGrn;
use App\Concerns\BelongsToCreator;
use App\Concerns\BelongsToTeam;
use App\Concerns\HasReviews;
use App\Concerns\HasStatusTransitions;
use App\Models\Inventory\Store;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Collection;
use Spatie\LaravelPdf\PdfBuilder;
use Throwable;

use function Spatie\LaravelPdf\Support\pdf;

class PurchaseOrder extends Model
{
    use BelongsToCreator, HasStatusTransitions;
    use BelongsToTeam, HasReviews;

    // todo: refactor

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

    /**
     * @throws Throwable
     */
    public function approvalAction(): void
    {
        // todo: add to procurement settings
        $this->expires_at = now()->addDays(14);
        $this->lpo_generated_at = now();
        $this->updateStatus();
        $this->is_lpo = true;
        $this->update();

        // todo: notify creator
        // todo: send LPO to supplier via email
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

    public function isFulfilled(): bool
    {
        return $this->status() === 'fulfilled';
    }

    public function pendingFulfillment(): bool
    {
        return ! $this->isFulfilled();
    }

    public function remainingItems(): Collection
    {
        $select = [
            'id', 'article_id', 'ordered_units', 'price',
            'remaining_units', 'purchase_order_id',
        ];

        return PurchaseOrderItem::wherePurchaseOrderId($this->getKey())
            ->where('remaining_units', '>', 0)
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
        $notExpired = ! $this->isExpired();

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

    /**
     * @throws Throwable
     */
    public function generateCrn(): CreditNote
    {
        return (new GenerateCrn())->execute($this);
    }

    public function canGeneratedCrn(): bool
    {
        $one = or_check($this->isExpired(), $this->isValidLPO());

        return and_check($one, $this->pendingFulfillment());
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
}
