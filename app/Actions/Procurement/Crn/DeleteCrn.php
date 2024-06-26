<?php

namespace App\Actions\Procurement\Crn;

use App\Filament\Clusters\Procurement\Resources\CreditNoteResource;
use App\Models\Procurement\CreditNote;
use App\Models\Procurement\CreditNoteItem;
use Exception;
use Illuminate\Support\Facades\DB;
use Throwable;

class DeleteCrn
{
    public function execute(CreditNote $grn): void
    {
        try {
            DB::transaction(function () use ($grn): void {
                $message = 'Only draft Credit Notes can be deleted';
                $check = $grn->getAttribute('status') !== 'draft';
                throw_if($check, new Exception($message));

                CreditNoteItem::whereCreditNoteId($grn->id)->delete();
                $grn->delete();

                success();
                redirect(CreditNoteResource::getUrl());
            });
        } catch (Throwable $exception) {
            error_notification($exception);
        }
    }
}
