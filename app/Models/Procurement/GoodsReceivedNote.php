<?php

namespace App\Models\Procurement;

use App\Actions\Procurement\Grn\DeleteGrn;
use App\Actions\Procurement\Grn\ExecuteGrnReceipt;
use App\Concerns\BelongsToCreator;
use App\Concerns\BelongsToTeam;
use App\Concerns\HasStatusTransitions;
use App\Models\Inventory\Store;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\LaravelPdf\PdfBuilder;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

use function Spatie\LaravelPdf\Support\pdf;

class GoodsReceivedNote extends Model implements HasMedia
{
    use BelongsToCreator, InteractsWithMedia;
    use BelongsToTeam, HasStatusTransitions;

    // todo: refactor

    protected $guarded = [];

    public function statusTransitions(): array
    {
        return [
            'draft' => 'received',
            'attachments' => 'array',
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

    public function receiver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'received_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(GoodsReceivedNoteItem::class);
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function canBeReceived(): bool
    {
        return $this->purchaseOrder->canBeReceived();
    }

    public function toPDF(): PdfBuilder
    {
        return pdf('pdf.procurement.grn', ['grn' => $this]);
    }

    public function downloadLink(): string
    {
        return route('download.grn', ['grn' => $this->id]);
    }

    public function receive(array $data = []): void
    {
        (new ExecuteGrnReceipt())->execute($this, $data);
    }

    public function canBeDownload(): bool
    {
        $one = $this->getAttribute('status') === 'received';
        $two = $this->purchaseOrder->canBeDownloaded();

        return and_check($one, $two);
    }

    public function deleteGrn(): void
    {
        (new DeleteGrn())->execute($this);
    }

    protected function casts(): array
    {
        return [
            'invoiced_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (GoodsReceivedNote $grn): void {
            $code = generate_code('GRN-', get_next_id($grn));

            $grn->setAttribute('code', $code);
            $grn->setAttribute('status', 'draft');
        });
    }
}
