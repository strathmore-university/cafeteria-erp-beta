<?php

namespace App\Models\Procurement;

use App\Actions\Procurement\DeleteCrn;
use App\Actions\Procurement\IssueCrn;
use App\Concerns\BelongsToTeam;
use App\Concerns\HasStatusTransitions;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\LaravelPdf\PdfBuilder;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

use function Spatie\LaravelPdf\Support\pdf;

class CreditNote extends Model implements HasMedia
{
    use BelongsToTeam, HasStatusTransitions;
    use InteractsWithMedia;

    protected $guarded = [];

    public function statusTransitions(): array
    {
        return [
            'draft' => 'issued',
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
        return $this->hasMany(CreditNoteItem::class);
    }

    public function toPDF(): PdfBuilder
    {
        return pdf('pdf.procurement.crn', ['crn' => $this]);
    }

    public function downloadLink(): string
    {
        return route('download.crn', ['crn' => $this->id]);
    }

    public function canBeIssued(): bool
    {
        $one = $this->purchaseOrder->canGeneratedCrn();

        return and_check($one, $this->allowEdits());
    }

    public function deleteCrn(): void
    {
        (new DeleteCrn())->execute($this);
    }

    public function issueCrn(): void
    {
        (new IssueCrn())->execute($this);
    }

    protected static function booted(): void
    {
        static::creating(function (CreditNote $grn): void {
            $code = generate_code('CRN-', get_next_id($grn));

            $grn->setAttribute('code', $code);
            $grn->setAttribute('status', 'draft');
        });
    }

    protected function casts(): array
    {
        return [
            'issued_at' => 'datetime',
        ];
    }
}
